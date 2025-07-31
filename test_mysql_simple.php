<?php
echo "<h1>Simple MySQL Test</h1>";

// Test different MySQL connection methods
$connections = [
    'Default port (3306)' => 'mysql:host=localhost;dbname=myproject',
    'Port 3307' => 'mysql:host=localhost;port=3307;dbname=myproject',
    'Socket connection' => 'mysql:host=localhost;unix_socket=/tmp/mysql.sock;dbname=myproject'
];

$working_connection = null;

foreach ($connections as $name => $dsn) {
    try {
        echo "<h3>Testing: $name</h3>";
        $pdo = new PDO($dsn, 'root', '');
        echo "✅ $name - SUCCESS!<br>";
        
        // Test if we can query
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "MySQL Version: $version<br>";
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS myproject");
        echo "✅ Database 'myproject' created/verified!<br>";
        
        $working_connection = $dsn;
        break;
        
    } catch (Exception $e) {
        echo "❌ $name - FAILED: " . $e->getMessage() . "<br>";
    }
}

if ($working_connection) {
    echo "<h2>🎉 MySQL is Working!</h2>";
    echo "<p>Working connection: $working_connection</p>";
    echo "<p><a href='test_sprint8.php'>Test Sprint 8 Now</a></p>";
} else {
    echo "<h2>❌ No MySQL Connection Working</h2>";
    echo "<p>Please start MySQL in XAMPP Control Panel</p>";
}
?> 