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
                if ($crud->createInventoryItem($_POST['name'], $_POST['description'], $_POST['quantity'], $_POST['price'], $_POST['category'])) {
                    $message = 'Inventory item created successfully!';
                } else {
                    $message = 'Error creating inventory item.';
                }
                break;
                
            case 'update':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'quantity' => $_POST['quantity'],
                    'price' => $_POST['price'],
                    'category' => $_POST['category'],
                    'min_stock_level' => $_POST['min_stock_level']
                ];
                if ($crud->updateInventoryItem($_POST['id'], $data)) {
                    $message = 'Inventory item updated successfully!';
                } else {
                    $message = 'Error updating inventory item.';
                }
                break;
                
            case 'delete':
                if ($crud->deleteInventoryItem($_POST['id'])) {
                    $message = 'Inventory item deleted successfully!';
                } else {
                    $message = 'Error deleting inventory item.';
                }
                break;
                
            case 'update_quantity':
                if ($crud->updateInventoryQuantity($_POST['id'], $_POST['quantity'])) {
                    $message = 'Quantity updated successfully!';
                } else {
                    $message = 'Error updating quantity.';
                }
                break;
        }
    }
}

$inventory = $crud->listInventoryItems();

// Calculate statistics
$totalItems = count($inventory);
$lowStockItems = 0;
$totalValue = 0;
$categories = [];

