# Sprint 9 - Basic CRUD System (30% of Final System)

## 🎯 **Project Overview**

Sprint 9 implements a comprehensive CRUD (Create, Read, Update, Delete) system for a restaurant management application. This represents 30% of the final system and includes user management, order processing, reservation handling, and inventory control with 2-Factor Authentication for password resets.

## 📋 **Features Implemented**

### ✅ **1. User Management**
- **Complete CRUD Operations**: Create, Read, Update, Delete users
- **Role-based Access Control**: Admin, Manager, User roles
- **User Authentication**: Secure login system
- **Profile Management**: User information management
- **Activity Tracking**: User activity monitoring

### ✅ **2. 2-Factor Authentication Password Reset**
- **Email-based Verification**: Secure code delivery
- **6-digit Code Generation**: Random secure codes
- **15-minute Expiration**: Time-limited security
- **Password Strength Validation**: Minimum requirements
- **Secure Password Hashing**: bcrypt encryption

### ✅ **3. Order Management**
- **Order Creation**: Multi-item order processing
- **Status Tracking**: Pending → Confirmed → Preparing → Ready → Delivered
- **Item Selection**: Dynamic inventory integration
- **Order History**: Complete order tracking
- **Total Calculation**: Automatic pricing

### ✅ **4. Reservation Management**
- **Calendar View**: Visual booking interface
- **Date/Time Selection**: Flexible scheduling
- **Guest Tracking**: Party size management
- **Special Requests**: Custom requirements
- **Status Management**: Confirmation workflow

### ✅ **5. Inventory Management**
- **Stock Tracking**: Real-time quantity monitoring
- **Low Stock Alerts**: Automatic notifications
- **Category Organization**: Product classification
- **Value Calculations**: Total inventory worth
- **Filtering System**: Advanced search capabilities

## 🗂️ **File Structure**

```
Meproject/
├── Sprint9_CRUD_System.php          # Main CRUD controller class
├── setup_sprint9_database.sql       # Database schema and sample data
├── user_management.php              # User CRUD interface
├── password_reset.php               # 2FA password reset system
├── order_management.php             # Order CRUD interface
├── reservation_management.php       # Reservation CRUD interface
├── inventory_management.php         # Inventory CRUD interface
├── dashboard.php                    # Updated dashboard with Sprint 9 features
├── test_sprint9.php                # Comprehensive testing system
├── README_Sprint9.md               # This documentation
├── includes/
│   ├── db.php                      # Database connection
│   ├── header.php                  # Reusable header
│   ├── footer.php                  # Reusable footer
│   └── send_2fa.php               # Email functionality
└── vendor/                         # PHPMailer dependencies
    └── phpmailer/
```

## 🗄️ **Database Schema**

### **Users Table**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'manager') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **Password Resets Table**
```sql
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);
```

### **Orders Table**
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    items JSON NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### **Reservations Table**
```sql
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    guests INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### **Inventory Table**
```sql
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    min_stock_level INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🚀 **Setup Instructions**

### **1. Database Setup**
```bash
# Run the database setup script
mysql -u root -p < setup_sprint9_database.sql
```

### **2. Email Configuration**
Update `includes/send_2fa.php` with your SMTP credentials:
```php
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';
```

### **3. Access the System**
1. Navigate to `http://localhost/Meproject/login.php`
2. Login with default credentials:
   - Username: `admin`
   - Password: `password`
3. Access the dashboard and explore all features

## 🔧 **System Features**

### **Dashboard Analytics**
- Real-time statistics
- Recent activity monitoring
- Low stock alerts
- Quick action buttons
- System status indicators

### **User Management**
- Create new users with roles
- Edit user information
- Delete users (with confirmation)
- Role-based access control
- User activity tracking

### **Order Management**
- Create orders with multiple items
- Track order status
- View order history
- Update order status
- Calculate totals automatically

### **Reservation Management**
- Calendar view of bookings
- Create new reservations
- Edit reservation details
- Track guest counts
- Handle special requests

### **Inventory Management**
- Add new inventory items
- Track stock levels
- Set minimum stock alerts
- Filter by category
- Calculate inventory value

### **2FA Password Reset**
- Email-based verification
- Secure code generation
- Time-limited codes
- Password strength validation
- Secure password updates

## 🧪 **Testing**

Run the comprehensive test suite:
```bash
# Access the test page
http://localhost/Meproject/test_sprint9.php
```

The test system validates:
- Database connectivity
- All CRUD operations
- 2FA functionality
- Email integration
- Session management
- File system access

## 🔒 **Security Features**

- **Password Hashing**: bcrypt encryption
- **Prepared Statements**: SQL injection prevention
- **Session Management**: Secure authentication
- **2FA Integration**: Two-factor authentication
- **Input Validation**: Data sanitization
- **Role-based Access**: Permission control

## 📊 **Performance Features**

- **Database Indexing**: Optimized queries
- **Connection Pooling**: Efficient database usage
- **Caching**: Session-based caching
- **Error Handling**: Comprehensive error management
- **Logging**: Activity tracking

## 🎨 **User Interface**

- **Responsive Design**: Mobile-friendly interface
- **Modern UI**: Clean, professional appearance
- **Intuitive Navigation**: Easy-to-use menus
- **Real-time Updates**: Dynamic content
- **Accessibility**: Screen reader support

## 📈 **Sprint 9 Progress (30% Complete)**

### **Completed Features:**
- ✅ User Management CRUD
- ✅ 2FA Password Reset
- ✅ Order Management
- ✅ Reservation Management
- ✅ Inventory Management
- ✅ Dashboard Analytics
- ✅ Security Implementation
- ✅ Testing Framework

### **Next Steps (Sprint 10):**
- Advanced reporting
- Payment processing
- Customer feedback system
- Advanced analytics
- API development

## 🐛 **Troubleshooting**

### **Common Issues:**

1. **Database Connection Error**
   - Verify MySQL is running
   - Check database credentials in `includes/db.php`

2. **Email Not Sending**
   - Update SMTP credentials in `includes/send_2fa.php`
   - Check firewall settings

3. **Session Issues**
   - Ensure PHP sessions are enabled
   - Check file permissions

4. **File Not Found Errors**
   - Verify all files are in the correct directory
   - Check Apache configuration

## 📞 **Support**

For technical support or questions about Sprint 9 implementation, refer to:
- Database schema: `setup_sprint9_database.sql`
- Main CRUD system: `Sprint9_CRUD_System.php`
- Test results: `test_sprint9.php`

## 🎯 **Assessment Criteria Met**

- ✅ **User Management**: Complete CRUD operations
- ✅ **2FA Password Reset**: Secure credential recovery
- ✅ **Order Management**: Full order lifecycle
- ✅ **Reservation Management**: Booking system
- ✅ **Inventory Management**: Stock control
- ✅ **Database Integration**: MariaDB/MySQL
- ✅ **Security Implementation**: Authentication & authorization
- ✅ **Testing**: Comprehensive test suite
- ✅ **Documentation**: Complete system documentation

**Sprint 9 Status: COMPLETE ✅**
**Ready for Progress Assessment ✅** 