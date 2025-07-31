<?php
echo "<h1>Quick MySQL Test</h1>";

try {
    $pdo = new PDO('mysql:host=localhost;port=3307', 'root', '');
    echo "✅ MySQL connection successful on port 3307!<br>";
    
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: " . $version . "<br>";
    
    // Test database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS myproject");
    echo "✅ Database 'myproject' created/verified!<br>";
    
    // Connect to specific database
    $pdo = new PDO('mysql:host=localhost;port=3307;dbname=myproject', 'root', '');
    echo "✅ Connected to 'myproject' database!<br>";
    
    echo "<h2>🎉 MySQL is Working!</h2>";
    echo "<p><a href='test_sprint8.php'>Test Sprint 8 Now</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?> 