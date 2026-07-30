<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];

// Latest application
$stmt = mysqli_prepare($conn, "SELECT * FROM applications WHERE user_id = ? ORDER BY application_id DESC LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$application = mysqli_stmt_get_result($stmt)->fetch_assoc();

// Allocation info (if any)
$allocation = null;
if ($application && $application['status'] === 'approved') {
    $stmt2 = mysqli_prepare($conn, "SELECT al.allocation_date, b.bed_number, r.room_number, r.room_type
                                     FROM allocations al
                                     JOIN beds b ON al.bed_id = b.bed_id
                                     JOIN rooms r ON b.room_id = r.room_id
                                     WHERE al.user_id = ? ORDER BY al.allocation_id DESC LIMIT 1");
    mysqli_stmt_bind_param($stmt2, "i", $uid);
    mysqli_stmt_execute($stmt2);
    $allocation = mysqli_stmt_get_result($stmt2)->fetch_assoc();
}

// Latest 3 announcements
$anns = mysqli_query($conn, "SELECT title, content, posted_date FROM announcements ORDER BY posted_date DESC LIMIT 3");

$base = '../';
$active = 'dash';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">STUDENT DASHBOARD</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Welcome, <?= h($_SESSION['full_name']) ?></h1>
        <p>Overview of your accommodation status and announcements.</p>
    </div>
    <div>
        <a href="apply.php" class="btn btn-luxury btn-accent">Apply For Hostel</a>
    </div>
</div>

<div class="card-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="num">
            <?php if ($application): ?>
                <span class="badge badge-<?= $application['status'] === 'approved' ? 'success' : ($application['status'] === 'rejected' ? 'danger' : 'warning') ?>" style="font-size:0.9rem;padding:0.35rem 0.85rem;">
                    <?= h(strtoupper($application['status'])) ?>
                </span>
            <?php else: ?>
                <span class="badge badge-muted" style="font-size:0.9rem;">NOT APPLIED</span>
            <?php endif; ?>
        </div>
        <div class="label">Application Status</div>
    </div>
    <div class="stat-card">
        <div class="num" style="font-size:2.2rem;"><?= $allocation ? h($allocation['room_number']) : '—' ?></div>
        <div class="label">Allocated Room</div>
    </div>
    <div class="stat-card">
        <div class="num" style="font-size:2.2rem;"><?= $allocation ? h($allocation['bed_number']) : '—' ?></div>
        <div class="label">Allocated Bed</div>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;">Accommodation Details</h3>
        <span class="badge badge-muted">Resident Overview</span>
    </div>

    <?php if (!$application): ?>
        <p style="color:var(--text-secondary);font-size:1rem;margin-bottom:1.5rem;">
            You haven't submitted a hostel application for the current academic session yet.
        </p>
        <a href="apply.php" class="btn btn-luxury btn-filled">Start New Application &rarr;</a>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1.5rem;background:var(--bg-primary);padding:1.5rem;border-radius:var(--radius-md);border:1px solid var(--border);">
            <div>
                <span style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;display:block;">Preferred Room Type</span>
                <strong style="font-size:1.1rem;color:var(--primary-dark);"><?= h(ucfirst($application['preferred_room_type'])) ?></strong>
            </div>
            <div>
                <span style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;display:block;">Submitted Date</span>
                <strong style="font-size:1.1rem;color:var(--primary-dark);"><?= date('d M Y', strtotime($application['applied_date'])) ?></strong>
            </div>
            <div>
                <span style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;display:block;">Review Status</span>
                <strong style="font-size:1.1rem;color:var(--primary-dark);"><?= h(ucfirst($application['status'])) ?></strong>
            </div>
        </div>

        <?php if ($allocation): ?>
            <div class="alert alert-success" style="margin-top:1.5rem;">
                🎉 <strong>Congratulations!</strong> Bed allocated: Room <strong><?= h($allocation['room_number']) ?></strong>, Bed <strong><?= h($allocation['bed_number']) ?></strong> (<?= h(ucfirst($allocation['room_type'])) ?>) allocated on <?= date('d M Y', strtotime($allocation['allocation_date'])) ?>.
            </div>
        <?php elseif ($application['status'] === 'pending'): ?>
            <p style="color:var(--text-muted);margin-top:1.25rem;font-size:0.92rem;">
                ⏳ Your application is currently under review by the university warden and administration office. You will be notified here once a bed is assigned.
            </p>
        <?php elseif ($application['status'] === 'rejected'): ?>
            <div class="alert alert-error" style="margin-top:1.5rem;">
                Your application was not approved. <?= isset($application['remarks']) && $application['remarks'] ? 'Remarks: ' . h($application['remarks']) : '' ?>
            </div>
            <a href="apply.php" class="btn btn-luxury btn-outline" style="margin-top:0.5rem;">Submit New Application</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;">📢 Recent Announcements</h3>
        <a href="announcements.php" style="font-size:0.82rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;">View All &rarr;</a>
    </div>

    <?php if ($anns && mysqli_num_rows($anns) > 0): ?>
        <?php while ($a = mysqli_fetch_assoc($anns)): ?>
            <div style="padding:1rem 0;border-bottom:1px solid var(--border);">
                <h4 style="color:var(--primary-dark);margin-bottom:0.35rem;"><?= h($a['title']) ?></h4>
                <p style="margin:0 0 0.5rem;color:var(--text-secondary);font-size:0.92rem;line-height:1.6;"><?= h($a['content']) ?></p>
                <span style="font-size:0.75rem;color:var(--text-muted);font-weight:500;"><?= date('d M Y, h:i A', strtotime($a['posted_date'])) ?></span>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-state">No announcements posted yet.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
