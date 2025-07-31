<?php
session_start();
require 'includes/db.php';
require 'Sprint9_CRUD_System.php';

// Check if user is authenticated and has admin privileges
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
                if ($crud->createUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['role'])) {
                    $message = 'User created successfully!';
                } else {
                    $message = 'Error creating user.';
                }
                break;
                
            case 'update':
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'role' => $_POST['role']
                ];
                if ($crud->updateUser($_POST['id'], $data)) {
                    $message = 'User updated successfully!';
                } else {
                    $message = 'Error updating user.';
                }
                break;
                
            case 'delete':
                if ($crud->deleteUser($_POST['id'])) {
                    $message = 'User deleted successfully!';
                } else {
                    $message = 'Error deleting user.';
                }
                break;
        }
    }
}

$users = $crud->listUsers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - User Management</title>
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
        .form-group { margin: 10px 0; }
        .form-group label { display: inline-block; width: 100px; }
        .form-group input, .form-group select { padding: 8px; width: 200px; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Sprint 9 - User Management</h1>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div style="margin: 20px 0;">
            <button class="btn btn-primary" onclick="openCreateModal()">➕ Create New User</button>
            <a href="dashboard.php" class="btn btn-success">🏠 Dashboard</a>
            <a href="order_management.php" class="btn btn-warning">📋 Orders</a>
            <a href="reservation_management.php" class="btn btn-warning">📅 Reservations</a>
            <a href="inventory_management.php" class="btn btn-warning">📦 Inventory</a>
        </div>
        
        <h2>👥 User List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 3px; font-size: 12px; 
                            background-color: <?= $user['role'] == 'admin' ? '#dc3545' : ($user['role'] == 'manager' ? '#ffc107' : '#28a745') ?>; 
                            color: <?= $user['role'] == 'manager' ? 'black' : 'white' ?>;">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td><?= $user['created_at'] ?></td>
                    <td>
                        <button class="btn btn-warning" onclick="openEditModal(<?= $user['id'] ?>, '<?= $user['username'] ?>', '<?= $user['email'] ?>', '<?= $user['role'] ?>')">✏️ Edit</button>
                        <button class="btn btn-danger" onclick="deleteUser(<?= $user['id'] ?>)">🗑️ Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Create User Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2>Create New User</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Create User</button>
                <button type="button" class="btn btn-danger" onclick="closeCreateModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" id="editUsername" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" id="editRole">
                        <option value="user">User</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update User</button>
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
        
        function openEditModal(id, username, email, role) {
            document.getElementById('editId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
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