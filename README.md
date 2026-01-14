# Visitor Management System (VMS)

A modern Visitor Management System built with CodeIgniter 4 framework for tracking and managing visitors efficiently.

## Features

- User Authentication (Login/Register)
- Dashboard for visitor statistics
- Visitor invitation
- Visitor logbook management
- Visitor list tracking
- Visitor security briefing
- Facial Verification
- Secure session management

## Requirements

- [Laragon](https://laragon.org/) (includes PHP 8.1+, Apache, MySQL)
- Composer (included in Laragon)
- PHP Extensions (already configured in Laragon):
  - intl
  - mbstring
  - mysqli/pdo_mysql
  - json
  - xml

## Installation Guide (Laragon)

### Step 1: Setup Project in Laragon

1. Ensure Laragon is installed and running
2. Clone or copy this project to Laragon's www directory:

```bash
cd C:\laragon\www
git clone <repository-url> vms
```

Or simply extract/copy the project folder to `C:\laragon\www\vms`

### Step 2: Install Dependencies

1. Open Laragon
2. Right-click on Laragon → Terminal
3. Navigate to project and install dependencies:

```bash
cd vms
composer install
```

### Step 3: Environment Configuration

1. Copy the `env` file to `.env`:

```bash
copy env .env
```

2. Open `.env` file and configure for Laragon:

```ini
# Application Settings
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/vms/'

# Database Configuration (Laragon defaults)
database.default.hostname = localhost
database.default.database = vms
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306

# Security
encryption.key = hex2bin:your-32-char-hex-key-here
```

### Step 4: Generate Encryption Key

Generate a secure 32-character hex key:

1. Visit an online generator or use this PHP command:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

2. Copy the generated key and add it to `.env`:

```ini
encryption.key = hex2bin:your_generated_key_here
```

### Step 5: Database Setup

1. Open HeidiSQL from Laragon (Right-click Laragon → HeidiSQL)
2. Create a new database:
   - Right-click on server → Create new → Database
   - Name: `vms`
   - Collation: `utf8mb4_unicode_ci`

3. Import your SQL file if you have one, or create tables manually

4. If you have migration files, you can run them via terminal:

```bash
php vendor/codeigniter4/framework/spark migrate
```

### Step 6: Set File Permissions

In Laragon, the `writable` directory should already have proper permissions. If you encounter permission issues:

1. Right-click on `writable` folder → Properties → Security
2. Edit permissions to allow full control for your user

Or run in terminal as Administrator:

```bash
icacls writable /grant Everyone:F /T
```

### Step 7: Configure Apache for Laragon

**Important:** Configure Apache to point to the `public` folder instead of project root.

1. Open Apache configuration:
   - Laragon → Apache → httpd.conf

2. Find the `DocumentRoot` section and verify it points to Laragon's www directory:

```apache
DocumentRoot "C:/laragon/www"
<Directory "C:/laragon/www">
    AllowOverride All
    Require all granted
</Directory>
```

3. Create an `.htaccess` file in `C:/laragon/www/vms/` (project root, not public) with:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

This redirects all requests to the `public` folder.

4. Restart Laragon (Stop All → Start All)

5. Access the application at: `http://localhost/vms`

## Project Structure

```
vms/
├── app/
│   ├── Config/         # Configuration files
│   ├── Controllers/    # Application controllers
│   │   ├── Auth.php
│   │   ├── Dashboard.php
│   │   ├── VisitorList.php
│   │   └── VisitorLogbook.php
│   ├── Models/         # Database models
│   ├── Views/          # View templates
│   ├── Filters/        # Request filters
│   └── Database/       # Migrations and seeds
├── public/             # Public web root (point your server here)
│   ├── index.php
│   └── assets/         # CSS, JS, images
├── writable/           # Writable directory (logs, cache, sessions)
├── tests/              # Unit and integration tests
└── vendor/             # Composer dependencies
```

## Usage

### Default Access

1. Ensure Laragon is running (Start All)
2. Open browser and navigate to: `http://localhost/vms`
3. Register a new account or login with existing credentials
4. Access the dashboard to manage visitors

### Running Tests

Open Laragon Terminal and execute:

```bash
cd C:\laragon\www\vms
vendor\bin\phpunit
```

## Common Issues & Troubleshooting (Laragon)

### Issue: "404 Page Not Found" or Homepage loads but links don't work

**Solution:** 
- Verify your virtual host points to the `public` folder, not project root
- Check `.htaccess` file exists in `public/` folder
- Ensure `mod_rewrite` is enabled in Apache (Laragon has it by default)

### Issue: Database connection failed

**Solution:** 
- Verify database credentials in `.env` file
- Ensure MySQL is running in Laragon (check status indicator)
- Verify database `vms` exists in HeidiSQL
- Default Laragon MySQL password is empty

### Issue: "Writable directory not writable"

**Solution:** 
- Check file permissions on `writable` folder
- Run Laragon as Administrator
- Manually set permissions (see Step 6)

### Issue: Virtual host not working

**Solution:**
- Check if virtual host is properly configured in Apache
- Restart Laragon completely
- Verify hosts file has entry (Laragon usually manages this)
- Clear browser cache

### Issue: Session not working

**Solution:** 
- Check `writable/session/` directory exists and is writable
- Verify session configuration in `app/Config/App.php`
- Ensure Laragon has write permissions

## Development

### Running in Development Mode

Ensure your `.env` file has:

```ini
CI_ENVIRONMENT = development
```

This enables:
- Detailed error messages
- Debug toolbar
- Query logging

### Working with Laragon

- **Start/Stop Services:** Right-click Laragon tray icon
- **Access Database:** Right-click Laragon → HeidiSQL
- **Terminal Access:** Right-click Laragon → Terminal (Cmder)
- **Quick Switch PHP Version:** Laragon → PHP → Select version
- **View Logs:** Check `writable/logs/` folder

## Security Considerations

- Never commit `.env` file to version control
- Change default encryption key
- Use strong passwords for database users
- Keep CodeIgniter framework updated
- Enable CSRF protection in production
- Use HTTPS in production environment

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Built With

- [CodeIgniter 4](https://codeigniter.com/) - PHP Framework
- [Composer](https://getcomposer.org/) - Dependency Management

## Support

For issues and questions:
- Create an issue in the repository
- Check [CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)
- Visit [CodeIgniter Forum](https://forum.codeigniter.com/)

## Acknowledgments

- CodeIgniter 4 framework and community
- All contributors to this project

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
