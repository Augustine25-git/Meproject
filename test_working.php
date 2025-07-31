<?php
echo "<h1>🎉 Apache is Working!</h1>";
echo "<p>If you can see this, Apache is serving from the correct directory.</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Server software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

echo "<h2>Quick Links:</h2>";
echo "<ul>";
echo "<li><a href='create_users_table.php'>Create Users Table</a></li>";
echo "<li><a href='test_sprint8.php'>Test Sprint 8</a></li>";
echo "<li><a href='register.php'>Register User</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "</ul>";
?> 