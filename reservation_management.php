<?php
session_start();
require 'includes/db.php';
require 'Sprint9_CRUD_System.php';

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

$crud = new CRUDSystem($pdo);
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $userId = $_SESSION['user_id'] ?? 1; // Default to user ID 1 if not set
                if ($crud->createReservation($userId, $_POST['date'], $_POST['time'], $_POST['guests'], $_POST['status'])) {
                    $message = 'Reservation created successfully!';
                } else {
                    $message = 'Error creating reservation.';
                }
                break;
                
            case 'update':
                $data = [
                    'date' => $_POST['date'],
                    'time' => $_POST['time'],
                    'guests' => $_POST['guests'],
                    'status' => $_POST['status'],
                    'special_requests' => $_POST['special_requests']
                ];
                if ($crud->updateReservation($_POST['id'], $data)) {
                    $message = 'Reservation updated successfully!';
                } else {
                    $message = 'Error updating reservation.';
                }
                break;
                
            case 'delete':
                if ($crud->deleteReservation($_POST['id'])) {
                    $message = 'Reservation deleted successfully!';
                } else {
                    $message = 'Error deleting reservation.';
                }
                break;
        }
    }
}

$reservations = $crud->listReservations();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Reservation Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .btn { padding: 8px 16px; margin: 2px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-info { background-color: #17a2b8; color: white; }
        .form-group { margin: 10px 0; }
        .form-group label { display: inline-block; width: 120px; }
        .form-group input, .form-group select, .form-group textarea { padding: 8px; width: 200px; }
        .form-group textarea { width: 300px; height: 80px; resize: vertical; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .status-badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .status-pending { background-color: #ffc107; color: black; }
        .status-confirmed { background-color: #28a745; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .date-time { font-weight: bold; color: #007bff; }
        .guests { font-weight: bold; color: #28a745; }
        .special-requests { max-width: 200px; word-wrap: break-word; }
        .calendar-view { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin: 20px 0; }
        .calendar-day { padding: 10px; border: 1px solid #ddd; text-align: center; background-color: #f8f9fa; }
        .calendar-day.has-reservation { background-color: #d4edda; border-color: #28a745; }
        .calendar-day.today { background-color: #fff3cd; border-color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Sprint 9 - Reservation Management</h1>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div style="margin: 20px 0;">
            <button class="btn btn-primary" onclick="openCreateModal()">➕ Create New Reservation</button>
            <a href="dashboard.php" class="btn btn-success">🏠 Dashboard</a>
            <a href="user_management.php" class="btn btn-warning">👥 Users</a>
            <a href="order_management.php" class="btn btn-warning">📋 Orders</a>
            <a href="inventory_management.php" class="btn btn-warning">📦 Inventory</a>
        </div>
        
        <h2>📅 Reservation List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date & Time</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Special Requests</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td>#<?= $reservation['id'] ?></td>
                    <td><?= htmlspecialchars($reservation['username']) ?></td>
                    <td class="date-time">
                        <?= date('M j, Y', strtotime($reservation['date'])) ?><br>
                        <?= date('g:i A', strtotime($reservation['time'])) ?>
                    </td>
                    <td class="guests"><?= $reservation['guests'] ?> people</td>
                    <td>
                        <span class="status-badge status-<?= $reservation['status'] ?>">
                            <?= ucfirst($reservation['status']) ?>
                        </span>
                    </td>
                    <td class="special-requests">
                        <?= $reservation['special_requests'] ? htmlspecialchars($reservation['special_requests']) : 'None' ?>
                    </td>
                    <td><?= date('M j, Y g:i A', strtotime($reservation['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-info" onclick="viewReservation(<?= $reservation['id'] ?>)">👁️ View</button>
                        <button class="btn btn-warning" onclick="openEditModal(<?= $reservation['id'] ?>, '<?= $reservation['date'] ?>', '<?= $reservation['time'] ?>', <?= $reservation['guests'] ?>, '<?= $reservation['status'] ?>', '<?= htmlspecialchars($reservation['special_requests'] ?? '') ?>')">✏️ Edit</button>
                        <button class="btn btn-danger" onclick="deleteReservation(<?= $reservation['id'] ?>)">🗑️ Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Calendar View -->
        <h2>📅 Calendar View</h2>
        <div class="calendar-view">
            <?php
            $currentMonth = date('Y-m');
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $startDate = new DateTime($firstDay);
            $endDate = new DateTime($lastDay);
            
            // Get reservations for current month
            $monthReservations = [];
            foreach ($reservations as $reservation) {
                if (date('Y-m', strtotime($reservation['date'])) === $currentMonth) {
                    $monthReservations[date('j', strtotime($reservation['date']))] = $reservation;
                }
            }
            
            // Display calendar
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $day = $currentDate->format('j');
                $isToday = $currentDate->format('Y-m-d') === date('Y-m-d');
                $hasReservation = isset($monthReservations[$day]);
                $class = 'calendar-day';
                if ($isToday) $class .= ' today';
                if ($hasReservation) $class .= ' has-reservation';
                
                echo "<div class='$class'>";
                echo "<strong>$day</strong>";
                if ($hasReservation) {
                    $reservation = $monthReservations[$day];
                    echo "<br><small>{$reservation['guests']} guests</small>";
                    echo "<br><small>" . ucfirst($reservation['status']) . "</small>";
                }
                echo "</div>";
                
                $currentDate->add(new DateInterval('P1D'));
            }
            ?>
        </div>
    </div>
    
    <!-- Create Reservation Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2>Create New Reservation</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label>Time:</label>
                    <select name="time" required>
                        <option value="">Select Time</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="11:30:00">11:30 AM</option>
                        <option value="12:00:00">12:00 PM</option>
                        <option value="12:30:00">12:30 PM</option>
                        <option value="13:00:00">1:00 PM</option>
                        <option value="13:30:00">1:30 PM</option>
                        <option value="14:00:00">2:00 PM</option>
                        <option value="14:30:00">2:30 PM</option>
                        <option value="15:00:00">3:00 PM</option>
                        <option value="17:00:00">5:00 PM</option>
                        <option value="17:30:00">5:30 PM</option>
                        <option value="18:00:00">6:00 PM</option>
                        <option value="18:30:00">6:30 PM</option>
                        <option value="19:00:00">7:00 PM</option>
                        <option value="19:30:00">7:30 PM</option>
                        <option value="20:00:00">8:00 PM</option>
                        <option value="20:30:00">8:30 PM</option>
                        <option value="21:00:00">9:00 PM</option>
                        <option value="21:30:00">9:30 PM</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Number of Guests:</label>
                    <input type="number" name="guests" required min="1" max="20" value="2">
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Special Requests:</label>
                    <textarea name="special_requests" placeholder="Any special requests or dietary restrictions..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Create Reservation</button>
                <button type="button" class="btn btn-danger" onclick="closeCreateModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Reservation Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Reservation</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="date" id="editDate" required>
                </div>
                
                <div class="form-group">
                    <label>Time:</label>
                    <select name="time" id="editTime" required>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="11:30:00">11:30 AM</option>
                        <option value="12:00:00">12:00 PM</option>
                        <option value="12:30:00">12:30 PM</option>
                        <option value="13:00:00">1:00 PM</option>
                        <option value="13:30:00">1:30 PM</option>
                        <option value="14:00:00">2:00 PM</option>
                        <option value="14:30:00">2:30 PM</option>
                        <option value="15:00:00">3:00 PM</option>
                        <option value="17:00:00">5:00 PM</option>
                        <option value="17:30:00">5:30 PM</option>
                        <option value="18:00:00">6:00 PM</option>
                        <option value="18:30:00">6:30 PM</option>
                        <option value="19:00:00">7:00 PM</option>
                        <option value="19:30:00">7:30 PM</option>
                        <option value="20:00:00">8:00 PM</option>
                        <option value="20:30:00">8:30 PM</option>
                        <option value="21:00:00">9:00 PM</option>
                        <option value="21:30:00">9:30 PM</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Number of Guests:</label>
                    <input type="number" name="guests" id="editGuests" required min="1" max="20">
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="editStatus">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Special Requests:</label>
                    <textarea name="special_requests" id="editSpecialRequests" placeholder="Any special requests or dietary restrictions..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Update Reservation</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>
    
    <script>
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }
        
        function openEditModal(id, date, time, guests, status, specialRequests) {
            document.getElementById('editId').value = id;
            document.getElementById('editDate').value = date;
            document.getElementById('editTime').value = time;
            document.getElementById('editGuests').value = guests;
            document.getElementById('editStatus').value = status;
            document.getElementById('editSpecialRequests').value = specialRequests;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteReservation(id) {
            if (confirm('Are you sure you want to delete this reservation?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function viewReservation(id) {
            // Implement reservation detail view
            alert('Reservation #' + id + ' details would be displayed here.');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html> 