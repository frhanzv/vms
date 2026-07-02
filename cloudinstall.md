# SafeG VMS — Cloud Installation Guide (Debian)

## 1. Update the system

```bash
sudo apt update && sudo apt upgrade -y
```

## 2. Install PHP 8.1+ and required extensions

```bash
sudo apt install -y php php-cli php-fpm php-mysql php-mbstring php-xml php-curl php-intl php-gd php-zip php-bcmath unzip git
```

Verify PHP version (must be 8.1+):

```bash
php -v
```

If your Debian version ships PHP < 8.1 (e.g. Debian 11/Bullseye), add the Sury PPA first:

```bash
sudo apt install -y lsb-release ca-certificates apt-transport-https software-properties-common
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-intl php8.3-gd php8.3-zip php8.3-bcmath
```

## 3. Install Apache and enable modules

```bash
sudo apt install -y apache2 libapache2-mod-php
sudo a2enmod rewrite headers
sudo systemctl restart apache2
```

## 4. Install MySQL

VMS uses CodeIgniter’s **MySQLi** driver (`DBDriver = MySQLi` in `.env`). That works with **Oracle MySQL** and **MariaDB** — same SQL, same PHP extension (`php-mysql`).

Use **Oracle MySQL** if you want the official MySQL server (matches Laragon’s “MySQL” on Windows).

### Option A — Oracle MySQL 8 (recommended)

```bash
# Download the MySQL APT repo package (check https://dev.mysql.com/downloads/repo/apt/ for the latest version)
wget https://dev.mysql.com/get/mysql-apt-config_0.8.29-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.29-1_all.deb
sudo apt update
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

During install, set a **root password** when prompted.

Create the database and user:

```bash
sudo mysql
```

```sql
CREATE DATABASE vms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'vms_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON vms.* TO 'vms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Verify MySQL is running:

```bash
sudo systemctl status mysql
mysql --version
```

### Option B — MariaDB (Debian default, also works)

On Debian, `apt install mariadb-server` is a MySQL-compatible drop-in. Use this only if you prefer the simpler default package:

```bash
sudo apt install -y mariadb-server
sudo mysql_secure_installation
```

Then run the same `CREATE DATABASE` / `CREATE USER` SQL as above (use `sudo mysql -u root -p` or `sudo mysql` if root has no password yet).

## 5. Install phpMyAdmin (optional)

Use phpMyAdmin to manage the `vms_cloud` database through the browser.

```bash
sudo apt install -y phpmyadmin
```

During the installer:

1. **Web server** — select **apache2** (Space to select, Enter to confirm)
2. **Configure database for phpmyadmin** — choose **Yes**
3. Set a **phpMyAdmin application password** when prompted (this is for phpMyAdmin’s internal DB user, not `vms_user`)

If the installer did not enable Apache integration automatically:

```bash
sudo a2enconf phpmyadmin
sudo systemctl reload apache2
```

### Add phpMyAdmin to the VMS vhost (required)

When VMS is the only site enabled, CodeIgniter catches every URL — including `/phpmyadmin` — and returns a 404. Add an **Alias** in `vms.conf` so Apache serves phpMyAdmin before CI routing:

```bash
sudo nano /etc/apache2/sites-available/vms.conf
```

Add this block **above** the VMS `DocumentRoot` or `Alias /vms` line:

```apache
    # phpMyAdmin — must be in vhost, outside CodeIgniter
    Alias /phpmyadmin /usr/share/phpmyadmin
    <Directory /usr/share/phpmyadmin>
        Options SymLinksIfOwnerMatch
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>
```

Reload Apache:

```bash
sudo systemctl reload apache2
```

Open in your browser:

```text
http://your-server-ip/phpmyadmin
```

Log in with the VMS database user:

- **Username:** `vms_user`
- **Password:** the password you set in step 4

Or use the MariaDB `root` account if needed for admin tasks.

### Restrict phpMyAdmin access (recommended)

Do not leave phpMyAdmin open to the whole internet on a production server. Restrict by IP in Apache:

```bash
sudo nano /etc/apache2/conf-available/phpmyadmin.conf
```

Add inside the `<Directory /usr/share/phpmyadmin>` block (replace with your admin IP):

```apache
Require ip YOUR_ADMIN_IP
```

Then reload:

```bash
sudo systemctl reload apache2
```

Alternatively, remove phpMyAdmin when you no longer need it:

```bash
sudo apt remove --purge phpmyadmin
sudo systemctl reload apache2
```

## 6. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 7. Deploy the application code

```bash
cd /var/www
sudo git clone <your-repo-url> vms
sudo chown -R www-data:www-data /var/www/vms
cd /var/www/vms
```

Install PHP dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

## 8. Configure the `.env` file

```bash
cp env .env
nano .env
```

Set these values (uncomment and edit):

