# SafeG VMS — Jetson Installation Guide

Complete setup for the on-site Jetson edge device. Assumes the **cloud server is already running**.

---

## Jetson role

| Item | Value |
|---|---|
| Purpose | On-site edge device (kiosk, RFID, local DB) |
| Local DB | `vms` |
| Syncs to | Cloud `vms_cloud` via `database.cloud.*` in `.env` |
| Code path | `/var/www/html/vms` |
| Linux user | `tally` |
| URL | `http://JETSON_IP/vms/login` |

---

## 1. System packages

```bash
sudo apt update && sudo apt upgrade -y

sudo apt install -y \
  apache2 libapache2-mod-php \
  php php-cli php-mysql php-mbstring php-xml php-curl php-intl php-gd php-zip php-bcmath \
  mysql-server \
  git unzip composer \
  tesseract-ocr

sudo a2enmod rewrite headers
sudo systemctl enable apache2 mysql
sudo systemctl restart apache2
```

### Verify Apache PHP has intl (required)

```bash
ls /etc/apache2/mods-enabled/ | grep php
```

If you see `php8.2` (common on Jetson/Ubuntu):

```bash
sudo apt install -y php8.2-intl
sudo systemctl restart apache2
```

Test:

```bash
echo '<?php echo PHP_VERSION." intl=".(extension_loaded("intl")?"yes":"NO");' | sudo tee /var/www/html/vms/public/_chk.php
curl -s http://127.0.0.1/vms/_chk.php
sudo rm /var/www/html/vms/public/_chk.php
```

Must show `intl=yes`. Without intl you get: `Class "Locale" not found`.

---

## 2. MySQL (local on Jetson)

```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```

```sql
CREATE DATABASE vms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'vms_user'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON vms.* TO 'vms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Clone code (do NOT use sudo)

```bash
cd /var/www/html
git clone https://github.com/frhanzv/vms.git
cd vms
composer install --no-dev --optimize-autoloader
```

If repo already exists with messy local changes:

```bash
cd /var/www/html/vms
cp .env ~/.env.backup
git fetch origin main
git reset --hard origin/main
git clean -fd
cp ~/.env.backup .env
composer install --no-dev --optimize-autoloader
```

Fix ownership:

```bash
sudo chown -R tally:tally /var/www/html/vms
```

If Git says "dubious ownership":

```bash
sudo chown -R tally:tally /var/www/html/vms
```

---

## 4. Configure `.env`

```bash
cp env .env
nano .env
```

Jetson settings:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'http://JETSON_IP/vms/'

# Local DB (Jetson)
database.default.hostname = localhost
database.default.database = vms
database.default.username = vms_user
database.default.password = YOUR_PASSWORD
database.default.DBDriver = MySQLi
database.default.port = 3306

# Cloud DB (for sync to server)
database.cloud.hostname = CLOUD_SERVER_IP
database.cloud.database = vms_cloud
database.cloud.username = vms_sync
database.cloud.password = YOUR_CLOUD_SYNC_PASSWORD
database.cloud.DBDriver = MySQLi
database.cloud.port = 3306

encryption.key = hex2bin:GENERATE_NEW_KEY
```

Generate encryption key:

```bash
php -r "echo 'hex2bin:'.bin2hex(random_bytes(32)).PHP_EOL;"
```

Set mail/LLM vars if needed.

---

## 5. Permissions

```bash
cd /var/www/html/vms

sudo mkdir -p writable/{cache,logs,session,uploads,debugbar}
sudo mkdir -p public/uploads/visitor_photos writable/sync

sudo chown -R tally:www-data writable
sudo chmod -R 775 writable

sudo usermod -aG www-data tally
```

Log out and back in to SSH after `usermod`.

Test write:

```bash
touch writable/cache/test.txt && rm writable/cache/test.txt && echo "OK"
```

**Rule:** repo owned by `tally`; `writable/` owned by `tally:www-data` with `775`.

---

## 6. Apache vhost

```bash
sudo nano /etc/apache2/sites-available/vms.conf
```

