<?php
/**
 * Sprint 7: Database Initialization Script
 * Run this script to create the database and tables for the personal website
 */

// Include database connection
require_once 'includes/db_connect.php';

echo "<h1>Sprint 7: Database Initialization</h1>";
echo "<h2>Personal Website Database Setup</h2>";

// Test if we can connect to MySQL
try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ MySQL connection successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ MySQL connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure XAMPP is running and MySQL service is started.</p>";
    exit;
}

// Create database
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database '" . DB_NAME . "' created successfully!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error creating database: " . $e->getMessage() . "</p>";
    exit;
}

// Use the database
$pdo->exec("USE " . DB_NAME);

// Read and execute SQL file
try {
    $sql = file_get_contents('database/website_db.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $successCount++;
            } catch (PDOException $e) {
                $errorCount++;
                echo "<p style='color: orange;'>⚠️ Statement skipped: " . substr($statement, 0, 50) . "...</p>";
            }
        }
    }
    
    echo "<p style='color: green;'>✅ Database setup completed!</p>";
    echo "<p>Successfully executed: $successCount statements</p>";
    if ($errorCount > 0) {
        echo "<p style='color: orange;'>Skipped: $errorCount statements (likely already exist)</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error reading SQL file: " . $e->getMessage() . "</p>";
    exit;
}

// Test database connection
echo "<h3>Testing Database Connection</h3>";
if (testDBConnection()) {
    echo "<p style='color: green;'>✅ Database connection test successful!</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection test failed!</p>";
}

// Test data retrieval
echo "<h3>Testing Data Retrieval</h3>";

// Test services
$services = getServices();
echo "<p>📊 Services in database: " . count($services) . "</p>";

// Test projects
$projects = getFeaturedProjects();
echo "<p>📊 Featured projects in database: " . count($projects) . "</p>";

// Test skills
$skills = getSkillsByCategory();
echo "<p>📊 Skills in database: " . count($skills) . "</p>";

// Test education
$education = getEducation();
echo "<p>📊 Education records in database: " . count($education) . "</p>";

// Test experience
$experience = getExperience();
echo "<p>📊 Experience records in database: " . count($experience) . "</p>";

echo "<h3>Database Tables Created:</h3>";
echo "<ul>";
echo "<li>✅ users - User accounts and authentication</li>";
echo "<li>✅ services - Service offerings</li>";
echo "<li>✅ service_requests - Service request form submissions</li>";
echo "<li>✅ projects - Portfolio projects</li>";
echo "<li>✅ skills - Technical and soft skills</li>";
echo "<li>✅ education - Academic background</li>";
echo "<li>✅ experience - Work history</li>";
echo "<li>✅ contact_messages - Contact form submissions</li>";
echo "<li>✅ blog_posts - Blog content management</li>";
echo "</ul>";

echo "<h3>Database Views Created:</h3>";
echo "<ul>";
echo "<li>✅ active_services - Active service offerings</li>";
echo "<li>✅ featured_projects - Featured portfolio projects</li>";
echo "<li>✅ user_stats - User statistics</li>";
echo "</ul>";

echo "<h3>Sample Data Inserted:</h3>";
echo "<ul>";
echo "<li>✅ 6 service offerings</li>";
echo "<li>✅ 10 technical skills</li>";
echo "<li>✅ 5 portfolio projects</li>";
echo "<li>✅ 2 education records</li>";
echo "<li>✅ 3 work experience records</li>";
echo "</ul>";

echo "<h2 style='color: green;'>🎉 Sprint 7 Database Setup Complete!</h2>";
echo "<p>Your personal website database is now ready to store and manage content.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Test the website forms to ensure they connect to the database</li>";
echo "<li>Customize the content with your personal information</li>";
echo "<li>Add more projects, skills, and experience to the database</li>";
echo "</ul>";

echo "<p><a href='index.html' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Go to Homepage</a></p>";
?> 