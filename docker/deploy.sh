#!/usr/bin/env bash
# Deploy / refresh SafeG VMS stack (app + MySQL + phpMyAdmin).
#
# First time on server:
#   cp .env docker/.env          # then set hostname=db, baseURL, password
#   cp docker/compose.env.example docker/compose.env
#   # DB_PASSWORD must match database.default.password
#   # VMS_IMAGE=bytespace.jfrog.io/vms/vms:v1.0.1
#   SKIP_BUILD=1 ./docker/deploy.sh
#
# New Artifactory tag later (DB stays):
#   VMS_IMAGE=bytespace.jfrog.io/vms/vms:v1.0.2 ./docker/upgrade-app.sh

set -euo pipefail
cd "$(dirname "$0")/.."

APP_PORT="${APP_PORT:-80}"
PHPMYADMIN_PORT="${PHPMYADMIN_PORT:-8082}"
DB_PORT="${DB_PORT:-3307}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
RUN_SEEDERS="${RUN_SEEDERS:-true}"
VMS_IMAGE="${VMS_IMAGE:-localhost/vms:v1.0.1}"
MYSQL_IMAGE="${MYSQL_IMAGE:-docker.io/library/mysql:8.0.37-oraclelinux8}"

env_get() {
  local file="$1" key="$2" default="${3:-}"
  local val
  val="$(grep -E "^[[:space:]]*${key}[[:space:]]*=" "$file" 2>/dev/null | tail -n1 | sed -E "s/^[^=]+=[[:space:]]*//; s/^['\"]//; s/['\"]$//; s/[[:space:]]*$//" || true)"
  if [[ -z "$val" ]]; then
    printf '%s' "$default"
  else
    printf '%s' "$val"
  fi
}

normalize_app_env() {
  local f=docker/.env
  # Force compose DNS hostname (in_pod: false)
  if grep -qE '^[[:space:]]*database\.default\.hostname[[:space:]]*=' "$f"; then
    sed -i 's/^[[:space:]]*database\.default\.hostname[[:space:]]*=.*/database.default.hostname = db/' "$f"
  else
    echo 'database.default.hostname = db' >> "$f"
  fi
  # Uncomment / set CI_ENVIRONMENT
  if grep -qE '^[[:space:]]*#[[:space:]]*CI_ENVIRONMENT[[:space:]]*=' "$f"; then
    sed -i 's/^[[:space:]]*#[[:space:]]*CI_ENVIRONMENT[[:space:]]*=.*/CI_ENVIRONMENT = production/' "$f"
  elif ! grep -qE '^[[:space:]]*CI_ENVIRONMENT[[:space:]]*=' "$f"; then
    echo 'CI_ENVIRONMENT = production' >> "$f"
  fi
  # Warn on Laragon-style baseURL with /vms/
  if grep -qE "app\.baseURL.*=.*'/vms" "$f"; then
    echo "WARN: app.baseURL contains /vms — container serves at site root. Use https://HOST/ not .../vms/"
  fi
  if grep -qE '^[[:space:]]*database\.default\.hostname[[:space:]]*=[[:space:]]*(localhost|127\.0\.0\.1)' "$f"; then
    echo "WARN: hostname still localhost — normalizing to db"
    sed -i 's/^[[:space:]]*database\.default\.hostname[[:space:]]*=.*/database.default.hostname = db/' "$f"
  fi
}

if [[ ! -f docker/.env ]]; then
  if [[ -f .env ]]; then
    echo "Creating docker/.env from project .env ..."
    cp .env docker/.env
  else
    echo "ERROR: docker/.env missing. Copy your CI4 .env:"
    echo "  cp .env docker/.env"
    echo "  # set database.default.hostname = db"
    echo "  # set app.baseURL = 'https://your-host/'"
    echo "  # set database.default.password (must match compose.env DB_PASSWORD)"
    exit 1
  fi
fi

normalize_app_env

