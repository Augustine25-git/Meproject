<?php
session_start();
require_once 'includes/db.php';
require_once 'Sprint10_Advanced_Data_Processing.php';
require_once 'Sprint11_Basic_Analytics.php';
require_once 'Sprint12_Exportable_Reports.php';

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit();
}

$exportReports = new ExportableReports($pdo);

// Handle export requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = $_POST['report_type'] ?? '';
    $format = $_POST['format'] ?? 'csv';
    $params = $_POST['params'] ?? [];
    
    switch ($format) {
        case 'csv':
            $data = $exportReports->getReportData($reportType, $params);
            $filename = $exportReports->exportToCSV($data, $reportType . '_report.csv');
            break;
        case 'json':
            $data = $exportReports->getReportData($reportType, $params);
            $filename = $exportReports->exportToJSON($data, $reportType . '_report.json');
            break;
        case 'pdf':
            $filename = $exportReports->generatePDFReport($reportType, $params);
            break;
        case 'excel':
            $filename = $exportReports->exportToExcel($reportType, $params);
            break;
    }
    
    if ($filename) {
        $success = "Report exported successfully: $filename";
    } else {
        $error = "Failed to export report";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Export - Restaurant Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4">Reports Export</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Export Reports</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select name="report_type" id="report_type" class="form-select" required>
                                    <option value="">Select Report Type</option>
                                    <option value="sales">Sales Report</option>
                                    <option value="inventory">Inventory Report</option>
                                    <option value="customers">Customer Report</option>
                                    <option value="reservations">Reservation Report</option>
                                    <option value="comprehensive">Comprehensive Report</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="format" class="form-label">Export Format</label>
                                <select name="format" id="format" class="form-select" required>
                                    <option value="csv">CSV</option>
                                    <option value="json">JSON</option>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="period" class="form-label">Time Period (for Sales)</label>
                                <select name="params[period]" id="period" class="form-select">
                                    <option value="month">Monthly</option>
                                    <option value="day">Daily</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Export Report</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Export</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="?quick=sales_csv" class="btn btn-outline-primary">Sales CSV</a>
                            <a href="?quick=inventory_json" class="btn btn-outline-primary">Inventory JSON</a>
                            <a href="?quick=customers_pdf" class="btn btn-outline-primary">Customers PDF</a>
                            <a href="?quick=reservations_excel" class="btn btn-outline-primary">Reservations Excel</a>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Scheduled Reports</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success w-100 mb-2" onclick="generateScheduledReports()">
                            Generate All Scheduled Reports
                        </button>
                        <small class="text-muted">Creates daily, weekly, and monthly reports</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Report Templates</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6>Sales Reports</h6>
                                <ul class="list-unstyled">
                                    <li><a href="#" onclick="exportReport('sales', 'csv', {period: 'month'})">Monthly Sales CSV</a></li>
                                    <li><a href="#" onclick="exportReport('sales', 'pdf', {period: 'month'})">Monthly Sales PDF</a></li>
                                    <li><a href="#" onclick="exportReport('sales', 'excel', {period: 'day'})">Daily Sales Excel</a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6>Inventory Reports</h6>
                                <ul class="list-unstyled">
                                    <li><a href="#" onclick="exportReport('inventory', 'csv')">Inventory Status CSV</a></li>
                                    <li><a href="#" onclick="exportReport('inventory', 'json')">Inventory JSON</a></li>
                                    <li><a href="#" onclick="exportReport('inventory', 'pdf')">Inventory PDF</a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6>Customer Reports</h6>
                                <ul class="list-unstyled">
                                    <li><a href="#" onclick="exportReport('customers', 'csv')">Customer List CSV</a></li>
                                    <li><a href="#" onclick="exportReport('customers', 'pdf')">Customer Analysis PDF</a></li>
                                    <li><a href="#" onclick="exportReport('customers', 'excel')">Customer Excel</a></li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6>Reservation Reports</h6>
                                <ul class="list-unstyled">
                                    <li><a href="#" onclick="exportReport('reservations', 'csv')">Reservations CSV</a></li>
                                    <li><a href="#" onclick="exportReport('reservations', 'json')">Reservations JSON</a></li>
                                    <li><a href="#" onclick="exportReport('reservations', 'pdf')">Reservations PDF</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportReport(type, format, params = {}) {
            const form = document.createElement('form');
            form.method = 'POST';
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'report_type';
            typeInput.value = type;
            form.appendChild(typeInput);
            
            const formatInput = document.createElement('input');
            formatInput.type = 'hidden';
            formatInput.name = 'format';
            formatInput.value = format;
            form.appendChild(formatInput);
            
            for (const [key, value] of Object.entries(params)) {
                const paramInput = document.createElement('input');
                paramInput.type = 'hidden';
                paramInput.name = `params[${key}]`;
                paramInput.value = value;
                form.appendChild(paramInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
        
        function generateScheduledReports() {
            if (confirm('Generate all scheduled reports? This may take a moment.')) {
                window.location.href = '?action=scheduled_reports';
            }
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 