<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$userId = (int) ($_GET['id'] ?? 0);
$error = '';

if ($userId <= 0) {
    header('Location: manage_students.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT u.*, a.application_id, a.preferred_room_type, a.nic_no AS app_nic, a.address AS app_address, a.academic_year AS app_year, a.status AS app_status, a.applied_date FROM users u LEFT JOIN applications a ON a.user_id = u.user_id AND a.application_id = (SELECT MAX(application_id) FROM applications WHERE user_id = u.user_id) WHERE u.user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$student = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$student) {
    header('Location: manage_students.php');
    exit;
}

$badgeClass = $student['role'] === 'student' ? 'badge badge-success' : 'badge badge-warning';

$base = '../';
$active = 'students';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Details — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">STUDENT RECORD</span>
        <h1 class="serif-heading" style="font-size:2.4rem;"><?= h($student['full_name']) ?></h1>
        <p>Detailed view of the student account and application information.</p>
    </div>
    <div>
        <a href="manage_students.php" class="btn btn-luxury btn-outline">&larr; Back to Students</a>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<div class="card" style="max-width:900px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h3 class="serif-heading" style="font-size:1.5rem;margin:0;">Student Profile</h3>
            <p style="margin:0.25rem 0 0;color:var(--text-muted);">Student ID: <?= h($student['student_id'] ?: 'Pending') ?></p>
        </div>
        <span class="<?= h($badgeClass) ?>"><?= h(strtoupper($student['role'])) ?></span>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" class="input-luxury" value="<?= h($student['full_name']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="input-luxury" value="<?= h($student['username']) ?>" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="input-luxury" value="<?= h($student['email'] ?: '—') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Contact No.</label>
            <input type="text" class="input-luxury" value="<?= h($student['contact_no'] ?: '—') ?>" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>NIC No.</label>
            <input type="text" class="input-luxury" value="<?= h($student['app_nic'] ?: $student['nic_no'] ?: '—') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Academic Year</label>
            <input type="text" class="input-luxury" value="<?= h($student['app_year'] ?: $student['academic_year'] ?: '—') ?>" disabled>
        </div>
    </div>

    <div class="form-group">
        <label>Address</label>
        <textarea class="input-luxury" rows="3" disabled><?= h($student['app_address'] ?: $student['address'] ?: '—') ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Application Status</label>
            <input type="text" class="input-luxury" value="<?= h(ucfirst($student['app_status'] ?: 'No application')) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Preferred Room Type</label>
            <input type="text" class="input-luxury" value="<?= h($student['preferred_room_type'] ? ucfirst($student['preferred_room_type']) : '—') ?>" disabled>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
