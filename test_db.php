<?php
/**
 * Sprint 7: Database Connection Test
 * Test file to diagnose database connection issues
 */

echo "<h1>Database Connection Test</h1>";

// Test 1: Check if PDO is available
echo "<h2>Test 1: PDO Availability</h2>";
if (class_exists('PDO')) {
    echo "<p style='color: green;'>✅ PDO class is available</p>";
    
    // Get available PDO drivers
    $drivers = PDO::getAvailableDrivers();
    echo "<p>Available PDO drivers: " . implode(', ', $drivers) . "</p>";
    
    if (in_array('mysql', $drivers)) {
        echo "<p style='color: green;'>✅ MySQL PDO driver is available</p>";
    } else {
        echo "<p style='color: red;'>❌ MySQL PDO driver is NOT available</p>";
    }
} else {
    echo "<p style='color: red;'>❌ PDO class is NOT available</p>";
}

// Test 2: Check MySQL connection
echo "<h2>Test 2: MySQL Connection</h2>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "<p style='color: green;'>✅ MySQL connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p>MySQL Version: " . $result['version'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ MySQL connection failed: " . $e->getMessage() . "</p>";
}

// Test 3: Check if database exists
echo "<h2>Test 3: Database Check</h2>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'personal_website_db'");
    $databases = $stmt->fetchAll();
    
    if (count($databases) > 0) {
        echo "<p style='color: green;'>✅ Database 'personal_website_db' exists</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Database 'personal_website_db' does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database check failed: " . $e->getMessage() . "</p>";
}

// Test 4: Try to connect to specific database
echo "<h2>Test 4: Specific Database Connection</h2>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=personal_website_db;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "<p style='color: green;'>✅ Connection to personal_website_db successful!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Connection to personal_website_db failed: " . $e->getMessage() . "</p>";
}

// Test 5: Check PHP configuration
echo "<h2>Test 5: PHP Configuration</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Loaded Extensions:</p>";
echo "<ul>";
$extensions = get_loaded_extensions();
foreach ($extensions as $ext) {
    if (stripos($ext, 'mysql') !== false || stripos($ext, 'pdo') !== false) {
        echo "<li style='color: green;'>✅ $ext</li>";
    }
}
echo "</ul>";

echo "<h2>Summary</h2>";
echo "<p>If you see any red errors above, those need to be fixed before the database initialization will work.</p>";
echo "<p><a href='init_database.php'>Try Database Initialization Again</a></p>";
?> 