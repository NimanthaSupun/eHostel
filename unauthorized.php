<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access Denied — eHostel</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box" style="text-align:center;">
        <div class="brand">🚫 Access Denied</div>
        <p style="color:var(--text-muted)">You are not authorized to view that page.</p>
        <?php if (is_logged_in()): ?>
            <a class="btn btn-block" href="<?= current_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php' ?>">Go to my Dashboard</a>
        <?php else: ?>
            <a class="btn btn-block" href="login.php">Go to Login</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
