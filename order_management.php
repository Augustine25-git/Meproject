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
                $items = json_decode($_POST['items'], true);
                $total = $_POST['total'];
                $userId = $_SESSION['user_id'] ?? 1; // Default to user ID 1 if not set
                
                if ($crud->createOrder($userId, $items, $total, $_POST['status'])) {
                    $message = 'Order created successfully!';
                } else {
                    $message = 'Error creating order.';
                }
                break;
                
            case 'update':
                if ($crud->updateOrder($_POST['id'], $_POST['status'])) {
                    $message = 'Order updated successfully!';
                } else {
                    $message = 'Error updating order.';
                }
                break;
                
            case 'delete':
                if ($crud->deleteOrder($_POST['id'])) {
                    $message = 'Order deleted successfully!';
                } else {
                    $message = 'Error deleting order.';
                }
                break;
        }
    }
}

$orders = $crud->listOrders();
$inventory = $crud->listInventoryItems();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Order Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
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
        .form-group input, .form-group select { padding: 8px; width: 200px; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .status-badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .status-pending { background-color: #ffc107; color: black; }
        .status-confirmed { background-color: #17a2b8; color: white; }
        .status-preparing { background-color: #fd7e14; color: white; }
        .status-ready { background-color: #28a745; color: white; }
        .status-delivered { background-color: #6c757d; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .order-items { max-width: 300px; word-wrap: break-word; }
        .item-list { list-style: none; padding: 0; }
        .item-list li { padding: 2px 0; border-bottom: 1px solid #eee; }
        .total-amount { font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Sprint 9 - Order Management</h1>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div style="margin: 20px 0;">
            <button class="btn btn-primary" onclick="openCreateModal()">➕ Create New Order</button>
            <a href="dashboard.php" class="btn btn-success">🏠 Dashboard</a>
            <a href="user_management.php" class="btn btn-warning">👥 Users</a>
            <a href="reservation_management.php" class="btn btn-warning">📅 Reservations</a>
            <a href="inventory_management.php" class="btn btn-warning">📦 Inventory</a>
        </div>
        
        <h2>📋 Order List</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td class="order-items">
                        <ul class="item-list">
                            <?php 
                            $items = json_decode($order['items'], true);
                            if (is_array($items)):
                                foreach ($items as $item): ?>
                                <li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?> - $<?= number_format($item['price'], 2) ?></li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </td>
                    <td class="total-amount">$<?= number_format($order['total'], 2) ?></td>
                    <td>
                        <span class="status-badge status-<?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-info" onclick="viewOrder(<?= $order['id'] ?>)">👁️ View</button>
                        <button class="btn btn-warning" onclick="openEditModal(<?= $order['id'] ?>, '<?= $order['status'] ?>')">✏️ Edit</button>
                        <button class="btn btn-danger" onclick="deleteOrder(<?= $order['id'] ?>)">🗑️ Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Create Order Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2>Create New Order</h2>
            <form method="POST" id="createOrderForm">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="items" id="orderItems">
                <input type="hidden" name="total" id="orderTotal">
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready">Ready</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <h3>Select Items:</h3>
                <div id="itemSelection">
                    <?php foreach ($inventory as $item): ?>
                    <div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <label>
                            <input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>" 
                                   data-name="<?= htmlspecialchars($item['name']) ?>" 
                                   data-price="<?= $item['price'] ?>" 
                                   onchange="updateOrderItems()">
                            <?= htmlspecialchars($item['name']) ?> - $<?= number_format($item['price'], 2) ?>
                        </label>
                        <input type="number" name="quantity_<?= $item['id'] ?>" value="0" min="0" 
                               style="width: 60px; margin-left: 10px;" 
                               onchange="updateOrderItems()" placeholder="Qty">
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                    <h4>Order Summary:</h4>
                    <div id="orderSummary">No items selected</div>
                    <div style="font-weight: bold; margin-top: 10px;">
                        Total: <span id="orderTotalDisplay">$0.00</span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Create Order</button>
                <button type="button" class="btn btn-danger" onclick="closeCreateModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Order Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Order Status</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="editStatus">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready">Ready</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update Order</button>
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
        
        function openEditModal(id, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteOrder(id) {
            if (confirm('Are you sure you want to delete this order?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function viewOrder(id) {
            // Implement order detail view
            alert('Order #' + id + ' details would be displayed here.');
        }
        
        function updateOrderItems() {
            const checkboxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
            const items = [];
            let total = 0;
            
            checkboxes.forEach(checkbox => {
                const itemId = checkbox.value;
                const quantityInput = document.querySelector(`input[name="quantity_${itemId}"]`);
                const quantity = parseInt(quantityInput.value) || 0;
                
                if (quantity > 0) {
                    const item = {
                        id: itemId,
                        name: checkbox.dataset.name,
                        price: parseFloat(checkbox.dataset.price),
                        quantity: quantity
                    };
                    items.push(item);
                    total += item.price * item.quantity;
                }
            });
            
            // Update hidden inputs
            document.getElementById('orderItems').value = JSON.stringify(items);
            document.getElementById('orderTotal').value = total.toFixed(2);
            
            // Update display
            const summary = document.getElementById('orderSummary');
            const totalDisplay = document.getElementById('orderTotalDisplay');
            
            if (items.length > 0) {
                summary.innerHTML = items.map(item => 
                    `${item.quantity}x ${item.name} - $${(item.price * item.quantity).toFixed(2)}`
                ).join('<br>');
            } else {
                summary.innerHTML = 'No items selected';
            }
            
            totalDisplay.textContent = '$' + total.toFixed(2);
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