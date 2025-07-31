<?php
echo "<h1>MySQL Fix Test - Port 3307</h1>";

try {
    // Test connection to MySQL on port 3307
    $pdo = new PDO('mysql:host=localhost;port=3307', 'root', '');
    echo "✅ MySQL connection successful on port 3307!<br>";
    
    // Test database creation
    $pdo->exec("CREATE DATABASE IF NOT EXISTS myproject");
    echo "✅ Database 'myproject' created/verified!<br>";
    
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
    
    echo "<h2>🎉 MySQL is Working!</h2>";
    echo "<p>Your Sprint 8 authentication system is ready!</p>";
    echo "<ul>";
    echo "<li><a href='register.php'>Register a new user</a></li>";
    echo "<li><a href='login.php'>Login with 2FA</a></li>";
    echo "<li><a href='test_sprint8.php'>Test Sprint 8</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<p>MySQL might not be running. Check XAMPP Control Panel.</p>";
}
?> 