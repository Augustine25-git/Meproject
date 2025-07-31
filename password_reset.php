<?php
session_start();
require 'includes/db.php';
require 'Sprint9_CRUD_System.php';

$crud = new CRUDSystem($pdo);
$message = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 'request') {
        $email = $_POST['email'];
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate 2FA code
            $code = $crud->generateResetCode($email);
            
            if ($code) {
                // Send email with 2FA code
                require 'includes/send_2fa.php';
                
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_code'] = $code;
                
                header('Location: password_reset.php?step=verify');
                exit;
            } else {
                $message = 'Error generating reset code.';
            }
        } else {
            $message = 'Email not found in our system.';
        }
    } elseif ($step == 'verify') {
        $code = $_POST['code'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword !== $confirmPassword) {
            $message = 'Passwords do not match.';
        } elseif (strlen($newPassword) < 6) {
            $message = 'Password must be at least 6 characters long.';
        } else {
            $email = $_SESSION['reset_email'];
            
            if ($crud->resetPassword($email, $code, $newPassword)) {
                $message = 'Password reset successfully! You can now login with your new password.';
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_code']);
                $step = 'success';
            } else {
                $message = 'Invalid code or code expired.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sprint 9 - Password Reset with 2FA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .steps { display: flex; margin-bottom: 30px; }
        .step { flex: 1; text-align: center; padding: 10px; }
        .step.active { background-color: #007bff; color: white; border-radius: 5px; }
        .step.completed { background-color: #28a745; color: white; border-radius: 5px; }
        .code-input { font-size: 24px; text-align: center; letter-spacing: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Sprint 9 - Password Reset with 2FA</h1>
        
        <!-- Progress Steps -->
        <div class="steps">
            <div class="step <?= $step == 'request' ? 'active' : ($step == 'verify' || $step == 'success' ? 'completed' : '') ?>">
                1. Request Reset
            </div>
            <div class="step <?= $step == 'verify' ? 'active' : ($step == 'success' ? 'completed' : '') ?>">
                2. Verify 2FA Code
            </div>
            <div class="step <?= $step == 'success' ? 'active' : '' ?>">
                3. Complete
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false || strpos($message, 'Invalid') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step == 'request'): ?>
            <h2>📧 Request Password Reset</h2>
            <p>Enter your email address to receive a 2FA code for password reset.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                <button type="submit" class="btn btn-primary">Send Reset Code</button>
                <a href="login.php" class="btn btn-danger">Back to Login</a>
            </form>
            
        <?php elseif ($step == 'verify'): ?>
            <h2>🔢 Verify 2FA Code</h2>
            <p>We've sent a 6-digit code to your email. Enter it below along with your new password.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label>2FA Code:</label>
                    <input type="text" name="code" required maxlength="6" class="code-input" placeholder="000000">
                </div>
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                <button type="submit" class="btn btn-success">Reset Password</button>
                <a href="password_reset.php" class="btn btn-danger">Start Over</a>
            </form>
            
        <?php elseif ($step == 'success'): ?>
            <h2>✅ Password Reset Complete!</h2>
            <p>Your password has been successfully reset. You can now login with your new password.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="login.php" class="btn btn-success">Go to Login</a>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>🔒 Security Features:</h3>
            <ul>
                <li>✅ 2-Factor Authentication via email</li>
                <li>✅ 6-digit secure code generation</li>
                <li>✅ 15-minute code expiration</li>
                <li>✅ Password strength validation</li>
                <li>✅ Secure password hashing</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Auto-focus on code input
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.querySelector('.code-input');
            if (codeInput) {
                codeInput.focus();
            }
        });
        
        // Format code input
        const codeInput = document.querySelector('.code-input');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
            });
        }
    </script>
</body>
</html> 