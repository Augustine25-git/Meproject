-- Sprint 9: Basic CRUD Database Setup (30% of Final System)
-- User management, Orders, Reservations, & Inventory management

USE myproject;

-- Update users table to include role
ALTER TABLE users ADD COLUMN role ENUM('admin', 'user', 'manager') DEFAULT 'user' AFTER password;

-- Create password_resets table for 2FA reset functionality
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    items JSON NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create reservations table
CREATE TABLE IF NOT EXISTS reservations (
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

-- Create inventory table
CREATE TABLE IF NOT EXISTS inventory (
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

-- Create order_items table for detailed order tracking
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    inventory_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE
);

-- Insert sample data for testing

-- Sample inventory items
INSERT INTO inventory (name, description, quantity, price, category) VALUES
('Margherita Pizza', 'Classic tomato sauce with mozzarella cheese', 50, 12.99, 'Pizza'),
('Pepperoni Pizza', 'Spicy pepperoni with melted cheese', 45, 14.99, 'Pizza'),
('Chicken Wings', 'Crispy wings with choice of sauce', 100, 8.99, 'Appetizers'),
('Caesar Salad', 'Fresh romaine lettuce with caesar dressing', 30, 7.99, 'Salads'),
('Pasta Carbonara', 'Creamy pasta with bacon and parmesan', 25, 11.99, 'Pasta'),
('Garlic Bread', 'Toasted bread with garlic butter', 40, 3.99, 'Sides'),
('Soft Drinks', 'Various soft drinks (Coke, Sprite, Fanta)', 200, 2.99, 'Beverages'),
('Chocolate Cake', 'Rich chocolate cake with frosting', 15, 6.99, 'Desserts');

-- Sample users (if not exists)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('manager', 'manager@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
('customer1', 'customer1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')
ON DUPLICATE KEY UPDATE role = VALUES(role);

-- Sample orders
INSERT INTO orders (user_id, items, total, status) VALUES
(3, '[{"name":"Margherita Pizza","quantity":2,"price":12.99},{"name":"Garlic Bread","quantity":1,"price":3.99}]', 29.97, 'confirmed'),
(3, '[{"name":"Chicken Wings","quantity":1,"price":8.99},{"name":"Soft Drinks","quantity":2,"price":2.99}]', 14.97, 'pending');

-- Sample reservations
INSERT INTO reservations (user_id, date, time, guests, status) VALUES
(3, '2025-08-15', '19:00:00', 4, 'confirmed'),
(3, '2025-08-20', '20:30:00', 2, 'pending');

-- Create indexes for better performance
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_reservations_user_id ON reservations(user_id);
CREATE INDEX idx_reservations_date ON reservations(date);
CREATE INDEX idx_inventory_category ON inventory(category);
CREATE INDEX idx_password_resets_email ON password_resets(email);
CREATE INDEX idx_password_resets_expires ON password_resets(expires_at);

-- Create views for common queries
CREATE VIEW order_summary AS
SELECT 
    o.id,
    u.username,
    o.total,
    o.status,
    o.created_at,
    COUNT(oi.id) as item_count
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

CREATE VIEW inventory_low_stock AS
SELECT * FROM inventory 
WHERE quantity <= min_stock_level;

CREATE VIEW reservation_summary AS
SELECT 
    r.id,
    u.username,
    r.date,
    r.time,
    r.guests,
    r.status
FROM reservations r
JOIN users u ON r.user_id = u.id
ORDER BY r.date DESC, r.time DESC; 