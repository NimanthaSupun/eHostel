<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

function normalize_room_search($input) {
    $clean = strtoupper(trim($input));

    if (preg_match('/^F([12])\/(\d{2})\/(\d{2})$/', $clean, $matches)) {
        $room = sprintf('F%s/%s', $matches[1], $matches[2]);
        $bedNo = (int) $matches[3];
        $bedLabel = 'A' . $bedNo;
        return $room . '/' . $bedLabel;
    }

    return $clean;
}

$search = trim($_GET['q'] ?? '');
$searchTerm = normalize_room_search($search);
$roomFilter = '';
$detailBedFilter = '';
$detailRoomFilter = '';
$searchLookup = 'all';

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($conn, $searchTerm);

    if (preg_match('/^F([12])$/', $searchTerm, $matches)) {
        $roomFilter = ' AND r.floor = ' . (int) $matches[1];
        $detailRoomFilter = ' AND r.floor = ' . (int) $matches[1];
        $searchLookup = 'floor';
    } elseif (preg_match('/^F([12])\/\d{2}$/', $searchTerm)) {
        $roomFilter = " AND r.room_number = '{$searchEscaped}'";
        $detailRoomFilter = " AND r.room_number = '{$searchEscaped}'";
        $searchLookup = 'room';
    } elseif (preg_match('/^F([12])\/\d{2}\/[A-Z]\d+$/', $searchTerm)) {
        $roomFilter = " AND r.room_number = '" . substr($searchEscaped, 0, 5) . "'";
        $detailRoomFilter = " AND r.room_number = '" . substr($searchEscaped, 0, 5) . "'";
        $detailBedFilter = " AND b.bed_number = '{$searchEscaped}'";
        $searchLookup = 'bed';
    } else {
        $roomFilter = " AND r.room_number LIKE '%{$searchEscaped}%'";
        $detailRoomFilter = " AND r.room_number LIKE '%{$searchEscaped}%'";
        $searchLookup = 'text';
    }
}

$rooms = mysqli_query($conn, "SELECT r.room_id, r.room_number, r.floor, r.room_type, r.capacity,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id) AS total_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='occupied') AS occupied_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='vacant') AS vacant_beds
                               FROM rooms r
                               WHERE (r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%')" . $roomFilter . "
                               ORDER BY r.floor, r.room_number");

$searchDetails = [];
if ($search !== '') {
    $detailQuery = mysqli_query($conn, "SELECT r.room_number, r.floor, r.room_type, r.capacity,
                                            b.bed_id, b.bed_number, b.status AS bed_status,
                                            u.user_id, u.full_name, u.student_id, u.nic_no, u.email, u.contact_no, u.address, u.academic_year
                                       FROM rooms r
                                       JOIN beds b ON b.room_id = r.room_id
                                       LEFT JOIN allocations a ON a.bed_id = b.bed_id
                                       LEFT JOIN users u ON u.user_id = a.user_id
                                       WHERE (r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%')" . $detailRoomFilter . $detailBedFilter . "
                                       ORDER BY r.floor, r.room_number, b.bed_number");

    while ($row = mysqli_fetch_assoc($detailQuery)) {
        $searchDetails[$row['room_number']]['room_number'] = $row['room_number'];
        $searchDetails[$row['room_number']]['floor'] = $row['floor'];
        $searchDetails[$row['room_number']]['room_type'] = $row['room_type'];
        $searchDetails[$row['room_number']]['capacity'] = $row['capacity'];
        $searchDetails[$row['room_number']]['beds'][] = $row;
    }
}

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
    <form method="GET" action="manage_rooms.php" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;margin-bottom:1.5rem;">
        <input type="text" name="q" class="input-luxury" placeholder="Search by floor (F1), room (F1/01), or bed (F1/01/A1)" value="<?= h($search) ?>" style="max-width:420px;">
        <button type="submit" class="btn btn-luxury btn-filled btn-sm">Search Room</button>
        <?php if ($search): ?>
            <a href="manage_rooms.php" class="btn btn-luxury btn-outline btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>

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

<?php if ($search !== ''): ?>
<div class="card" style="margin-top:1.5rem;">
    <h3 class="serif-heading" style="font-size:1.4rem;margin-bottom:1rem;">Occupancy Search Details</h3>

    <?php if ($searchDetails): ?>
        <?php foreach ($searchDetails as $roomNumber => $room): ?>
            <div style="border:1px solid var(--border);border-radius:var(--radius-md);padding:1rem;margin-bottom:1rem;background:var(--bg-secondary);">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.85rem;">
                    <div>
                        <strong style="color:var(--primary-dark);font-size:1rem;">Room <?= h($room['room_number']) ?></strong>
                        <div style="font-size:0.82rem;color:var(--text-muted);">Floor <?= (int) $room['floor'] ?> &middot; <?= $room['room_type'] === 'single' ? 'Single Bed Room' : 'Double Bed Room' ?></div>
                    </div>
                    <span class="badge badge-<?= $room['capacity'] == 1 ? 'success' : 'warning' ?>">Capacity <?= (int) $room['capacity'] ?></span>
                </div>

                <?php foreach ($room['beds'] as $bed): ?>
                    <div style="display:grid;grid-template-columns:140px 1fr;gap:0.75rem;padding:0.75rem 0;border-top:1px solid var(--border);">
                        <div style="font-weight:700;color:var(--primary-dark);">
                            <?= h($bed['bed_number']) ?>
                        </div>
                        <div>
                            <?php if ($bed['full_name']): ?>
                                <div><strong><?= h($bed['full_name']) ?></strong></div>
                                <div style="font-size:0.8rem;color:var(--text-muted);">
                                    Student ID: <?= h($bed['student_id'] ?: 'Pending') ?> &middot;
                                    NIC: <?= h($bed['nic_no'] ?: '—') ?>
                                </div>
                                <div style="font-size:0.8rem;color:var(--text-muted);">
                                    Email: <?= h($bed['email'] ?: '—') ?> &middot;
                                    Contact: <?= h($bed['contact_no'] ?: '—') ?>
                                </div>
                            <?php else: ?>
                                <span style="color:var(--success);font-weight:600;">Vacant Bed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:var(--text-muted);margin:0;">No room or bed details matched the search string.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
