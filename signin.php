<?php
session_start();
$activePage = 'signin';
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_connect.php';

// Helper: send 2FA code via email
function send2FACode($email, $code) {
    $subject = 'Your 2FA Code';
    $message = "Your 2FA code is: $code\nThis code will expire in 5 minutes.";
    $headers = 'From: noreply@localhost';
    // For local dev, this will only work if mail() is configured
    return mail($email, $subject, $message, $headers);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $user = verifyUser($email, $password);
        if ($user) {
            // Generate 2FA code
            $code = random_int(100000, 999999);
            $_SESSION['2fa_code'] = $code;
            $_SESSION['2fa_email'] = $email;
            $_SESSION['2fa_expires'] = time() + 300; // 5 minutes
            // Send code
            send2FACode($email, $code);
            // Redirect to 2FA page
            header('Location: 2fa.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter your email and password.';
    }
}
?>
<main>
    <section class="hero">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold">Welcome Back</h1>
                <p class="lead">Sign in to access your account and manage your projects</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="section">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Sign In</h2>
                            <?php if ($error): ?>
                                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <form action="signin.php" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid email address.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback">
                                        Please provide your password.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                                </div>
                                <div class="text-center mt-3">
                                    <p><a href="#" class="text-primary">Forgot your password?</a></p>
                                    <p>Don't have an account? <a href="signup.php" class="text-primary">Sign Up</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?> 