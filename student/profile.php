<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $contact   = trim($_POST['contact_no'] ?? '');
    $address   = trim($_POST['address'] ?? '');

    if ($full_name === '') {
        $error = 'Full name is required.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, email=?, contact_no=?, address=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $contact, $address, $uid);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['full_name'] = $full_name;
            $success = 'Profile details updated successfully.';
        } else {
            $error = 'Could not update profile details.';
        }
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

$base = '../';
$active = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">STUDENT ACCOUNT</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">My Profile</h1>
        <p>Update your personal information and contact details.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card" style="max-width:680px;">
    <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
        <div style="width:68px;height:68px;border-radius:50%;background:var(--primary);color:var(--accent);border:2px solid var(--accent);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:600;">
            <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
            <h3 class="serif-heading" style="margin:0;font-size:1.6rem;"><?= h($user['full_name']) ?></h3>
            <span style="font-size:0.85rem;color:var(--text-muted);">Registered Student &middot; <?= h($user['reg_no'] ?? 'No Reg No') ?></span>
        </div>
    </div>

    <form method="POST" action="profile.php">
        <div class="form-group">
            <label>Username (System Identifier)</label>
            <input type="text" class="input-luxury" value="<?= h($user['username']) ?>" disabled style="background:var(--bg-secondary);">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="input-luxury" required value="<?= h($user['full_name']) ?>">
            </div>
            <div class="form-group">
                <label>Registration No. (System Generated)</label>
                <input type="text" class="input-luxury" value="<?= h($user['reg_no'] ?? 'Pending') ?>" disabled style="background:var(--bg-secondary);">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>NIC No.</label>
                <input type="text" class="input-luxury" value="<?= h($user['nic_no'] ?? 'Not Set') ?>" disabled style="background:var(--bg-secondary);">
            </div>
            <div class="form-group"></div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="input-luxury" value="<?= h($user['email']) ?>">
            </div>
            <div class="form-group">
                <label for="contact_no">Contact No.</label>
                <input type="tel" id="contact_no" name="contact_no" class="input-luxury" value="<?= h($user['contact_no']) ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="address">Permanent Address</label>
            <textarea id="address" name="address" class="input-luxury" rows="3"><?= h($user['address']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-luxury btn-accent">Save Profile Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
