<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $app_id = (int) $_POST['approve_id'];
    $bed_id = (int) $_POST['bed_id'];

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, "SELECT applications.user_id AS app_user_id, users.student_id FROM applications JOIN users ON users.user_id = applications.user_id WHERE applications.application_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $app_id);
        mysqli_stmt_execute($stmt);
        $app = mysqli_stmt_get_result($stmt)->fetch_assoc();

        if (!$app) throw new Exception('Application not found.');

        $upd = mysqli_prepare($conn, "UPDATE applications SET status='approved' WHERE application_id=?");
        mysqli_stmt_bind_param($upd, "i", $app_id);
        mysqli_stmt_execute($upd);

        $bedUpd = mysqli_prepare($conn, "UPDATE beds SET status='occupied' WHERE bed_id=? AND status='vacant'");
        mysqli_stmt_bind_param($bedUpd, "i", $bed_id);
        mysqli_stmt_execute($bedUpd);
        if (mysqli_stmt_affected_rows($bedUpd) === 0) throw new Exception('Selected bed is no longer available.');

        $alloc = mysqli_prepare($conn, "INSERT INTO allocations (application_id, user_id, bed_id, allocation_date) VALUES (?, ?, ?, CURDATE())");
        mysqli_stmt_bind_param($alloc, "iii", $app_id, $app['app_user_id'], $bed_id);
        mysqli_stmt_execute($alloc);

        if (empty($app['student_id'])) {
            $newStudentId = generate_student_id($conn);
            $studentIdStmt = mysqli_prepare($conn, "UPDATE users SET student_id=? WHERE user_id=?");
            mysqli_stmt_bind_param($studentIdStmt, "si", $newStudentId, $app['app_user_id']);
            mysqli_stmt_execute($studentIdStmt);
        }

        mysqli_commit($conn);
        $success = 'Application approved and bed allocated successfully.';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}

if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $stmt = mysqli_prepare($conn, "UPDATE applications SET status='rejected' WHERE application_id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Application status set to rejected.'; }
}

$apps = mysqli_query($conn, "SELECT ap.*, u.full_name, u.email, u.student_id, u.nic_no, u.address, u.academic_year, u.contact_no, u.distance_km
                              FROM applications ap JOIN users u ON ap.user_id = u.user_id
                              ORDER BY FIELD(ap.status,'pending','approved','rejected'), ap.application_id DESC");

$vacantBeds = mysqli_query($conn, "SELECT b.bed_id, b.bed_number, r.room_number, r.room_type
                                    FROM beds b
                                    JOIN rooms r ON b.room_id = r.room_id
                                    LEFT JOIN allocations a ON a.bed_id = b.bed_id
                                    WHERE b.status='vacant' AND a.bed_id IS NULL
                                      AND (r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%')
                                    ORDER BY r.room_number, b.bed_number");
$bedOptions = [];
while ($b = mysqli_fetch_assoc($vacantBeds)) { $bedOptions[] = $b; }

$base = '../';
$active = 'apps';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Applications — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">APPLICATION REVIEW</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Student Applications</h1>
        <p>Review student requests, allocate vacant beds upon approval, or reject requests.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Student Details</th>
                <th>Student ID</th>
                <th>Distance from Campus (km)</th>
                <th>Preferred Type</th>
                <th>Applied Date</th>
                <th>Status</th>
                <th>Action &amp; Allocation</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($apps) > 0): while ($a = mysqli_fetch_assoc($apps)): ?>
            <tr>
                <td>
                    <strong style="color:var(--primary-dark);"><?= h($a['full_name']) ?></strong><br>
                    <span style="font-size:0.78rem;color:var(--text-muted);">NIC: <?= h($a['nic_no'] ?: '—') ?></span><br>
                    <span style="font-size:0.78rem;color:var(--text-muted);">Year: <?= h($a['academic_year'] ?: '—') ?></span><br>
                    <span style="font-size:0.78rem;color:var(--text-muted);">Address: <?= h($a['address'] ?: '—') ?></span>
                </td>
                <td><?= h($a['student_id'] ?: 'Pending') ?></td>
                <td><?= h($a['distance_km'] !== null && $a['distance_km'] !== '' ? $a['distance_km'] : '—') ?></td>
                <td><?= h(ucfirst($a['preferred_room_type'])) ?></td>
                <td><?= date('d M Y', strtotime($a['applied_date'])) ?></td>
                <td>
                    <span class="badge badge-<?= $a['status'] === 'approved' ? 'success' : ($a['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= h(strtoupper($a['status'])) ?>
                    </span>
                </td>
                <td>
                    <?php if ($a['status'] === 'pending'): ?>
                        <?php if (count($bedOptions) > 0): ?>
                        <form method="POST" action="applications.php" style="display:flex;flex-direction:column;align-items:stretch;gap:0.45rem;max-width:260px;">
                            <input type="hidden" name="approve_id" value="<?= $a['application_id'] ?>">
                            <select name="bed_id" required class="input-luxury" style="padding:0.4rem 0.6rem;font-size:0.8rem;width:100%;">
                                <option value="">Select Vacant Bed</option>
                                <?php foreach ($bedOptions as $b): ?>
                                    <option value="<?= $b['bed_id'] ?>">Room <?= h($b['room_number']) ?> — Bed <?= h($b['bed_number']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-luxury btn-accent">Approve</button>
                        </form>
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:var(--danger);font-weight:600;">No Vacant Beds</span>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-danger" style="margin-top:0.25rem;" href="applications.php?reject=<?= $a['application_id'] ?>" onclick="return confirm('Reject this application?')">Reject</a>
                    <?php else: ?>
                        <span style="font-size:0.85rem;color:var(--text-muted);">Process Complete</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="empty-state">No student applications submitted yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
