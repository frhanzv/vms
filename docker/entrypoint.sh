#!/bin/bash
set -euo pipefail

cd /var/www/html

# Ensure writable dirs exist (volumes may replace them)
mkdir -p \
  writable/cache \
  writable/debugbar \
  writable/logs \
  writable/session \
  writable/uploads \
  public/uploads \
  public/files

chown -R www-data:www-data writable public/uploads public/files 2>/dev/null || true
chmod -R ug+rwx writable public/uploads public/files 2>/dev/null || true

ENV_FILE=".env"
if [ ! -f "$ENV_FILE" ]; then
  echo "ERROR: .env missing. Mount CI4 env at /var/www/html/.env (see docker/.env.example)."
  exit 1
fi

# Read dotted CI4 keys from .env (do not regenerate / overwrite the file)
env_get() {
  local key="$1" default="${2:-}"
  local val
  val="$(grep -E "^[[:space:]]*${key}[[:space:]]*=" "$ENV_FILE" | tail -n1 | sed -E "s/^[^=]+=[[:space:]]*//; s/^['\"]//; s/['\"]$//; s/[[:space:]]*$//" || true)"
  if [ -z "$val" ]; then
    printf '%s' "$default"
  else
    printf '%s' "$val"
  fi
}

DB_HOST="$(env_get 'database.default.hostname' 'db')"
DB_PORT="$(env_get 'database.default.port' '3306')"
DB_DATABASE="$(env_get 'database.default.database' 'vms')"
DB_USERNAME="$(env_get 'database.default.username' 'vms')"
DB_PASSWORD="$(env_get 'database.default.password' '')"

# Export for PHP (avoids shell-quoting bugs with passwords like Vms@2026)
export VMS_DB_HOST="$DB_HOST"
export VMS_DB_PORT="$DB_PORT"
export VMS_DB_NAME="$DB_DATABASE"
export VMS_DB_USER="$DB_USERNAME"
export VMS_DB_PASS="$DB_PASSWORD"

if [ "$DB_HOST" = "localhost" ] || [ "$DB_HOST" = "127.0.0.1" ]; then
  echo "WARN: database.default.hostname=${DB_HOST}"
  echo "      With compose.yml x-podman.in_pod=false, use hostname=db (service name)."
  echo "      Will also try fallbacks: db, 127.0.0.1"
fi

php_pdo_ok() {
  local host="$1"
  VMS_DB_HOST="$host" php -r '
    $host = getenv("VMS_DB_HOST");
    $port = getenv("VMS_DB_PORT") ?: "3306";
    $db   = getenv("VMS_DB_NAME") ?: "vms";
    $user = getenv("VMS_DB_USER") ?: "vms";
    $pass = getenv("VMS_DB_PASS");
    try {
      new PDO(
        "mysql:host={$host};port={$port};dbname={$db}",
        $user,
        $pass,
        [PDO::ATTR_TIMEOUT => 2, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
      );
      exit(0);
    } catch (Throwable $e) {
      fwrite(STDERR, $e->getMessage() . PHP_EOL);
      exit(1);
    }
  ' 2>/tmp/vms-pdo.err
}

db_ready=0
ACTIVE_HOST=""
CANDIDATES=("$DB_HOST")
# Fallbacks for both bridge DNS (db) and shared-pod localhost
for h in db 127.0.0.1; do
  if [ "$h" != "$DB_HOST" ]; then
    CANDIDATES+=("$h")
  fi
done

echo "Waiting for MySQL (from .env host=${DB_HOST}:${DB_PORT}, user=${DB_USERNAME}, db=${DB_DATABASE})..."
for i in $(seq 1 90); do
  for host in "${CANDIDATES[@]}"; do
    if php_pdo_ok "$host"; then
      echo "MySQL is ready at ${host}:${DB_PORT}."
      db_ready=1
      ACTIVE_HOST="$host"
      export VMS_DB_HOST="$host"
      break 2
    fi
  done
  if [ $((i % 15)) -eq 0 ]; then
    echo "  still waiting (${i}s)... last error: $(tr '\n' ' ' </tmp/vms-pdo.err 2>/dev/null || true)"
  fi
  sleep 1
done

if [ "$db_ready" -ne 1 ]; then
  echo "ERROR: MySQL not reachable after 90s."
  echo "  Check: database.default.hostname=db in docker/.env"
  echo "  Check: DB_PASSWORD in docker/compose.env MATCHES database.default.password"
  echo "  Check: compose.yml has x-podman.in_pod: false (so hostname db resolves)"
  echo "  Last PDO error: $(cat /tmp/vms-pdo.err 2>/dev/null || echo n/a)"
  echo "Starting Apache anyway (app will Whoops until DB works)."
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ] && [ "$db_ready" -eq 1 ]; then
  echo "Running migrations..."
  php spark migrate --all || php spark migrate || true
fi

if [ "${RUN_SEEDERS:-true}" = "true" ] && [ "$db_ready" -eq 1 ]; then
  user_count="$(php -r '
    try {
      $pdo = new PDO(
        sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("VMS_DB_HOST"), getenv("VMS_DB_PORT"), getenv("VMS_DB_NAME")),
        getenv("VMS_DB_USER"),
        getenv("VMS_DB_PASS")
      );
      echo (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    } catch (Throwable $e) {
      echo 0;
    }
  ' 2>/dev/null || echo 0)"
  if [ "${user_count}" = "0" ]; then
    echo "No users found — seeding demo accounts (UserSeeder)..."
    php spark db:seed UserSeeder || true
  fi
fi

exec "$@"
