<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header("Location: " . (current_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
    exit;
}

$error = '';
$base = '';
$active = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, username, password, role, full_name FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — eHostel Resident Portal</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
<style>
.auth-page-hero {
    text-align: center;
    padding: 2.25rem 1.5rem 1.25rem;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
}

.auth-container {
    max-width: 860px;
    margin: 2rem auto 3rem;
    padding: 0 1.25rem;
}

.auth-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.auth-card-form {
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

@media (max-width: 850px) {
    .auth-card-form { padding: 2.5rem 1.75rem; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/public_nav.php'; ?>

<section class="auth-page-hero">
    <div style="max-width:560px;margin:0 auto;">
        <span class="section-label">AUTHENTICATION PORTAL</span>
        <h1 class="serif-heading" style="font-size:2.8rem;margin-bottom:0.75rem;">Welcome to eHostel</h1>
        <p style="font-size:1.02rem;color:var(--text-secondary);">
            Sign in to access your student room allocation details, submit applications, or manage hostel premises.
        </p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-card-form">
            <div style="margin-bottom:1.75rem;">
                <div style="font-family:var(--font-serif);font-size:2rem;color:var(--primary-dark);font-weight:500;margin-bottom:0.25rem;">Sign In</div>
                <p style="font-size:0.88rem;color:var(--text-muted);margin:0;">Enter your account credentials to log in.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom:1.5rem;"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="input-luxury" required autofocus value="<?= h($_POST['username'] ?? '') ?>" placeholder="Your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="input-luxury" required placeholder="Your password">
                </div>
                <button type="submit" class="btn btn-luxury btn-accent btn-block" style="margin-top:1.25rem;">Login</button>
            </form>

            <div style="margin-top:1.75rem;padding:1rem 1.2rem;background:var(--bg-secondary);border-radius:var(--radius-md);font-size:0.83rem;color:var(--text-secondary);border:1px solid var(--border);">
                <strong style="color:var(--primary-dark);">Demo Account:</strong><br>
                Username <code style="background:var(--surface);padding:0.15rem 0.4rem;border-radius:3px;">uoc</code> &middot; Password <code style="background:var(--surface);padding:0.15rem 0.4rem;border-radius:3px;">uoc</code>
            </div>

            <div style="margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--border);text-align:center;font-size:0.88rem;color:var(--text-muted);">
                Don't have an account? <a href="register.php" style="font-weight:600;color:var(--primary-dark);">Sign Up &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
