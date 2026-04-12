
<section class="auth-panel">
    <div class="auth-card">
        <div class="auth-card-header">
            <span class="brand-mark"><?= htmlspecialchars($_SESSION['brand_name'])[0] ?></span>
            <div>
                <p class="auth-overline">Welcome back</p>
                <h2>Sign in to <?= htmlspecialchars($_SESSION['brand_name']) ?></h2>
            </div>
        </div>

        <?php if ($loginError !== ''): ?>
            <div class="alert alert-info auth-alert" role="alert">
                <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?action=login_submit" class="auth-form">
            <div>
                <label for="user_name" class="form-label">User Account</label>
                <select
                    class="form-control auth-input"
                    id="user_name"
                    name="user_name"
                    required
                >
                    <option value="">Select your account...</option>
                    <?php 
                        $users = $result['users'] ?? [];
                    
                        foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_name']) ?>" >
                                <?= htmlspecialchars($user['user_name']) ?> (<?= htmlspecialchars($user['user_name']) ?>)
                            </option>
                    <?php endforeach; ?>
                </select>
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