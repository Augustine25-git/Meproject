# Sprint 8 - User Authentication with 2FA

## Overview
This sprint implements user authentication with Two-Factor Authentication (2FA) using PHP, MariaDB, and PHPMailer.

## Features Implemented

### 1. Code Reusability with `require` and `include`
- **`includes/header.php`** - Reusable header component
- **`includes/footer.php`** - Reusable footer component
- **`includes/db.php`** - Database connection file
- **`includes/send_2fa.php`** - Email sending functionality

### 2. User Authentication System
- **`register.php`** - User registration with password hashing
- **`login.php`** - User login with credential verification
- **`verify_2fa.php`** - 2FA code verification
- **`dashboard.php`** - Protected dashboard after successful login
- **`logout.php`** - Session destruction and logout

### 3. Two-Factor Authentication
- Generates 6-digit random code
- Sends code via email using PHPMailer
- Verifies code before granting access

### 4. Database Integration
- MariaDB/MySQL database
- Users table with secure password storage
- Prepared statements for SQL injection prevention

## Setup Instructions

### 1. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import `setup_database.sql` or run the SQL commands manually:
```sql
CREATE DATABASE IF NOT EXISTS myproject;
USE myproject;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Email Configuration
Update `includes/send_2fa.php` with your email credentials:
```php
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password'; // Gmail App Password
```

### 3. Test the System
1. Visit `http://localhost/Meproject/test_sprint8.php` to verify setup
2. Register a new user at `http://localhost/Meproject/register.php`
3. Login at `http://localhost/Meproject/login.php`
4. Complete 2FA verification
5. Access dashboard at `http://localhost/Meproject/dashboard.php`

## File Structure
```
Meproject/
├── includes/
│   ├── header.php          # Reusable header
│   ├── footer.php          # Reusable footer
│   ├── db.php             # Database connection
│   └── send_2fa.php       # Email sending
├── register.php            # User registration
├── login.php              # User login
├── verify_2fa.php         # 2FA verification
├── dashboard.php          # Protected dashboard
├── logout.php             # Logout functionality
├── test_sprint8.php       # System test page
├── setup_database.sql     # Database setup
├── composer.json          # PHP dependencies
└── README_Sprint8.md      # This file
```

## Security Features
- Password hashing using `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- 2FA for additional security layer
- Input validation and sanitization

## Dependencies
- PHP 8.3+
- MariaDB/MySQL
- PHPMailer (installed via Composer)
- XAMPP/Apache

## Testing Checklist
- [ ] Database connection works
- [ ] User registration functional
- [ ] User login functional
- [ ] 2FA email sending works
- [ ] 2FA code verification works
- [ ] Dashboard access protected
- [ ] Logout functionality works
- [ ] Reusable includes work properly 