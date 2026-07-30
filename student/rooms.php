<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$rooms = mysqli_query($conn, "SELECT r.*,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id) AS total_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='occupied') AS occupied_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='vacant') AS vacant_beds
                               FROM rooms r ORDER BY r.room_number");

$base = '../';
$active = 'st_rooms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room Availability — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ACCOMMODATION CATALOG</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Hostel Room &amp; Bed Status</h1>
        <p>Explore configured room units, bed capacities, and real-time vacant bed counts.</p>
    </div>
    <div>
        <a href="apply.php" class="btn btn-luxury btn-accent">Apply For Hostel Bed</a>
    </div>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.5rem;">Current Room Availability Directory</h3>

    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Configuration</th>
                <th>Capacity</th>
                <th>Occupied Beds</th>
                <th>Vacant Beds</th>
                <th>Occupancy Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($rooms && mysqli_num_rows($rooms) > 0): while ($r = mysqli_fetch_assoc($rooms)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);font-size:1.05rem;"><?= h($r['room_number']) ?></td>
                <td><?= h(ucfirst($r['room_type'])) ?> Room</td>
                <td><?= $r['total_beds'] ?> Bed(s)</td>
                <td><?= $r['occupied_beds'] ?> Bed(s)</td>
                <td style="font-weight:700;color:var(--success);"><?= $r['vacant_beds'] ?> Bed(s) Available</td>
                <td>
                    <?php if ($r['occupied_beds'] == $r['total_beds']): ?>
                        <span class="badge badge-danger">Fully Occupied</span>
                    <?php elseif ($r['occupied_beds'] == 0): ?>
                        <span class="badge badge-success">Fully Vacant</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Partial Occupancy</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="6" class="empty-state">No rooms registered yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
