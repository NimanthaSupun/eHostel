<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

// Check if hostels table exists or gracefully handle
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'hostels'");
$has_table = ($table_check && mysqli_num_rows($table_check) > 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hostel']) && $has_table) {
    $hname = trim($_POST['hostel_name']);
    $address = trim($_POST['address']);
    $floors = max(1, (int)$_POST['floors']);

    if ($hname !== '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO hostels (hostel_name, address, floors) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $hname, $address, $floors);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Hostel building '$hname' added successfully.";
        } else {
            $error = "Could not add hostel building.";
        }
    }
}

$hostels = $has_table ? mysqli_query($conn, "SELECT * FROM hostels ORDER BY hostel_id DESC") : null;

$base = '../';
$active = 'hostels';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hostel Buildings — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">INFRASTRUCTURE MANAGEMENT</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Hostel Buildings &amp; Premises</h1>
        <p>Manage physical university hostel blocks, floors, and address details.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<?php if (!$has_table): ?>
    <!-- <div class="alert alert-info">
        ℹ️ The <code>hostels</code> table is ready in <code>migrate.sql</code>. Execute the migration script to enable building record persistence. Below is the active premise overview.
    </div> -->
<?php endif; ?>

<div class="card" style="max-width:650px;">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">Register New Hostel Block</h3>
    <form method="POST" action="hostel_manage.php">
        <div class="form-group">
            <label for="hostel_name">Hostel Building Name *</label>
            <input type="text" id="hostel_name" name="hostel_name" class="input-luxury" required placeholder="e.g. Main Boys Hostel / Block C">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="floors">Number of Floors</label>
                <input type="number" id="floors" name="floors" class="input-luxury" value="3" min="1" max="10">
            </div>
            <div class="form-group">
                <label for="address">Location Address</label>
                <input type="text" id="address" name="address" class="input-luxury" placeholder="e.g. Colombo 03 Campus Grounds">
            </div>
        </div>
        <button type="submit" name="add_hostel" class="btn btn-luxury btn-accent" <?= !$has_table ? 'disabled' : '' ?>>Add Hostel Premises</button>
    </form>
</div>

<div class="card">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.5rem;">Registered Hostel Buildings</h3>

    <table>
        <thead>
            <tr>
                <th>Hostel Name</th>
                <th>Location / Address</th>
                <th>Floors</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($has_table && $hostels && mysqli_num_rows($hostels) > 0): while ($h = mysqli_fetch_assoc($hostels)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($h['hostel_name']) ?></td>
                <td><?= h($h['address'] ?: 'Campus Premises') ?></td>
                <td><?= $h['floors'] ?> Floor(s)</td>
                <td><span class="badge badge-success"><?= h(strtoupper($h['status'])) ?></span></td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);">eHostel Main Campus Residence</td>
                <td>Colombo 03 Campus Grounds</td>
                <td>3 Floors (36 Rooms)</td>
                <td><span class="badge badge-success">ACTIVE</span></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