```ini
CI_ENVIRONMENT = production

app.baseURL = 'http://34.57.166.51/'

database.default.hostname = localhost
database.default.database = vms_cloud
database.default.username = vms_user
database.default.password = Vms@2026!
database.default.DBDriver = MySQLi
database.default.port = 3306

encryption.key = hex2bin:bca83600034ebe9e64915e054f7aa904e1f2eb13739f07c1c076fb9ae3f13e90
```

Generate the encryption key:

```bash
php -r "echo 'hex2bin:'.bin2hex(random_bytes(32)).PHP_EOL;"
```

Copy the output into `encryption.key`.

If you use the LLM assistant feature, also set:

```ini
LLM_ENABLED = true
LLM_BASE_URL = https://api.openai.com/v1
LLM_API_KEY = sk-...
LLM_MODEL = gpt-4o
```

## 9. Set directory permissions

```bash
sudo chown -R www-data:www-data /var/www/html/vms/writable
sudo chmod -R 775 /var/www/html/vms/writable
sudo mkdir -p /var/www/html/vms/public/uploads/visitor_photos
sudo chown -R www-data:www-data /var/www/html/vms/public/uploads
sudo chmod -R 775 /var/www/html/vms/public/uploads
rm -rf /var/www/html/vms/writable/cache/*
```

After every `git pull` on cloud/Jetson, clear `writable/cache/` and restart Apache so PHP OPcache serves the latest code:

```bash
cd /var/www/html/vms
git pull
composer install --no-dev --optimize-autoloader
php spark migrate
rm -rf writable/cache/*
sudo systemctl restart apache2
```

## 10. Run database migrations and seed initial data

```bash
cd /var/www/html/vms
php spark migrate
php spark db:seed DatabaseSeeder
```

## 11. Configure Apache virtual host

Create a new site config:

```bash
sudo nano /etc/apache2/sites-available/vms.conf
```

### Option A — Serve at the domain root (recommended)

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/vms/public

    <Directory /var/www/html/vms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/vms-error.log
    CustomLog ${APACHE_LOG_DIR}/vms-access.log combined
</VirtualHost>
```

If serving at the domain root, update `public/.htaccess` and `app/Config/App.php`:

- In `public/.htaccess`, change `RewriteBase /vms` to `RewriteBase /`
- In `app/Config/App.php` line 25, change `'/vms/'` to `'/'`

```bash
nano /var/www/html/vms/public/.htaccess
```

Change:

```apache
RewriteBase /vms
```

To:

```apache
RewriteBase /
```

### Option B — Serve under a `/vms` subfolder

Keep `RewriteBase /vms` as-is in `public/.htaccess`.

```apache
<VirtualHost *:80>
    ServerName yourdomain.com

    Alias /vms /var/www/vms/public
    <Directory /var/www/vms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/vms-error.log
    CustomLog ${APACHE_LOG_DIR}/vms-access.log combined
</VirtualHost>
```

Enable the site and disable the default:

```bash
sudo a2ensite vms.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

## 12. Set up HTTPS with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

Certbot will auto-configure the SSL virtual host and set up auto-renewal.

## 13. (Optional) Install Tesseract OCR

The project depends on `thiagoalessio/tesseract_ocr`, so if you use OCR features:

```bash
sudo apt install -y tesseract-ocr
```

## 14. Firewall setup

```bash
sudo apt install -y ufw
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw reload
sudo ufw status
```

If `ufw app list` shows `Apache Full`, you can use `sudo ufw allow 'Apache Full'` instead of the individual port rules.

---

## Post-deployment checklist

| Item | Command / Check |
|---|---|
| App loads | Visit `https://yourdomain.com` |
| `.env` is production | `CI_ENVIRONMENT = production` |
| Writable directory is writable | `ls -la /var/www/html/vms/writable/` (owned by `www-data`) |
| Migrations applied | `php spark migrate:status` |
| phpMyAdmin (if installed) | Visit `http://your-server-ip/phpmyadmin` |
| HTTPS working | Browser shows padlock |
| Logs accessible | `tail -f /var/www/html/vms/writable/logs/log-*.log` |
| Error pages (not stack traces) | Verify `CI_ENVIRONMENT = production` hides debug info |

## Troubleshooting

- **500 errors** — Check `writable/logs/` and `/var/log/apache2/vms-error.log`
- **Permission denied on writable/** — Re-run `chown -R www-data:www-data writable && chmod -R 775 writable`
- **Config / kiosk toggles not saving on cloud** — Deploy latest code, then `rm -rf writable/cache/*` and `sudo systemctl restart apache2`. Verify with `grep saveGlobalSetting app/Controllers/Config.php` on the server.
- **mod_rewrite not working** — Ensure `AllowOverride All` is set and `a2enmod rewrite` was run
- **Session issues** — The default file-based session handler writes to `writable/session/` — make sure it's writable by `www-data`
- **phpMyAdmin shows CodeIgniter 404** — Add the `Alias /phpmyadmin` block to `vms.conf` (see step 5), then `sudo systemctl reload apache2`
