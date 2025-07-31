<?php
require 'includes/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $code = rand(100000, 999999);
        $_SESSION['2fa_code'] = $code;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        require 'includes/send_2fa.php';
        header('Location: verify_2fa.php');
        exit;
    } else {
        echo "Invalid credentials!";
    }
}
?>
<?php require 'includes/header.php'; ?>
<form method="post">
    Username: <input name="username" required><br>
    Password: <input name="password" type="password" required><br>
    <button type="submit">Login</button>
</form>
<?php require 'includes/footer.php'; ?>