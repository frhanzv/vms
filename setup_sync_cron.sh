#!/usr/bin/env bash
# =============================================================================
# setup_sync_cron.sh
# Installs cron jobs to run VMS sync at 00:00 and 12:00 every day.
# Run once on the Jetson: bash setup_sync_cron.sh
# =============================================================================

# --- Adjust these if your paths differ ---
PHP_BIN="/usr/bin/php"
VMS_DIR="/var/www/vms"
LOG_FILE="${VMS_DIR}/writable/logs/sync.log"

CRON_MIDNIGHT="0 0 * * * ${PHP_BIN} ${VMS_DIR}/spark sync:run --quiet >> ${LOG_FILE} 2>&1"
CRON_NOON="0 12 * * * ${PHP_BIN} ${VMS_DIR}/spark sync:run --quiet >> ${LOG_FILE} 2>&1"

MARKER="# VMS sync"

# Read current crontab (ignore error if empty)
current=$(crontab -l 2>/dev/null | grep -v "${MARKER}")

# Build new crontab
new_crontab="${current}
${MARKER} - midnight
${CRON_MIDNIGHT}
${MARKER} - noon
${CRON_NOON}"

echo "$new_crontab" | crontab -

echo "Cron jobs installed:"
crontab -l | grep -A1 "${MARKER}"
echo ""
echo "To run sync manually:"
echo "  ${PHP_BIN} ${VMS_DIR}/spark sync:run"
echo ""
echo "To view sync logs:"
echo "  tail -f ${LOG_FILE}"
