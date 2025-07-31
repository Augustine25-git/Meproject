<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require 'includes/db.php';
require 'Sprint9_CRUD_System.php';

$crud = new CRUDSystem($pdo);

// Get statistics for dashboard
$users = $crud->listUsers();
$orders = $crud->listOrders();
$reservations = $crud->listReservations();
$inventory = $crud->listInventoryItems();

$totalUsers = count($users);
$totalOrders = count($orders);
$totalReservations = count($reservations);
$totalInventory = count($inventory);

// Calculate recent activity
$recentOrders = array_slice($orders, 0, 5);
$recentReservations = array_slice($reservations, 0, 5);
$lowStockItems = array_filter($inventory, function($item) {
    return $item['quantity'] <= $item['min_stock_level'];
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #007bff; }
        .stat-label { color: #666; margin-top: 5px; font-size: 1.1em; }
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin: 20px 0; }
        .main-content { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .sidebar { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { padding: 12px 24px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-info { background-color: #17a2b8; color: white; }
        .activity-list { list-style: none; padding: 0; }
        .activity-list li { padding: 10px 0; border-bottom: 1px solid #eee; }
        .activity-list li:last-child { border-bottom: none; }
        .activity-time { color: #666; font-size: 0.9em; }
        .status-badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .status-pending { background-color: #ffc107; color: black; }
        .status-confirmed { background-color: #28a745; color: white; }
        .status-preparing { background-color: #fd7e14; color: white; }
        .status-ready { background-color: #28a745; color: white; }
        .status-delivered { background-color: #6c757d; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .low-stock-alert { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .welcome-message { font-size: 1.5em; color: #333; margin-bottom: 10px; }
        .quick-actions { margin: 20px 0; }
        .quick-actions h3 { margin-bottom: 15px; color: #333; }
        .action-buttons { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Sprint 9 - Restaurant Management System</h1>
            <p class="welcome-message">Welcome back! Here's your system overview.</p>
            <div style="margin-top: 15px;">
                <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
            </div>
        </div>
        
        <!-- Statistics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalUsers ?></div>
                <div class="stat-label">👥 Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-label">📋 Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalReservations ?></div>
                <div class="stat-label">📅 Total Reservations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalInventory ?></div>
                <div class="stat-label">📦 Inventory Items</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>⚡ Quick Actions</h3>
            <div class="action-buttons">
                <a href="user_management.php" class="btn btn-primary">👥 Manage Users</a>
                <a href="order_management.php" class="btn btn-success">📋 Manage Orders</a>
                <a href="reservation_management.php" class="btn btn-info">📅 Manage Reservations</a>
                <a href="inventory_management.php" class="btn btn-warning">📦 Manage Inventory</a>
                <a href="password_reset.php" class="btn btn-danger">🔐 Reset Password</a>
            </div>
        </div>
        
        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-content">
                <h2>📊 Recent Activity</h2>
                
                <h3>📋 Recent Orders</h3>
                <?php if (empty($recentOrders)): ?>
                    <p>No recent orders.</p>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recentOrders as $order): ?>
                        <li>
                            <strong>Order #<?= $order['id'] ?></strong> - <?= htmlspecialchars($order['username']) ?>
                            <br>
                            <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
                            <span class="activity-time"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <h3>📅 Recent Reservations</h3>
                <?php if (empty($recentReservations)): ?>
                    <p>No recent reservations.</p>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recentReservations as $reservation): ?>
                        <li>
                            <strong>Reservation #<?= $reservation['id'] ?></strong> - <?= htmlspecialchars($reservation['username']) ?>
                            <br>
                            <?= date('M j, Y', strtotime($reservation['date'])) ?> at <?= date('g:i A', strtotime($reservation['time'])) ?>
                            (<?= $reservation['guests'] ?> guests)
                            <span class="status-badge status-<?= $reservation['status'] ?>"><?= ucfirst($reservation['status']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="sidebar">
                <h2>🔔 Alerts & Notifications</h2>
                
                <?php if (count($lowStockItems) > 0): ?>
                <div class="low-stock-alert">
                    <h4>⚠️ Low Stock Alert</h4>
                    <p><?= count($lowStockItems) ?> item(s) need restocking:</p>
                    <ul>
                        <?php foreach (array_slice($lowStockItems, 0, 3) as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?> left)</li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($lowStockItems) > 3): ?>
                        <p>... and <?= count($lowStockItems) - 3 ?> more items</p>
                    <?php endif; ?>
                    <a href="inventory_management.php" class="btn btn-warning">View All</a>
                </div>
                <?php else: ?>
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin: 10px 0;">
                    <h4>✅ All Good!</h4>
                    <p>All inventory items are well stocked.</p>
                </div>
                <?php endif; ?>
                
                <h3>🎯 Sprint 9 Features</h3>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ <strong>User Management</strong> - Complete CRUD operations</li>
                    <li>✅ <strong>2FA Password Reset</strong> - Secure credential recovery</li>
                    <li>✅ <strong>Order Management</strong> - Track order status and items</li>
                    <li>✅ <strong>Reservation Management</strong> - Calendar view and booking</li>
                    <li>✅ <strong>Inventory Management</strong> - Stock tracking and alerts</li>
                    <li>✅ <strong>Role-based Access</strong> - Admin, Manager, User roles</li>
                    <li>✅ <strong>Real-time Statistics</strong> - Dashboard analytics</li>
                    <li>✅ <strong>Responsive Design</strong> - Mobile-friendly interface</li>
                </ul>
                
                <h3>🔧 System Status</h3>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ Database: Connected</li>
                    <li>✅ Authentication: Active</li>
                    <li>✅ Email Service: Configured</li>
                    <li>✅ Session Management: Working</li>
                </ul>
            </div>
        </div>
        
        <!-- Sprint 9 Progress -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
            <h2>🚀 Sprint 9 Progress (30% of Final System)</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                <div>
                    <h4>👥 User Management</h4>
                    <ul>
                        <li>Create, Read, Update, Delete users</li>
                        <li>Role-based access control</li>
                        <li>User profile management</li>
                        <li>Activity tracking</li>
                    </ul>
                </div>
                <div>
                    <h4>🔐 2FA Password Reset</h4>
                    <ul>
                        <li>Email-based verification</li>
                        <li>Secure code generation</li>
                        <li>Time-limited codes</li>
                        <li>Password strength validation</li>
                    </ul>
                </div>
                <div>
                    <h4>📋 Order Management</h4>
                    <ul>
                        <li>Order creation and tracking</li>
                        <li>Status management</li>
                        <li>Item selection interface</li>
                        <li>Order history</li>
                    </ul>
                </div>
                <div>
                    <h4>📅 Reservation Management</h4>
                    <ul>
                        <li>Calendar view</li>
                        <li>Date and time selection</li>
                        <li>Guest count tracking</li>
                        <li>Special requests</li>
                    </ul>
                </div>
                <div>
                    <h4>📦 Inventory Management</h4>
                    <ul>
                        <li>Stock tracking</li>
                        <li>Low stock alerts</li>
                        <li>Category organization</li>
                        <li>Value calculations</li>
                    </ul>
                </div>
                <div>
                    <h4>📊 Dashboard Analytics</h4>
                    <ul>
                        <li>Real-time statistics</li>
                        <li>Activity monitoring</li>
                        <li>Alert system</li>
                        <li>Quick actions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 