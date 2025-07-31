<?php
session_start();
$activePage = '';
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_connect.php';

if (!isset($_SESSION['2fa_email'], $_SESSION['2fa_code'], $_SESSION['2fa_expires'])) {
    header('Location: signin.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = trim($_POST['code'] ?? '');
    if ($input_code === (string)$_SESSION['2fa_code'] && time() < $_SESSION['2fa_expires']) {
        // 2FA success: log the user in
        $_SESSION['user_email'] = $_SESSION['2fa_email'];
        unset($_SESSION['2fa_code'], $_SESSION['2fa_email'], $_SESSION['2fa_expires']);
        $success = '2FA verification successful! You are now logged in.';
        // Redirect to dashboard or home
        header('Refresh: 2; url=index.php');
    } else {
        $error = 'Invalid or expired code. Please try again.';
    }
}
?>
<main>
    <section class="hero">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold">Two-Factor Authentication</h1>
                <p class="lead">Enter the 6-digit code sent to your email to complete login.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="section">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">2FA Verification</h2>
                            <?php if ($error): ?>
                                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <?php if ($success): ?>
                                <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
                            <?php endif; ?>
                            <form action="2fa.php" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="code" class="form-label">2FA Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" required pattern="\d{6}" maxlength="6">
                                    <div class="invalid-feedback">
                                        Please enter the 6-digit code sent to your email.
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Verify</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <form action="signin.php" method="POST">
                                    <button type="submit" class="btn btn-link">Back to Sign In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?> 