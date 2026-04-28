# Pila Pet Registration System - PHP Version

This is a PHP conversion of the Flask-based Pila Pet Registration System. It provides a complete pet registration and management system with user authentication, admin dashboard, and various pet-related features.

## Features

- **User Authentication**: Login, registration, and session management
- **Pet Registration**: Register pets with photos and detailed information
- **User Dashboard**: Manage personal pets, view medical records
- **Admin Dashboard**: Approve registrations, manage users and pets
- **Lost & Found**: Report and search for lost pets
- **Adoption System**: Find pets available for adoption
- **Medical Records**: Track vaccinations and health records
- **Responsive UI**: Bootstrap-based modern interface

## Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP built-in server
- PDO PHP extension
- File upload permissions

## Installation

1. **Install PHP and MySQL** (if not already installed)

2. **Database Setup**:
   - Import the `database.sql` file into MySQL
   - Update database credentials in `config/database.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'pila_pets');
     define('DB_USER', 'your_mysql_user');
     define('DB_PASS', 'your_mysql_password');
     ```

3. **File Permissions**:
   - Create `uploads/` directory
   - Set write permissions: `chmod 755 uploads/`

4. **Web Server Configuration**:
   - Point your web server to the `php_version/` directory
   - Or use PHP built-in server: `php -S localhost:8000`

## Directory Structure

```
php_version/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php             # Authentication functions
│   ├── header.php           # HTML header template
│   └── footer.php           # HTML footer template
├── user/
│   ├── dashboard.php        # User dashboard
│   └── register_pet.php     # Pet registration
├── admin/
│   ├── dashboard.php        # Admin dashboard
│   └── manage_pets.php      # Pet management
├── uploads/                 # File uploads directory
├── index.php               # Home page
├── login.php              # Login page
├── register.php           # Registration page
├── logout.php             # Logout script
└── .htaccess             # Apache configuration
```

## Usage

### Admin Access
- **Email**: admin@pila.pets
- **Password**: admin123!

### User Features
- Register pets with photos
- View and manage pet information
- Report lost pets
- View medical records
- Browse adoption listings

### Admin Features
- Approve/reject pet registrations
- Manage users and pets
- View system statistics
- Access all user features

## Database Schema

The system uses MySQL with the following main tables:

- `users`: User accounts and information
- `pets`: Pet registrations and details
- `medical_records`: Health and vaccination records
- `comments`: Comments on pet reports

## Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Session-based authentication
- File upload validation

## Development

To run in development mode:
```bash
cd php_version
php -S localhost:8000
```

Visit `http://localhost:8000` in your browser.

## Conversion Notes

This PHP version maintains all the functionality of the original Flask application:

- ✅ User authentication and sessions
- ✅ Pet registration and management
- ✅ Admin approval system
- ✅ Lost pet reports
- ✅ Adoption listings
- ✅ Medical records tracking
- ✅ Responsive Bootstrap UI
- ✅ File upload handling
- ✅ Email notifications (to be implemented)

## Next Steps

The following features are partially implemented or need completion:

- Pet registration form and processing
- Medical records management
- Lost pets functionality
- Adoption system
- Email notifications
- Advanced admin features

## Support

For issues or questions about the PHP conversion, please check the code comments and database schema for implementation details.