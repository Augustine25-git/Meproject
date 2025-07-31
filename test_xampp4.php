<?php
echo "<h1>XAMPP4 Test Page</h1>";

// Test 1: PHP is working
echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: Database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=myproject', 'root', '');
    echo "✅ Database connection successful!<br>";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists!<br>";
    } else {
        echo "❌ Users table not found. Please run setup_database.sql<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: File system
echo "<h2>3. File System Test</h2>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Project files exist: " . (file_exists('test_sprint8.php') ? '✅ Yes' : '❌ No') . "<br>";

// Test 4: Apache
echo "<h2>4. Apache Test</h2>";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<h2>5. Next Steps:</h2>";
echo "1. Open XAMPP Control Panel<br>";
echo "2. Start Apache and MySQL services<br>";
echo "3. Visit: <a href='http://localhost/Meproject/test_sprint8.php'>Sprint 8 Test</a><br>";
echo "4. Visit: <a href='http://localhost/phpmyadmin'>phpMyAdmin</a><br>";
?> 