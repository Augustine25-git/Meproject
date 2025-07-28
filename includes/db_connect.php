<?php
/**
 * Sprint 7: Database Connection
 * Database connection configuration for personal website
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'personal_website_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // Log error (in production, log to file instead of displaying)
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

// Test database connection
function testDBConnection() {
    $pdo = getDBConnection();
    if ($pdo) {
        echo "Database connection successful!";
        return true;
    } else {
        echo "Database connection failed!";
        return false;
    }
}

// Get services from database
function getServices() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY service_name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching services: " . $e->getMessage());
        return [];
    }
}

// Get featured projects from database
function getFeaturedProjects() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM projects WHERE is_featured = TRUE ORDER BY created_at DESC LIMIT 6");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching projects: " . $e->getMessage());
        return [];
    }
}

// Get skills by category
function getSkillsByCategory($category = null) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM skills WHERE category = ? AND is_active = TRUE ORDER BY skill_name");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM skills WHERE is_active = TRUE ORDER BY category, skill_name");
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching skills: " . $e->getMessage());
        return [];
    }
}

// Get education history
function getEducation() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM education WHERE is_active = TRUE ORDER BY graduation_year DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching education: " . $e->getMessage());
        return [];
    }
}

// Get work experience
function getExperience() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM experience WHERE is_active = TRUE ORDER BY start_date DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching experience: " . $e->getMessage());
        return [];
    }
}

// Save service request
function saveServiceRequest($data) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO service_requests 
            (first_name, last_name, email, phone, company, service_required, 
             budget_range, timeline, start_date, end_date, project_description, newsletter_subscription)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] ?? '',
            $data['company'] ?? '',
            $data['service_required'],
            $data['budget_range'] ?? '',
            $data['timeline'] ?? '',
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['project_description'],
            $data['newsletter_subscription'] ?? false
        ]);
    } catch (PDOException $e) {
        error_log("Error saving service request: " . $e->getMessage());
        return false;
    }
}

// Save contact message
function saveContactMessage($data) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages 
            (name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'] ?? '',
            $data['message']
        ]);
    } catch (PDOException $e) {
        error_log("Error saving contact message: " . $e->getMessage());
        return false;
    }
}

// Create user account
function createUser($data) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users 
            (username, email, password_hash, first_name, last_name, phone, company, interests, newsletter_subscription)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $password_hash,
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? '',
            $data['company'] ?? '',
            $data['interests'] ?? '',
            $data['newsletter_subscription'] ?? false
        ]);
    } catch (PDOException $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

// Verify user login
function verifyUser($email, $password) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error verifying user: " . $e->getMessage());
        return false;
    }
}

// Get user statistics
function getUserStats() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM user_stats");
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching user stats: " . $e->getMessage());
        return [];
    }
}

// Sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if database exists and create if not
function initializeDatabase() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Use the database
        $pdo->exec("USE " . DB_NAME);
        
        // Read and execute SQL file
        $sql = file_get_contents(__DIR__ . '/../database/website_db.sql');
        $pdo->exec($sql);
        
        return true;
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}
?> 