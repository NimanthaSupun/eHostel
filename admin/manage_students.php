<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ? AND role = 'student'");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Student record deleted successfully.'; }
    else { $error = 'Could not delete student record.'; }
}

// Update (inline edit form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $id = (int) $_POST['edit_user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact_no']);
    $nic_no = trim($_POST['nic_no']);

    $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, email=?, contact_no=?, nic_no=? WHERE user_id=? AND role='student'");
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $contact, $nic_no, $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Student details updated.'; }
    else { $error = 'Could not update student details.'; }
}

// Search
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = "%$search%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE role='student' AND (full_name LIKE ? OR username LIKE ? OR reg_no LIKE ? OR nic_no LIKE ?) ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt);
} else {
    $students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY full_name");
}

$base = '../';
$active = 'students';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Students — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ADMINISTRATION</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Student Management</h1>
        <p>View, search, edit, or remove registered student profiles.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card">
    <form method="GET" action="manage_students.php" style="display:flex;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap;align-items:center;">
        <input type="text" name="q" class="input-luxury" placeholder="Search by name, username, NIC or reg. no." value="<?= h($search) ?>" style="max-width:380px;">
        <button type="submit" class="btn btn-luxury btn-filled btn-sm">Search Students</button>
        <?php if ($search): ?>
            <a href="manage_students.php" class="btn btn-luxury btn-outline btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>NIC No.</th>
                <th>Reg. No</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($students) > 0): while ($s = mysqli_fetch_assoc($students)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($s['full_name']) ?></td>
                <td><code style="background:var(--bg-secondary);padding:0.15rem 0.4rem;border-radius:3px;"><?= h($s['username']) ?></code></td>
                <td><?= h($s['nic_no'] ?: '—') ?></td>
                <td><?= h($s['reg_no'] ?: '—') ?></td>
                <td><?= h($s['email'] ?: '—') ?></td>
                <td><?= h($s['contact_no'] ?: '—') ?></td>
                <td style="display:flex;gap:0.5rem;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('edit-<?= $s['user_id'] ?>').style.display='table-row'">Edit</button>
                    <a class="btn btn-sm btn-danger" href="manage_students.php?delete=<?= $s['user_id'] ?>" onclick="return confirm('Permanently delete this student record?')">Delete</a>
                </td>
            </tr>
            <tr id="edit-<?= $s['user_id'] ?>" style="display:none;background:var(--bg-secondary);">
                <td colspan="7" style="padding:1.5rem;">
                    <h4 class="serif-heading" style="margin-bottom:1rem;color:var(--primary-dark);">Edit Details for <?= h($s['full_name']) ?></h4>
                    <form method="POST" action="manage_students.php" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;align-items:end;">
                        <input type="hidden" name="edit_user_id" value="<?= $s['user_id'] ?>">
                        <div class="form-group" style="margin:0;"><label>Full Name</label><input type="text" name="full_name" class="input-luxury" value="<?= h($s['full_name']) ?>" required></div>
                        <div class="form-group" style="margin:0;"><label>NIC No.</label><input type="text" name="nic_no" class="input-luxury" value="<?= h($s['nic_no']) ?>"></div>
                        <div class="form-group" style="margin:0;"><label>Email</label><input type="email" name="email" class="input-luxury" value="<?= h($s['email']) ?>"></div>
                        <div class="form-group" style="margin:0;"><label>Contact</label><input type="tel" name="contact_no" class="input-luxury" value="<?= h($s['contact_no']) ?>"></div>
                        <div style="display:flex;gap:0.5rem;">
                            <button type="submit" class="btn btn-sm btn-luxury btn-accent">Save</button>
                            <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('edit-<?= $s['user_id'] ?>').style.display='none'">Cancel</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="empty-state">No student records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
