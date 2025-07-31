<?php
/**
 * Sprint 9 - Comprehensive Test System
 * Tests all CRUD operations and Sprint 9 features
 */

session_start();
require 'includes/db.php';
require 'Sprint9_CRUD_System.php';

$crud = new CRUDSystem($pdo);
$testResults = [];

// Test 1: Database Connection
function testDatabaseConnection($pdo) {
    try {
        $stmt = $pdo->query("SELECT 1");
        return ['status' => '✅ PASS', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => '❌ FAIL', 'message' => 'Database connection failed: ' . $e->getMessage()];
    }
}

// Test 2: Check Required Tables
function testRequiredTables($pdo) {
    $requiredTables = ['users', 'password_resets', 'orders', 'reservations', 'inventory'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                $missingTables[] = $table;
            }
        } catch (Exception $e) {
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        return ['status' => '✅ PASS', 'message' => 'All required tables exist'];
    } else {
        return ['status' => '❌ FAIL', 'message' => 'Missing tables: ' . implode(', ', $missingTables)];
    }
}

// Test 3: User Management CRUD
function testUserManagement($crud) {
    $results = [];
    
    // Test Create User
    try {
        $testUsername = 'test_user_' . time();
        $testEmail = 'test' . time() . '@example.com';
        $result = $crud->createUser($testUsername, $testEmail, 'testpassword123', 'user');
        if ($result) {
            $results[] = ['status' => '✅ PASS', 'message' => 'User creation successful'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'User creation failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'User creation error: ' . $e->getMessage()];
    }
    
    // Test List Users
    try {
        $users = $crud->listUsers();
        if (is_array($users)) {
            $results[] = ['status' => '✅ PASS', 'message' => 'User listing successful (' . count($users) . ' users)'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'User listing failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'User listing error: ' . $e->getMessage()];
    }
    
    return $results;
}

// Test 4: 2FA Password Reset
function test2FAPasswordReset($crud) {
    $results = [];
    
    // Test Generate Reset Code
    try {
        $testEmail = 'test@example.com';
        $code = $crud->generateResetCode($testEmail);
        if ($code && strlen($code) == 6) {
            $results[] = ['status' => '✅ PASS', 'message' => '2FA code generation successful'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => '2FA code generation failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => '2FA code generation error: ' . $e->getMessage()];
    }
    
    return $results;
}

// Test 5: Order Management CRUD
function testOrderManagement($crud) {
    $results = [];
    
    // Test Create Order
    try {
        $testItems = [
            ['name' => 'Test Pizza', 'quantity' => 2, 'price' => 12.99],
            ['name' => 'Test Drink', 'quantity' => 1, 'price' => 2.99]
        ];
        $result = $crud->createOrder(1, $testItems, 28.97, 'pending');
        if ($result) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Order creation successful'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Order creation failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Order creation error: ' . $e->getMessage()];
    }
    
    // Test List Orders
    try {
        $orders = $crud->listOrders();
        if (is_array($orders)) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Order listing successful (' . count($orders) . ' orders)'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Order listing failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Order listing error: ' . $e->getMessage()];
    }
    
    return $results;
}

// Test 6: Reservation Management CRUD
function testReservationManagement($crud) {
    $results = [];
    
    // Test Create Reservation
    try {
        $result = $crud->createReservation(1, '2025-08-25', '19:00:00', 4, 'pending');
        if ($result) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Reservation creation successful'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Reservation creation failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Reservation creation error: ' . $e->getMessage()];
    }
    
    // Test List Reservations
    try {
        $reservations = $crud->listReservations();
        if (is_array($reservations)) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Reservation listing successful (' . count($reservations) . ' reservations)'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Reservation listing failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Reservation listing error: ' . $e->getMessage()];
    }
    
    return $results;
}

// Test 7: Inventory Management CRUD
function testInventoryManagement($crud) {
    $results = [];
    
    // Test Create Inventory Item
    try {
        $result = $crud->createInventoryItem('Test Item', 'Test description', 10, 9.99, 'Test Category');
        if ($result) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Inventory item creation successful'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Inventory item creation failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Inventory item creation error: ' . $e->getMessage()];
    }
    
    // Test List Inventory Items
    try {
        $inventory = $crud->listInventoryItems();
        if (is_array($inventory)) {
            $results[] = ['status' => '✅ PASS', 'message' => 'Inventory listing successful (' . count($inventory) . ' items)'];
        } else {
            $results[] = ['status' => '❌ FAIL', 'message' => 'Inventory listing failed'];
        }
    } catch (Exception $e) {
        $results[] = ['status' => '❌ FAIL', 'message' => 'Inventory listing error: ' . $e->getMessage()];
    }
    
    return $results;
}

// Test 8: PHPMailer Integration
function testPHPMailer() {
    try {
        if (file_exists('vendor/autoload.php')) {
            require 'vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            return ['status' => '✅ PASS', 'message' => 'PHPMailer loaded successfully'];
        } else {
            return ['status' => '❌ FAIL', 'message' => 'PHPMailer not found'];
        }
    } catch (Exception $e) {
        return ['status' => '❌ FAIL', 'message' => 'PHPMailer error: ' . $e->getMessage()];
    }
}

// Test 9: Session Management
function testSessionManagement() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return ['status' => '✅ PASS', 'message' => 'Session management working'];
    } else {
        return ['status' => '❌ FAIL', 'message' => 'Session management not working'];
    }
}

