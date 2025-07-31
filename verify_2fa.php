<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['code'] == $_SESSION['2fa_code']) {
        $_SESSION['authenticated'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Invalid code!";
    }
}
?>
<?php require 'includes/header.php'; ?>
<form method="post">
    Enter 2FA code sent to your email: <input name="code" required><br>
    <button type="submit">Verify</button>
</form>
<?php require 'includes/footer.php'; ?>