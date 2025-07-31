<?php
$activePage = 'signup';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="hero">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold">Create Your Account</h1>
                <p class="lead">Join our community and stay updated with the latest projects and services</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="section">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Sign Up</h2>
                            <form action="signup.php" method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                                        <div class="invalid-feedback">
                                            Please provide your first name.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                                        <div class="invalid-feedback">
                                            Please provide your last name.
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid email address.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                    <div class="invalid-feedback">
                                        Please choose a username.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback">
                                        Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    <div class="invalid-feedback">
                                        Please confirm your password.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company/Organization</label>
                                    <input type="text" class="form-control" id="company" name="company">
                                </div>
                                <div class="mb-3">
                                    <label for="interests" class="form-label">Areas of Interest</label>
                                    <select class="form-select" id="interests" name="interests">
                                        <option value="">Select your interests...</option>
                                        <option value="web-development">Web Development</option>
                                        <option value="mobile-apps">Mobile Applications</option>
                                        <option value="ecommerce">E-commerce</option>
                                        <option value="seo">SEO & Digital Marketing</option>
                                        <option value="consulting">Consulting</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a> *
                                        </label>
                                        <div class="invalid-feedback">
                                            You must agree before submitting.
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                        <label class="form-check-label" for="newsletter">
                                            Subscribe to our newsletter for updates and tips
                                        </label>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                                </div>
                                <div class="text-center mt-3">
                                    <p>Already have an account? <a href="signin.php" class="text-primary">Sign In</a></p>
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