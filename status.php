<?php
echo "<h1>XAMPP4 Status Check</h1>";

// Check if Apache is working
echo "<h2>1. Apache Status</h2>";
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "✅ Apache is running: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
} else {
    echo "❌ Apache not detected<br>";
}

// Check if MySQL is working
echo "<h2>2. MySQL Status</h2>";
try {
    $pdo = new PDO('mysql:host=localhost;port=3307', 'root', '');
    echo "✅ MySQL is running and accessible!<br>";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: " . $version . "<br>";
    
} catch (Exception $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "<br>";
}

// Check project files
echo "<h2>3. Project Files</h2>";
$files = ['test_sprint8.php', 'register.php', 'login.php', 'includes/db.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>4. Quick Actions</h2>";
echo "<ul>";
echo "<li><a href='setup_db_xampp4.php'>Setup Database</a></li>";
echo "<li><a href='test_sprint8.php'>Test Sprint 8</a></li>";
echo "<li><a href='register.php'>Register User</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "</ul>";

echo "<h2>5. XAMPP Control Panel</h2>";
echo "<p>If services are not working:</p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Stop all services</li>";
echo "<li>Start Apache first</li>";
echo "<li>Start MySQL second</li>";
echo "<li>Refresh this page</li>";
echo "</ol>";
?> 