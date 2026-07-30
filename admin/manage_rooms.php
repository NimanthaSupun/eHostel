<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

// Add room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_number = trim($_POST['room_number']);
    $room_type = $_POST['room_type'] === 'single' ? 'single' : 'shared';
    $capacity = max(1, (int) $_POST['capacity']);

    $stmt = mysqli_prepare($conn, "INSERT INTO rooms (room_number, room_type, capacity) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $room_number, $room_type, $capacity);
    if (mysqli_stmt_execute($stmt)) {
        $room_id = mysqli_insert_id($conn);
        // auto create beds
        for ($i = 1; $i <= $capacity; $i++) {
            $bed_number = $room_number . '-' . $i;
            $bstmt = mysqli_prepare($conn, "INSERT INTO beds (room_id, bed_number) VALUES (?, ?)");
            mysqli_stmt_bind_param($bstmt, "is", $room_id, $bed_number);
            mysqli_stmt_execute($bstmt);
        }
        $success = "Room $room_number added with $capacity bed(s).";
    } else {
        $error = 'Could not add room. Room number may already exist.';
    }
}

// Delete room
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM rooms WHERE room_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Room deleted successfully.'; }
    else { $error = 'Could not delete room.'; }
}

$rooms = mysqli_query($conn, "SELECT r.*,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id) AS total_beds,
                                (SELECT COUNT(*) FROM beds b WHERE b.room_id = r.room_id AND b.status='occupied') AS occupied_beds
                               FROM rooms r ORDER BY r.room_number");

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

<div class="card" style="max-width:680px;">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">Add New Room</h3>
    <form method="POST" action="manage_rooms.php">
        <div class="form-row">
            <div class="form-group">
                <label for="room_number">Room Number *</label>
                <input type="text" id="room_number" name="room_number" class="input-luxury" required placeholder="e.g. C-301">
            </div>
            <div class="form-group">
                <label for="room_type">Room Type</label>
                <select id="room_type" name="room_type" class="input-luxury">
                    <option value="shared">Shared Room</option>
                    <option value="single">Single Room</option>
                </select>
            </div>
            <div class="form-group">
                <label for="capacity">Capacity (Beds)</label>
                <input type="number" id="capacity" name="capacity" class="input-luxury" value="2" min="1" max="6" required>
            </div>
        </div>
        <button type="submit" name="add_room" class="btn btn-luxury btn-accent">Add Room &amp; Auto-Generate Beds</button>
    </form>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.5rem;">All Configured Rooms</h3>
    <table>
        <thead>
            <tr>
                <th>Room No.</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>Occupied</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($rooms) > 0): while ($r = mysqli_fetch_assoc($rooms)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($r['room_number']) ?></td>
                <td><?= h(ucfirst($r['room_type'])) ?></td>
                <td><?= $r['total_beds'] ?> bed(s)</td>
                <td><?= $r['occupied_beds'] ?> bed(s)</td>
                <td>
                    <?php if ($r['occupied_beds'] == $r['total_beds']): ?>
                        <span class="badge badge-danger">Fully Occupied</span>
                    <?php elseif ($r['occupied_beds'] == 0): ?>
                        <span class="badge badge-success">Vacant</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Partial Occupancy</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-sm btn-danger" href="manage_rooms.php?delete=<?= $r['room_id'] ?>" onclick="return confirm('Delete room <?= h($r['room_number']) ?> and associated bed records?')">Delete Room</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="6" class="empty-state">No rooms configured yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
