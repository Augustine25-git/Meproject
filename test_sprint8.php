<?php
// Sprint 8 Test Page
echo "<h1>Sprint 8 - Test Page</h1>";

// Test 1: Database Connection
echo "<h2>1. Testing Database Connection</h2>";
try {
    require 'includes/db.php';
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

// Test 2: PHPMailer
echo "<h2>2. Testing PHPMailer</h2>";
try {
    require 'vendor/autoload.php';
    echo "✅ PHPMailer loaded successfully!<br>";
} catch (Exception $e) {
    echo "❌ PHPMailer failed: " . $e->getMessage() . "<br>";
}

// Test 3: Reusable Includes
echo "<h2>3. Testing Reusable Includes</h2>";
try {
    ob_start();
    require 'includes/header.php';
    $header = ob_get_clean();
    echo "✅ Header include works!<br>";
    
    ob_start();
    require 'includes/footer.php';
    $footer = ob_get_clean();
    echo "✅ Footer include works!<br>";
} catch (Exception $e) {
    echo "❌ Include failed: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Sprint 8 Files Created:</h2>";
$files = [
    'includes/header.php',
    'includes/footer.php', 
    'includes/db.php',
    'includes/send_2fa.php',
    'register.php',
    'login.php',
    'verify_2fa.php',
    'setup_database.sql',
    'composer.json'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>5. Next Steps:</h2>";
echo "1. Run setup_database.sql in phpMyAdmin<br>";
echo "2. Update includes/send_2fa.php with your email credentials<br>";
echo "3. Test registration at: <a href='register.php'>register.php</a><br>";
echo "4. Test login at: <a href='login.php'>login.php</a><br>";
?> 