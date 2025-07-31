<?php
session_start();
require_once 'includes/db.php';
require_once 'Sprint10_Advanced_Data_Processing.php';
require_once 'Sprint11_Basic_Analytics.php';

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit();
}

$analytics = new BasicAnalytics($pdo);
$metrics = $analytics->getDashboardMetrics();
$salesData = $analytics->getSalesAnalytics();
$inventoryData = $analytics->getInventoryAnalytics();
$customerData = $analytics->getCustomerAnalytics();
$reservationData = $analytics->getReservationAnalytics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Restaurant Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .metric-card.success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .metric-card.warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .metric-card.info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
        .chart-container { position: relative; height: 300px; margin: 20px 0; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Analytics Dashboard</h1>
        
        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card metric-card">
                    <div class="card-body text-center">
                        <h5>Total Revenue</h5>
                        <h3>$<?php echo $metrics['total_revenue']; ?></h3>
                        <small>Monthly Growth: <?php echo $metrics['monthly_growth']; ?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card success">
                    <div class="card-body text-center">
                        <h5>Total Orders</h5>
                        <h3><?php echo $metrics['total_orders']; ?></h3>
                        <small>Avg: $<?php echo $metrics['avg_order_value']; ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card warning">
                    <div class="card-body text-center">
                        <h5>Customers</h5>
                        <h3><?php echo $metrics['total_customers']; ?></h3>
                        <small>Satisfaction: <?php echo $metrics['customer_satisfaction_score']; ?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card info">
                    <div class="card-body text-center">
                        <h5>Inventory Items</h5>
                        <h3><?php echo $metrics['total_inventory_items']; ?></h3>
                        <small>Top Category: <?php echo $metrics['top_performing_category']; ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory by Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Customer Segments</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="customerChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Peak Hours Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="peakHoursChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Reservation Patterns</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="reservationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($salesData['monthly_sales'], 'period')); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($salesData['monthly_sales'], 'total_revenue')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Order Status Chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($salesData['order_status_distribution'], 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($salesData['order_status_distribution'], 'count')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Inventory Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($inventoryData['category_distribution'], 'category')); ?>,
                datasets: [{
                    label: 'Total Value',
                    data: <?php echo json_encode(array_column($inventoryData['category_distribution'], 'total_value')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Customer Chart
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        new Chart(customerCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($customerData['customer_segments'], 'segment')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($customerData['customer_segments'], 'customer_count')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Peak Hours Chart
        const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
        new Chart(peakHoursCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($salesData['peak_hours'], 'hour')); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode(array_column($salesData['peak_hours'], 'order_count')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Reservation Chart
        const reservationCtx = document.getElementById('reservationChart').getContext('2d');
        new Chart(reservationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($reservationData['daily_distribution'], 'day_name')); ?>,
                datasets: [{
                    label: 'Reservations',
                    data: <?php echo json_encode(array_column($reservationData['daily_distribution'], 'reservation_count')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>