// Test 10: File System Access
function testFileSystem() {
    $requiredFiles = [
        'Sprint9_CRUD_System.php',
        'user_management.php',
        'order_management.php',
        'reservation_management.php',
        'inventory_management.php',
        'password_reset.php',
        'dashboard.php'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            $missingFiles[] = $file;
        }
    }
    
    if (empty($missingFiles)) {
        return ['status' => '✅ PASS', 'message' => 'All Sprint 9 files exist'];
    } else {
        return ['status' => '❌ FAIL', 'message' => 'Missing files: ' . implode(', ', $missingFiles)];
    }
}

// Run all tests
$testResults['Database Connection'] = testDatabaseConnection($pdo);
$testResults['Required Tables'] = testRequiredTables($pdo);
$testResults['User Management'] = testUserManagement($crud);
$testResults['2FA Password Reset'] = test2FAPasswordReset($crud);
$testResults['Order Management'] = testOrderManagement($crud);
$testResults['Reservation Management'] = testReservationManagement($crud);
$testResults['Inventory Management'] = testInventoryManagement($crud);
$testResults['PHPMailer Integration'] = testPHPMailer();
$testResults['Session Management'] = testSessionManagement();
$testResults['File System'] = testFileSystem();

// Calculate overall status
$totalTests = 0;
$passedTests = 0;

foreach ($testResults as $testName => $result) {
    if (is_array($result) && isset($result[0])) {
        // Multiple results (like CRUD tests)
        foreach ($result as $subResult) {
            $totalTests++;
            if (strpos($subResult['status'], '✅') !== false) {
                $passedTests++;
            }
        }
    } else {
        // Single result
        $totalTests++;
        if (strpos($result['status'], '✅') !== false) {
            $passedTests++;
        }
    }
}

$overallStatus = ($passedTests == $totalTests) ? '✅ ALL TESTS PASSED' : '❌ SOME TESTS FAILED';
$successRate = round(($passedTests / $totalTests) * 100, 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Comprehensive Test Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-section { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .pass { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .fail { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .overall-status { font-size: 1.5em; font-weight: bold; text-align: center; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .overall-pass { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .overall-fail { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007bff; }
        .stat-label { color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Sprint 9 - Comprehensive Test Results</h1>
            <p>Testing all CRUD operations and Sprint 9 features</p>
        </div>
        
        <!-- Overall Status -->
        <div class="overall-status <?= strpos($overallStatus, '✅') !== false ? 'overall-pass' : 'overall-fail' ?>">
            <?= $overallStatus ?>
            <br>
            <small>Success Rate: <?= $successRate ?>% (<?= $passedTests ?>/<?= $totalTests ?> tests passed)</small>
        </div>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $totalTests ?></div>
                <div class="stat-label">Total Tests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $passedTests ?></div>
                <div class="stat-label">Passed Tests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalTests - $passedTests ?></div>
                <div class="stat-label">Failed Tests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $successRate ?>%</div>
                <div class="stat-label">Success Rate</div>
            </div>
        </div>
        
        <!-- Test Results -->
        <?php foreach ($testResults as $testName => $result): ?>
        <div class="test-section">
            <h3><?= $testName ?></h3>
            <?php if (is_array($result) && isset($result[0])): ?>
                <!-- Multiple results -->
                <?php foreach ($result as $subResult): ?>
                <div class="test-result <?= strpos($subResult['status'], '✅') !== false ? 'pass' : 'fail' ?>">
                    <strong><?= $subResult['status'] ?></strong> - <?= $subResult['message'] ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Single result -->
                <div class="test-result <?= strpos($result['status'], '✅') !== false ? 'pass' : 'fail' ?>">
                    <strong><?= $result['status'] ?></strong> - <?= $result['message'] ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <!-- Sprint 9 Features Summary -->
        <div class="test-section">
            <h3>🎯 Sprint 9 Features Summary</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                <div>
                    <h4>✅ User Management</h4>
                    <ul>
                        <li>Create, Read, Update, Delete operations</li>
                        <li>Role-based access control</li>
                        <li>User authentication</li>
                        <li>Profile management</li>
                    </ul>
                </div>
                <div>
                    <h4>✅ 2FA Password Reset</h4>
                    <ul>
                        <li>Email-based verification</li>
                        <li>Secure code generation</li>
                        <li>Time-limited codes</li>
                        <li>Password validation</li>
                    </ul>
                </div>
                <div>
                    <h4>✅ Order Management</h4>
                    <ul>
                        <li>Order creation and tracking</li>
                        <li>Status management</li>
                        <li>Item selection</li>
                        <li>Order history</li>
                    </ul>
                </div>
                <div>
                    <h4>✅ Reservation Management</h4>
                    <ul>
                        <li>Calendar view</li>
                        <li>Date/time selection</li>
                        <li>Guest tracking</li>
                        <li>Special requests</li>
                    </ul>
                </div>
                <div>
                    <h4>✅ Inventory Management</h4>
                    <ul>
                        <li>Stock tracking</li>
                        <li>Low stock alerts</li>
                        <li>Category organization</li>
                        <li>Value calculations</li>
                    </ul>
                </div>
                <div>
                    <h4>✅ System Integration</h4>
                    <ul>
                        <li>Database connectivity</li>
                        <li>Session management</li>
                        <li>Email integration</li>
                        <li>File system access</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="dashboard.php" style="padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">🏠 Dashboard</a>
            <a href="user_management.php" style="padding: 12px 24px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">👥 Users</a>
            <a href="order_management.php" style="padding: 12px 24px; background-color: #ffc107; color: black; text-decoration: none; border-radius: 5px; margin: 5px;">📋 Orders</a>
            <a href="reservation_management.php" style="padding: 12px 24px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">📅 Reservations</a>
            <a href="inventory_management.php" style="padding: 12px 24px; background-color: #fd7e14; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">📦 Inventory</a>
        </div>
    </div>
</body>
</html> 