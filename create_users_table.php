<?php
echo "<h1>Creating Users Table for Sprint 8</h1>";

try {
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=myproject', 'root', '');
    echo "✅ Connected to database successfully!<br>";
    
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
    
    // Verify table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table verified and ready!<br>";
    } else {
        echo "❌ Users table not found after creation<br>";
    }
    
    echo "<h2>🎉 Database Setup Complete!</h2>";
    echo "<p>The users table is now ready for Sprint 8 authentication.</p>";
    echo "<ul>";
    echo "<li><a href='test_sprint8.php'>Test Sprint 8 Again</a></li>";
    echo "<li><a href='register.php'>Register a new user</a></li>";
    echo "<li><a href='login.php'>Login with 2FA</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?> 