foreach ($inventory as $item) {
    if ($item['quantity'] <= $item['min_stock_level']) {
        $lowStockItems++;
    }
    $totalValue += $item['quantity'] * $item['price'];
    $categories[$item['category']] = ($categories[$item['category']] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Inventory Management</title>
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
        .form-group input, .form-group select, .form-group textarea { padding: 8px; width: 200px; }
        .form-group textarea { width: 300px; height: 80px; resize: vertical; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007bff; }
        .stat-label { color: #666; margin-top: 5px; }
        .low-stock { background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .out-of-stock { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .in-stock { background-color: #d4edda; border-left: 4px solid #28a745; }
        .quantity-input { width: 80px !important; }
        .price { font-weight: bold; color: #28a745; }
        .category-badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; background-color: #e9ecef; color: #495057; }
        .filter-section { margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Sprint 9 - Inventory Management</h1>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div style="margin: 20px 0;">
            <button class="btn btn-primary" onclick="openCreateModal()">➕ Add New Item</button>
            <a href="dashboard.php" class="btn btn-success">🏠 Dashboard</a>
            <a href="user_management.php" class="btn btn-warning">👥 Users</a>
            <a href="order_management.php" class="btn btn-warning">📋 Orders</a>
            <a href="reservation_management.php" class="btn btn-warning">📅 Reservations</a>
        </div>
        
        <!-- Statistics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalItems ?></div>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $lowStockItems ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?= number_format($totalValue, 2) ?></div>
                <div class="stat-label">Total Inventory Value</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($categories) ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <h3>🔍 Filter Inventory</h3>
            <label>Category: </label>
            <select id="categoryFilter" onchange="filterInventory()">
                <option value="">All Categories</option>
                <?php foreach (array_keys($categories) as $category): ?>
                <option value="<?= $category ?>"><?= $category ?></option>
                <?php endforeach; ?>
            </select>
            
            <label style="margin-left: 20px;">Stock Status: </label>
            <select id="stockFilter" onchange="filterInventory()">
                <option value="">All Items</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
                <option value="in">In Stock</option>
            </select>
        </div>
        
        <h2>📦 Inventory List</h2>
        <table id="inventoryTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Value</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $item): ?>
                <tr class="inventory-row" 
                    data-category="<?= $item['category'] ?>" 
                    data-quantity="<?= $item['quantity'] ?>" 
                    data-min-stock="<?= $item['min_stock_level'] ?>">
                    <td>#<?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td>
                        <span class="category-badge"><?= $item['category'] ?></span>
                    </td>
                    <td>
                        <input type="number" value="<?= $item['quantity'] ?>" 
                               class="quantity-input" min="0" 
                               onchange="updateQuantity(<?= $item['id'] ?>, this.value)">
                    </td>
                    <td class="price">$<?= number_format($item['price'], 2) ?></td>
                    <td class="price">$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    <td>
                        <?php
                        $statusClass = '';
                        $statusText = '';
                        if ($item['quantity'] == 0) {
                            $statusClass = 'out-of-stock';
                            $statusText = 'Out of Stock';
                        } elseif ($item['quantity'] <= $item['min_stock_level']) {
                            $statusClass = 'low-stock';
                            $statusText = 'Low Stock';
                        } else {
                            $statusClass = 'in-stock';
                            $statusText = 'In Stock';
                        }
                        ?>
                        <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                    </td>
                    <td>
                        <button class="btn btn-info" onclick="viewItem(<?= $item['id'] ?>)">👁️ View</button>
                        <button class="btn btn-warning" onclick="openEditModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', '<?= htmlspecialchars($item['description']) ?>', <?= $item['quantity'] ?>, <?= $item['price'] ?>, '<?= $item['category'] ?>', <?= $item['min_stock_level'] ?>)">✏️ Edit</button>
                        <button class="btn btn-danger" onclick="deleteItem(<?= $item['id'] ?>)">🗑️ Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Create Item Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2>Add New Inventory Item</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Pizza">Pizza</option>
                        <option value="Appetizers">Appetizers</option>
                        <option value="Salads">Salads</option>
                        <option value="Pasta">Pasta</option>
                        <option value="Sides">Sides</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Supplies">Supplies</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" required min="0" value="0">
                </div>
                
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" required min="0" step="0.01" value="0.00">
                </div>
                
                <button type="submit" class="btn btn-success">Add Item</button>
                <button type="button" class="btn btn-danger" onclick="closeCreateModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Inventory Item</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" id="editDescription" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category" id="editCategory" required>
                        <option value="Pizza">Pizza</option>
                        <option value="Appetizers">Appetizers</option>
                        <option value="Salads">Salads</option>
                        <option value="Pasta">Pasta</option>
                        <option value="Sides">Sides</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Supplies">Supplies</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" id="editQuantity" required min="0">
                </div>
                
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" id="editPrice" required min="0" step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Min Stock Level:</label>
                    <input type="number" name="min_stock_level" id="editMinStock" required min="0" value="10">
                </div>
                
                <button type="submit" class="btn btn-success">Update Item</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>
    
    <!-- Update Quantity Form -->
    <form id="updateQuantityForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="update_quantity">
        <input type="hidden" name="id" id="updateQuantityId">
        <input type="hidden" name="quantity" id="updateQuantityValue">
    </form>
    
    <script>
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }
        
        function openEditModal(id, name, description, quantity, price, category, minStock) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editQuantity').value = quantity;
            document.getElementById('editPrice').value = price;
            document.getElementById('editCategory').value = category;
            document.getElementById('editMinStock').value = minStock;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this inventory item?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function viewItem(id) {
            // Implement item detail view
            alert('Inventory item #' + id + ' details would be displayed here.');
        }
        
        function updateQuantity(id, quantity) {
            document.getElementById('updateQuantityId').value = id;
            document.getElementById('updateQuantityValue').value = quantity;
            document.getElementById('updateQuantityForm').submit();
        }
        
        function filterInventory() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;
            const rows = document.querySelectorAll('.inventory-row');
            
            rows.forEach(row => {
                const category = row.dataset.category;
                const quantity = parseInt(row.dataset.quantity);
                const minStock = parseInt(row.dataset.minStock);
                
                let showRow = true;
                
                // Category filter
                if (categoryFilter && category !== categoryFilter) {
                    showRow = false;
                }
                
                // Stock filter
                if (stockFilter) {
                    if (stockFilter === 'low' && quantity > minStock) {
                        showRow = false;
                    } else if (stockFilter === 'out' && quantity > 0) {
                        showRow = false;
                    } else if (stockFilter === 'in' && quantity <= minStock) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
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