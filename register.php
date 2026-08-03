<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header("Location: " . (current_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
    exit;
}

$error = '';
$success = '';
$base = '';
$active = 'register';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 4) {
        $error = 'Password must be at least 4 characters.';
    } else {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, 's', $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That username is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role, full_name, email) VALUES (?, ?, 'student', ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $hash, $full_name, $email);

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Account created successfully. Please sign in to continue.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Account — eHostel Resident Portal</title>
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
        <span class="section-label">STUDENT REGISTRATION</span>
        <h1 class="serif-heading" style="font-size:2.8rem;margin-bottom:0.75rem;">Create Your Account</h1>
        <p style="font-size:1.02rem;color:var(--text-secondary);">
            Register as a student to apply for room accommodation, track status, and view announcements.
        </p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-card-form">
            <div style="margin-bottom:1.75rem;">
                <div style="font-family:var(--font-serif);font-size:2rem;color:var(--primary-dark);font-weight:500;margin-bottom:0.25rem;">Student Registration</div>
                <p style="font-size:0.88rem;color:var(--text-muted);margin:0;">Enter your details to create a student account.</p>
            </div>

            <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:1.5rem;"><?= h($error) ?></div><?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" style="margin-bottom:1.5rem;">
                    <?= h($success) ?> <a href="login.php" style="font-weight:700;color:inherit;margin-left:0.5rem;">Sign In Now &rarr;</a>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST" action="register.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" class="input-luxury" required value="<?= h($_POST['full_name'] ?? '') ?>" placeholder="e.g. Kavindu Perera">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" class="input-luxury" required value="<?= h($_POST['username'] ?? '') ?>" placeholder="Unique username">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="input-luxury" value="<?= h($_POST['email'] ?? '') ?>" placeholder="student@univ.ac.lk">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" class="input-luxury" required placeholder="Min 4 characters">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="input-luxury" required placeholder="Re-enter password">
                    </div>
                </div>

                <button type="submit" class="btn btn-luxury btn-accent btn-block" style="margin-top:1.25rem;">Sign Up</button>
            </form>
            <?php endif; ?>

            <div style="margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--border);text-align:center;font-size:0.88rem;color:var(--text-muted);">
                Already have an account? <a href="login.php" style="font-weight:600;color:var(--primary-dark);">Login &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
