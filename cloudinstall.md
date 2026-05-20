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

## 4. Install MariaDB (or MySQL)

```bash
sudo apt install -y mariadb-server
sudo mysql_secure_installation
```

Create the database and user:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE vms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'vms_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON vms.* TO 'vms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 6. Deploy the application code

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

## 7. Configure the `.env` file

```bash
cp env .env
nano .env
```

Set these values (uncomment and edit):

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://yourdomain.com/'

database.default.hostname = localhost
database.default.database = vms
database.default.username = vms_user
database.default.password = YOUR_STRONG_PASSWORD
database.default.DBDriver = MySQLi
database.default.port = 3306

encryption.key = hex2bin:GENERATED_KEY_HERE
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

## 8. Set directory permissions

```bash
sudo chown -R www-data:www-data /var/www/vms/writable
sudo chmod -R 775 /var/www/vms/writable
```

## 9. Run database migrations

```bash
cd /var/www/vms
php spark migrate
```

## 10. Configure Apache virtual host

Create a new site config:

```bash
sudo nano /etc/apache2/sites-available/vms.conf
```

### Option A — Serve at the domain root (recommended)

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/vms/public

    <Directory /var/www/vms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/vms-error.log
    CustomLog ${APACHE_LOG_DIR}/vms-access.log combined
</VirtualHost>
```

If serving at the domain root, update `public/.htaccess` — change `RewriteBase /vms` to `RewriteBase /`:

```bash
nano /var/www/vms/public/.htaccess
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

## 11. Set up HTTPS with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

Certbot will auto-configure the SSL virtual host and set up auto-renewal.

## 12. (Optional) Install Tesseract OCR

The project depends on `thiagoalessio/tesseract_ocr`, so if you use OCR features:

```bash
sudo apt install -y tesseract-ocr
```

## 13. Firewall setup

```bash
sudo apt install -y ufw
sudo ufw allow OpenSSH
sudo ufw allow 'Apache Full'
sudo ufw enable
```

---

## Post-deployment checklist

| Item | Command / Check |
|---|---|
| App loads | Visit `https://yourdomain.com` |
| `.env` is production | `CI_ENVIRONMENT = production` |
| Writable directory is writable | `ls -la /var/www/vms/writable/` (owned by `www-data`) |
| Migrations applied | `php spark migrate:status` |
| HTTPS working | Browser shows padlock |
| Logs accessible | `tail -f /var/www/vms/writable/logs/log-*.log` |
| Error pages (not stack traces) | Verify `CI_ENVIRONMENT = production` hides debug info |

## Troubleshooting

- **500 errors** — Check `writable/logs/` and `/var/log/apache2/vms-error.log`
- **Permission denied on writable/** — Re-run `chown -R www-data:www-data writable && chmod -R 775 writable`
- **mod_rewrite not working** — Ensure `AllowOverride All` is set and `a2enmod rewrite` was run
- **Session issues** — The default file-based session handler writes to `writable/session/` — make sure it's writable by `www-data`
