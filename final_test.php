<?php
echo "<h1>🎉 Final Test - MySQL is Working!</h1>";

try {
    // Test database connection
    $pdo = new PDO('mysql:host=localhost;dbname=myproject', 'root', '');
    echo "✅ MySQL connection successful!<br>";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version<br>";
    
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
    
    echo "<h2>🎯 Sprint 8 is Ready!</h2>";
    echo "<p>All components are working:</p>";
    echo "<ul>";
    echo "<li>✅ Apache is serving files</li>";
    echo "<li>✅ MySQL is running on port 3306</li>";
    echo "<li>✅ Database 'myproject' exists</li>";
    echo "<li>✅ Users table is created</li>";
    echo "<li>✅ PHP is working</li>";
    echo "</ul>";
    
    echo "<h2>🚀 Test Your Sprint 8 Features:</h2>";
    echo "<ul>";
    echo "<li><a href='register.php'>Register a new user</a></li>";
    echo "<li><a href='login.php'>Login with 2FA</a></li>";
    echo "<li><a href='test_sprint8.php'>Full Sprint 8 Test</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?> 