<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$totalStudents = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='student'"))[0];
$totalApps     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM applications"))[0];
$pending       = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status='pending'"))[0];
$approved      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status='approved'"))[0];
$rejected      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status='rejected'"))[0];
$totalRooms    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM rooms"))[0];
$totalBeds     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds"))[0];
$occupiedBeds  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beds WHERE status='occupied'"))[0];
$vacantBeds    = $totalBeds - $occupiedBeds;

$roomBreakdown = mysqli_query($conn, "SELECT r.room_number, r.room_type,
                                        (SELECT COUNT(*) FROM beds b WHERE b.room_id=r.room_id) total,
                                        (SELECT COUNT(*) FROM beds b WHERE b.room_id=r.room_id AND b.status='occupied') occ
                                       FROM rooms r ORDER BY r.room_number");

$base = '../';
$active = 'reports';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ANALYTICS &amp; STATS</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Summary Reports</h1>
        <p>Comprehensive breakdown of hostel occupancy and application metrics.</p>
    </div>
</div>

<div class="card-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="num"><?= $totalStudents ?></div>
        <div class="label">Registered Students</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $totalRooms ?></div>
        <div class="label">Total Configured Rooms</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $occupiedBeds ?> / <?= $totalBeds ?></div>
        <div class="label">Beds Occupied</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:var(--success);"><?= $vacantBeds ?></div>
        <div class="label">Vacant Beds</div>
    </div>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">Applications Overview</h3>
    <table>
        <thead>
            <tr>
                <th>Total Applications</th>
                <th>Pending Review</th>
                <th>Approved &amp; Allocated</th>
                <th>Rejected</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);font-size:1.1rem;"><?= $totalApps ?></td>
                <td><span class="badge badge-warning"><?= $pending ?> Pending</span></td>
                <td><span class="badge badge-success"><?= $approved ?> Approved</span></td>
                <td><span class="badge badge-danger"><?= $rejected ?> Rejected</span></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">Room Occupancy Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Occupied Beds</th>
                <th>Total Beds</th>
                <th>Occupancy Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($roomBreakdown)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($r['room_number']) ?></td>
                <td><?= h(ucfirst($r['room_type'])) ?></td>
                <td><?= $r['occ'] ?> bed(s)</td>
                <td><?= $r['total'] ?> bed(s)</td>
                <td>
                    <?php if ($r['occ'] == $r['total']): ?>
                        <span class="badge badge-danger">Fully Occupied</span>
                    <?php elseif ($r['occ'] == 0): ?>
                        <span class="badge badge-success">Vacant</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Partial Occupancy</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
