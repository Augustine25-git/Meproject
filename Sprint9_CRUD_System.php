<?php
/**
 * Sprint 9 - Basic CRUD System (30% of Final System)
 * 
 * Features:
 * - User management (CRUD operations)
 * - User reset credentials using 2FA
 * - Orders management
 * - Reservations management
 * - Inventory management
 */

class CRUDSystem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // ==================== USER MANAGEMENT ====================
    
    /**
     * Create new user
     */
    public function createUser($username, $email, $password, $role = 'user') {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password, role, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$username, $email, $hashedPassword, $role]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Read user by ID
     */
    public function readUser($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        try {
            $sql = "UPDATE users SET ";
            $params = [];
            
            foreach ($data as $field => $value) {
                if ($field !== 'id' && $field !== 'password') {
                    $sql .= "$field = ?, ";
                    $params[] = $value;
                }
            }
            
            $sql = rtrim($sql, ', ');
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * List all users
     */
    public function listUsers() {
        try {
            $stmt = $this->pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==================== 2FA RESET CREDENTIALS ====================
    
    /**
     * Generate 2FA code for password reset
     */
    public function generateResetCode($email) {
        try {
            $code = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO password_resets (email, code, expires_at) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE code = ?, expires_at = ?
            ");
            $stmt->execute([$email, $code, $expires, $code, $expires]);
            
            return $code;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Verify reset code and update password
     */
    public function resetPassword($email, $code, $newPassword) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM password_resets 
                WHERE email = ? AND code = ? AND expires_at > NOW()
            ");
            $stmt->execute([$email, $code]);
            $reset = $stmt->fetch();
            
            if ($reset) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashedPassword, $email]);
                
                // Delete used reset code
                $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // ==================== ORDERS MANAGEMENT ====================
    
    /**
     * Create new order
     */
    public function createOrder($userId, $items, $total, $status = 'pending') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (user_id, items, total, status, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$userId, json_encode($items), $total, $status]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Read order by ID
     */
    public function readOrder($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update order status
     */
    public function updateOrder($id, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete order
     */
    public function deleteOrder($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * List all orders
     */
    public function listOrders() {
        try {
            $stmt = $this->pdo->query("
                SELECT o.*, u.username 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==================== RESERVATIONS MANAGEMENT ====================
    
    /**
     * Create new reservation
     */
    public function createReservation($userId, $date, $time, $guests, $status = 'pending') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reservations (user_id, date, time, guests, status, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$userId, $date, $time, $guests, $status]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Read reservation by ID
     */
    public function readReservation($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update reservation
     */
    public function updateReservation($id, $data) {
        try {
            $sql = "UPDATE reservations SET ";
            $params = [];
            
            foreach ($data as $field => $value) {
                if ($field !== 'id') {
                    $sql .= "$field = ?, ";
                    $params[] = $value;
                }
            }
            
            $sql = rtrim($sql, ', ');
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete reservation
     */
    public function deleteReservation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * List all reservations
     */
    public function listReservations() {
        try {
            $stmt = $this->pdo->query("
                SELECT r.*, u.username 
                FROM reservations r 
                JOIN users u ON r.user_id = u.id 
                ORDER BY r.date DESC, r.time DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==================== INVENTORY MANAGEMENT ====================
    
    /**
     * Create new inventory item
     */
    public function createInventoryItem($name, $description, $quantity, $price, $category) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO inventory (name, description, quantity, price, category, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$name, $description, $quantity, $price, $category]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Read inventory item by ID
     */
    public function readInventoryItem($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update inventory item
     */
    public function updateInventoryItem($id, $data) {
        try {
            $sql = "UPDATE inventory SET ";
            $params = [];
            
            foreach ($data as $field => $value) {
                if ($field !== 'id') {
                    $sql .= "$field = ?, ";
                    $params[] = $value;
                }
            }
            
            $sql = rtrim($sql, ', ');
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete inventory item
     */
    public function deleteInventoryItem($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM inventory WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * List all inventory items
     */
    public function listInventoryItems() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM inventory ORDER BY category, name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Update inventory quantity
     */
    public function updateInventoryQuantity($id, $quantity) {
        try {
            $stmt = $this->pdo->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
            return $stmt->execute([$quantity, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?> 