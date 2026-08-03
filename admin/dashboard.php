<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$totalStudents = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='student'"))[0];
$pendingApps   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status='pending'"))[0];

$singleOccupied = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds b JOIN rooms r ON b.room_id = r.room_id WHERE r.room_type='single' AND (r.room_number LIKE 'F1/%') AND b.status='occupied'"))[0];
$singleVacant   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds b JOIN rooms r ON b.room_id = r.room_id WHERE r.room_type='single' AND (r.room_number LIKE 'F1/%') AND b.status='vacant'"))[0];
$doubleOccupied = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds b JOIN rooms r ON b.room_id = r.room_id WHERE r.room_type='shared' AND (r.room_number LIKE 'F2/%') AND b.status='occupied'"))[0];
$doubleVacant   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds b JOIN rooms r ON b.room_id = r.room_id WHERE r.room_type='shared' AND (r.room_number LIKE 'F2/%') AND b.status='vacant'"))[0];

$recentApps = mysqli_query($conn, "SELECT ap.application_id, u.full_name, ap.preferred_room_type, ap.applied_date, ap.status
                                    FROM applications ap JOIN users u ON ap.user_id = u.user_id
                                    ORDER BY ap.application_id DESC LIMIT 5");

$base = '../';
$active = 'dash';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ADMINISTRATION PORTAL</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Admin Overview</h1>
        <p>Operational summary of hostel capacity, student applications, and bed allocation.</p>
    </div>
</div>

<div class="card-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="num"><?= $totalStudents ?></div>
        <div class="label">Registered Students</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:var(--warning);"><?= $pendingApps ?></div>
        <div class="label">Pending Applications</div>
    </div>
</div>

<div class="card-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="num"><?= $singleOccupied ?></div>
        <div class="label">Single Rooms Occupied</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:var(--success);"><?= $singleVacant ?></div>
        <div class="label">Single Rooms Vacant</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $doubleOccupied ?></div>
        <div class="label">Double Rooms Occupied</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:var(--success);"><?= $doubleVacant ?></div>
        <div class="label">Double Rooms Vacant</div>
    </div>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.6rem;margin-bottom:1.25rem;">Quick Administrative Tasks</h3>
    <div class="card-grid">
        <a href="manage_students.php" class="btn btn-luxury btn-outline btn-block">🧑‍🎓 Manage Students</a>
        <a href="manage_rooms.php" class="btn btn-luxury btn-outline btn-block">🛏️ Manage Rooms &amp; Beds</a>
        <a href="applications.php" class="btn btn-luxury btn-outline btn-block">📄 Review Applications</a>
        <a href="announcements.php" class="btn btn-luxury btn-outline btn-block">📢 Post Announcement</a>
        <a href="reports.php" class="btn btn-luxury btn-outline btn-block">📊 View Occupancy Reports</a>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;margin:0;">Recent Applications</h3>
        <a href="applications.php" style="font-size:0.82rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;">Review All Applications &rarr;</a>
    </div>

    <?php if (mysqli_num_rows($recentApps) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Room Type</th>
                <th>Applied Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($recentApps)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($r['full_name']) ?></td>
                <td><?= h(ucfirst($r['preferred_room_type'])) ?></td>
                <td><?= date('d M Y', strtotime($r['applied_date'])) ?></td>
                <td>
                    <span class="badge badge-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= h(strtoupper($r['status'])) ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="empty-state">No applications submitted yet.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
