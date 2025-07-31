<?php
require 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);
    echo "Registration successful!";
}
?>
<?php require 'includes/header.php'; ?>
<form method="post">
    Username: <input name="username" required><br>
    Email: <input name="email" type="email" required><br>
    Password: <input name="password" type="password" required><br>
    <button type="submit">Register</button>
</form>
<?php require 'includes/footer.php'; ?>