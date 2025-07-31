<?php
echo "<h1>Setting up Database for XAMPP4</h1>";

try {
    // Connect to MySQL without specifying database (port 3307)
    $pdo = new PDO('mysql:host=localhost;port=3307', 'root', '');
    echo "✅ Connected to MySQL successfully!<br>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS myproject");
    echo "✅ Database 'myproject' created or already exists!<br>";
    
    // Connect to the specific database
    $pdo = new PDO('mysql:host=localhost;port=3307;dbname=myproject', 'root', '');
    echo "✅ Connected to 'myproject' database!<br>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Users table created successfully!<br>";
    
    echo "<h2>Database Setup Complete!</h2>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='test_sprint8.php'>Test Sprint 8</a></li>";
    echo "<li><a href='register.php'>Register a new user</a></li>";
    echo "<li><a href='login.php'>Login with 2FA</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<p>Make sure MySQL is running in XAMPP Control Panel</p>";
}
?> 