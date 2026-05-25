#!/usr/bin/env bash
# =============================================================================
# setup_sync_cron.sh — run on BOTH Jetson and cloud for symmetric sync
# Installs:
#   - Every 5 minutes: incremental sync (safety net)
#   - 00:00 and 12:00: scheduled backup
# Direct sync after saves is handled by SyncAutoFilter (SYNC.auto in .env)
# =============================================================================

PHP_BIN="/usr/bin/php"
VMS_DIR="/var/www/html/vms"
LOG_FILE="${VMS_DIR}/writable/logs/sync.log"
POLL_MINUTES="${SYNC_POLL_MINUTES:-5}"

CRON_POLL="*/${POLL_MINUTES} * * * * ${PHP_BIN} ${VMS_DIR}/spark sync:run --quiet >> ${LOG_FILE} 2>&1"
CRON_MIDNIGHT="0 0 * * * ${PHP_BIN} ${VMS_DIR}/spark sync:run --quiet >> ${LOG_FILE} 2>&1"
CRON_NOON="0 12 * * * ${PHP_BIN} ${VMS_DIR}/spark sync:run --quiet >> ${LOG_FILE} 2>&1"

MARKER="# VMS sync"

current=$(crontab -l 2>/dev/null | grep -v "${MARKER}")

new_crontab="${current}
${MARKER} - poll every ${POLL_MINUTES} min
${CRON_POLL}
${MARKER} - midnight backup
${CRON_MIDNIGHT}
${MARKER} - noon backup
${CRON_NOON}"

echo "$new_crontab" | crontab -

echo "Cron jobs installed:"
crontab -l | grep -A1 "${MARKER}"
echo ""
echo "Direct sync (both directions):"
echo "  - Each server pushes to peer DB ~15s after local saves (SYNC.auto=true)"
echo "  - Jetson: database.cloud -> cloud MySQL"
echo "  - Cloud:  database.cloud -> Jetson MySQL  OR  SYNC.notifyUrl webhook"
echo "  - Cron every ${POLL_MINUTES} min is a safety net only"
echo ""
echo "Manual: ${PHP_BIN} ${VMS_DIR}/spark sync:run"
echo "Logs:   tail -f ${LOG_FILE}"
