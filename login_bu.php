<?php
require_once 'includes/db_connect.php';

$loginError = '';
$submittedEmail = '';
$users = [];

// Fetch all users from database
$query = "SELECT user_email, user_name FROM users WHERE is_active = 1 ORDER BY user_name ASC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedEmail = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($submittedEmail === '' || $password === '') {
        $loginError = 'Please enter both email and password.';
    } else {
        $loginError = 'Login processing is ready for backend validation.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body class="auth-body">
    <main class="auth-shell">
        <section class="auth-showcase">
            <div class="auth-showcase-content">
                <span class="auth-kicker">Pulse Access</span>
                <h1>Manage your workspace from one calm, responsive dashboard.</h1>
                <p>Sign in to review projects, watch team activity, and keep your operations moving from any device.</p>

                <div class="auth-feature-list">
                    <article class="auth-feature-card">
                        <i class="bi bi-phone"></i>
                        <div>
                            <h2>Mobile ready</h2>
                            <p>The left navigation and dashboard stay clean on smaller screens.</p>
                        </div>
                    </article>
                    <article class="auth-feature-card">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <h2>Secure access</h2>
                            <p>Prepared for database-backed authentication and session handling.</p>
                        </div>
                    </article>
                    <article class="auth-feature-card">
                        <i class="bi bi-graph-up-arrow"></i>
                        <div>
                            <h2>Action focused</h2>
                            <p>Quickly move from login to the metrics and project views you need most.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-card-header">
                    <span class="brand-mark">P</span>
                    <div>
                        <p class="auth-overline">Welcome back</p>
                        <h2>Sign in to Pulse Admin</h2>
                    </div>
                </div>

                <?php if ($loginError !== ''): ?>
                    <div class="alert alert-info auth-alert" role="alert">
                        <?= htmlspecialchars($loginError) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="login.php" class="auth-form">
                    <div>
                        <label for="email" class="form-label">Email address</label>
                        <input
                            type="email"
                            class="form-control auth-input"
                            id="email"
                            name="email"
                            placeholder="morgan@pulseadmin.com"
                            value="<?= htmlspecialchars($submittedEmail) ?>"
                            required
                        >
                    </div>

                    <div>
                        <div class="auth-label-row">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="#" class="auth-link">Forgot password?</a>
                        </div>
                        <input
                            type="password"
                            class="form-control auth-input"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <div class="auth-form-row">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Keep me signed in
                            </label>
                        </div>
                        <span class="auth-form-note">Protected workspace</span>
                    </div>

                    <button type="submit" class="btn btn-dark auth-submit">Sign In</button>
                </form>

                <p class="auth-footer">
                    Need an account?
                    <a href="#" class="auth-link">Contact your administrator</a>
                </p>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