# Prefer passwords already in docker/.env / existing compose.env over defaults
DB_DATABASE="${DB_DATABASE:-$(env_get docker/.env database.default.database vms)}"
DB_USERNAME="${DB_USERNAME:-$(env_get docker/.env database.default.username vms)}"
DB_PASSWORD="${DB_PASSWORD:-$(env_get docker/.env database.default.password vms)}"
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-vmsroot}"

if [[ -f docker/compose.env ]]; then
  # Keep root password from existing compose.env if present
  existing_root="$(grep -E '^[[:space:]]*MYSQL_ROOT_PASSWORD[[:space:]]*=' docker/compose.env | tail -n1 | cut -d= -f2- || true)"
  [[ -n "${existing_root:-}" ]] && MYSQL_ROOT_PASSWORD="$existing_root"
  existing_img="$(grep -E '^[[:space:]]*VMS_IMAGE[[:space:]]*=' docker/compose.env | tail -n1 | cut -d= -f2- || true)"
  if [[ -z "${VMS_IMAGE_SET:-}" && -n "${existing_img:-}" && "${VMS_IMAGE}" == "localhost/vms:v1.0.1" ]]; then
    VMS_IMAGE="$existing_img"
  fi
fi

# Sync app .env password ← compose password source of truth for bootstrap:
# write compose.env FROM docker/.env password so they cannot drift
COMPOSE_ENV="docker/compose.env"
cat > "$COMPOSE_ENV" <<EOF
APP_PORT=${APP_PORT}
PHPMYADMIN_PORT=${PHPMYADMIN_PORT}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
RUN_MIGRATIONS=${RUN_MIGRATIONS}
RUN_SEEDERS=${RUN_SEEDERS}
VMS_IMAGE=${VMS_IMAGE}
MYSQL_IMAGE=${MYSQL_IMAGE}
EOF

echo "Synced docker/compose.env DB_* from docker/.env (password match enforced)."
echo "VMS_IMAGE=${VMS_IMAGE}"

# Clear stale podman-compose pod leftovers (common on server)
podman rm -f vms-app vms-db vms-phpmyadmin 2>/dev/null || true
podman pod rm -f pod_vms 2>/dev/null || true

if [[ "${SKIP_BUILD:-0}" != "1" ]]; then
  if [[ ! -f vendor/autoload.php ]]; then
    echo "Running composer install --no-dev ..."
    composer install --no-dev --optimize-autoloader --no-interaction
  fi
  echo "Building image ${VMS_IMAGE} ..."
  podman build --network=host --format=docker -t "${VMS_IMAGE}" -t localhost/vms:latest -f Containerfile .
else
  echo "SKIP_BUILD=1 — pulling ${VMS_IMAGE} (if remote) ..."
  podman pull "${VMS_IMAGE}" 2>/dev/null || echo "Pull skipped/failed (image may already be local)."
fi

echo "Starting stack (app :${APP_PORT}, phpMyAdmin :${PHPMYADMIN_PORT}, db :${DB_PORT}) ..."
# Prefer explicit --in-pod=false when CLI supports it
if podman compose --help 2>&1 | grep -q 'in-pod'; then
  podman compose --in-pod=false --env-file "$COMPOSE_ENV" up -d
else
  podman compose --env-file "$COMPOSE_ENV" up -d
fi

BASE_URL="$(env_get docker/.env app.baseURL "")"
echo
echo "App env:     docker/.env (CI4)"
echo "App URL:     ${BASE_URL:-http://SERVER:${APP_PORT}}"
echo "phpMyAdmin:  http://SERVER_IP:${PHPMYADMIN_PORT}"
echo "             (Tailscale: serve --set-path /phpmyadmin http://127.0.0.1:${PHPMYADMIN_PORT})"
echo "DB:          localhost:${DB_PORT}  (${DB_USERNAME}/${DB_DATABASE})"
echo "Logs:        podman compose --env-file ${COMPOSE_ENV} logs -f app"
echo
echo "Upgrade app image later:  VMS_IMAGE=bytespace.jfrog.io/vms/vms:vX.Y.Z ./docker/upgrade-app.sh"