```apache
<VirtualHost *:80>
    ServerName JETSON_IP

    Alias /vms /var/www/html/vms/public
    <Directory /var/www/html/vms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/vms-error.log
    CustomLog ${APACHE_LOG_DIR}/vms-access.log combined
</VirtualHost>
```

Enable:

```bash
sudo a2ensite vms.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

Keep `RewriteBase /vms` in `public/.htaccess` (default — do not change).

Open: `http://JETSON_IP/vms/login`

---

## 7. Migrate & seed

```bash
cd /var/www/html/vms
php spark migrate
php spark db:seed DatabaseSeeder
```

If `CountriesSeeder` fails (MySQL FK error on `states`), run the rest manually:

```bash
php spark db:seed VisitorTypeSeeder
php spark db:seed WorkflowSeeder
php spark db:seed ClientFeaturesSeeder
php spark db:seed ClientFormFieldsSeeder
php spark db:seed ClientNotificationSettingsSeeder
```

If cache permission error:

```bash
sudo -u www-data php spark migrate
```

### Demo login (change on production)

| Username | Password | Role |
|---|---|---|
| admin | admin123 | superadmin |
| host | host123 | host |
| officer | officer123 | officer |
| approver | approver123 | admin |

---

## 8. Sync with cloud (first time)

Ensure cloud allows Jetson to reach MySQL on port 3306 (cloud firewall + MySQL user `vms_sync`).

On Jetson:

```bash
cd /var/www/html/vms
php spark sync:run --full
```

Normal sync after that:

```bash
php spark sync:run
```

Optional cron (every 5 minutes):

```bash
crontab -e
```

```
*/5 * * * * cd /var/www/html/vms && php spark sync:run --quiet >> /var/www/html/vms/writable/logs/sync-cron.log 2>&1
```

---

## 9. Verification checklist

| Check | How |
|---|---|
| Login page | `http://JETSON_IP/vms/login` |
| Demo login | `admin` / `admin123` |
| Migrations | `php spark migrate:status` |
| Apache intl | curl test shows `intl=yes` |
| Writable OK | `touch writable/cache/test` works |
| Logs | `tail writable/logs/log-*.log` |
| Sync | `php spark sync:run` |

---

## 10. After every git pull on Jetson

```bash
cd /var/www/html/vms
cp .env ~/.env.backup
git pull origin main
cp ~/.env.backup .env

composer install --no-dev --optimize-autoloader
php spark migrate

sudo chown -R tally:www-data writable
sudo chmod -R 775 writable
rm -rf writable/cache/*
sudo systemctl restart apache2
```

If pull blocked by local changes:

```bash
cp .env ~/.env.backup
git fetch origin main
git reset --hard origin/main
git clean -fd
cp ~/.env.backup .env
```

**Never use `sudo git pull`.**

---

## Common Jetson errors

| Error | Fix |
|---|---|
| `Cache unable to write to writable/cache/` | `sudo chown -R tally:www-data writable && sudo chmod -R 775 writable` |
| `Class "Locale" not found` | `sudo apt install php8.2-intl` (match Apache PHP version) |
| Session permission denied | Same writable fix above |
| `git dubious ownership` | `sudo chown -R tally:tally /var/www/html/vms` |
| `git pull` blocked | Backup `.env`, then `git reset --hard origin/main` |
| Broken URLs / redirects | `app.baseURL = 'http://JETSON_IP/vms/'` + keep `RewriteBase /vms` |

---

## Quick order (fresh Jetson install)

1. Install packages (+ **php-intl** for Apache's PHP version)
2. Create local MySQL `vms` + `vms_user`
3. Clone repo + `composer install`
4. Configure `.env` (local DB + cloud sync DB)
5. Set permissions (`tally:www-data` on `writable/`)
6. Apache vhost with `Alias /vms`
7. `php spark migrate` + seed
8. `php spark sync:run --full`
9. Open `http://JETSON_IP/vms/login`

---

*SafeG VMS — Jetson Installation Guide*
