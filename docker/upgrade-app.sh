#!/usr/bin/env bash
# Switch / upgrade ONLY the app image (MySQL data + phpMyAdmin stay).
#
#   VMS_IMAGE=bytespace.jfrog.io/vms/vms:v1.0.2 ./docker/upgrade-app.sh
#
# Or edit VMS_IMAGE in docker/compose.env then:
#   ./docker/upgrade-app.sh

set -euo pipefail
cd "$(dirname "$0")/.."

COMPOSE_ENV="docker/compose.env"
if [[ ! -f "$COMPOSE_ENV" ]]; then
  echo "ERROR: ${COMPOSE_ENV} missing — run ./docker/deploy.sh first."
  exit 1
fi

if [[ ! -f docker/.env ]]; then
  echo "ERROR: docker/.env missing."
  exit 1
fi

# Keep hostname=db after any manual edit
sed -i 's/^[[:space:]]*database\.default\.hostname[[:space:]]*=.*/database.default.hostname = db/' docker/.env

if [[ -n "${VMS_IMAGE:-}" ]]; then
  if grep -qE '^[[:space:]]*VMS_IMAGE=' "$COMPOSE_ENV"; then
    sed -i "s|^[[:space:]]*VMS_IMAGE=.*|VMS_IMAGE=${VMS_IMAGE}|" "$COMPOSE_ENV"
  else
    echo "VMS_IMAGE=${VMS_IMAGE}" >> "$COMPOSE_ENV"
  fi
fi

VMS_IMAGE="$(grep -E '^[[:space:]]*VMS_IMAGE=' "$COMPOSE_ENV" | tail -n1 | cut -d= -f2-)"
echo "Upgrading app to: ${VMS_IMAGE}"

podman pull "${VMS_IMAGE}" || true

# Recreate app only — do not touch db volume
podman rm -f vms-app 2>/dev/null || true

if podman compose --help 2>&1 | grep -q 'in-pod'; then
  podman compose --in-pod=false --env-file "$COMPOSE_ENV" up -d app
else
  podman compose --env-file "$COMPOSE_ENV" up -d app
fi

# If compose tried to recreate the whole pod and failed, start app against existing network
if ! podman ps --format '{{.Names}}' | grep -qx vms-app; then
  echo "compose up app failed to leave vms-app running — trying replace run on existing network..."
  net="$(podman inspect vms-db --format '{{range $k,$v := .NetworkSettings.Networks}}{{$k}}{{end}}' 2>/dev/null || true)"
  if [[ -z "$net" ]]; then
    echo "ERROR: vms-db not running. Start full stack: ./docker/deploy.sh"
    exit 1
  fi
  podman run -d --replace --name vms-app \
    --network "$net" \
    -p "${APP_PORT:-80}:80" \
    -e RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}" \
    -e RUN_SEEDERS="${RUN_SEEDERS:-true}" \
    -v "$PWD/docker/.env:/var/www/html/.env:ro" \
    -v vms_vms-writable:/var/www/html/writable \
    -v vms_vms-uploads:/var/www/html/public/uploads \
    "${VMS_IMAGE}"
fi

sleep 5
podman ps --filter name=vms
echo
podman logs --tail 40 vms-app
echo
echo "Check: podman exec vms-app curl -s -o /dev/null -w 'login %{http_code}\\n' http://127.0.0.1/login"
