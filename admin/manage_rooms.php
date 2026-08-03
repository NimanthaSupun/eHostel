<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$rooms = mysqli_query($conn, "SELECT r.room_id, r.room_number, r.floor, r.room_type, r.capacity,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id) AS total_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='occupied') AS occupied_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='vacant') AS vacant_beds
                               FROM rooms r
                               WHERE r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%'
                               ORDER BY r.floor, r.room_number");

$base = '../';
$active = 'rooms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Rooms — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ROOM &amp; BED MANAGEMENT</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Hostel Rooms &amp; Beds</h1>
        <p>Configure room numbers, capacities, and monitor real-time occupancy.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:0.75rem;">Fixed Room Configuration</h3>
    <p style="color:var(--text-muted);margin-bottom:1.25rem;">The hostel layout is fixed to 20 rooms: 10 single-bed rooms on the first floor and 10 double-bed rooms on the second floor.</p>
    <table>
        <thead>
            <tr>
                <th>Room No.</th>
                <th>Floor</th>
                <th>Room Configuration</th>
                <th>Capacity</th>
                <th>Occupied Beds</th>
                <th>Vacant Beds</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($rooms) > 0): while ($r = mysqli_fetch_assoc($rooms)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($r['room_number']) ?></td>
                <td>Floor <?= (int) $r['floor'] ?></td>
                <td><?= $r['room_type'] === 'single' ? 'Single Bed Room' : 'Double Bed Room' ?></td>
                <td><?= (int) $r['capacity'] ?> Bed(s)</td>
                <td><?= (int) $r['occupied_beds'] ?> Bed(s)</td>
                <td><?= (int) $r['vacant_beds'] ?> Bed(s)</td>
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
            <tr><td colspan="7" class="empty-state">No rooms configured yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
