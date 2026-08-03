<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_begin_transaction($conn);
    try {
        $bedStmt = mysqli_prepare($conn, "SELECT DISTINCT bed_id FROM allocations WHERE user_id = ?");
        mysqli_stmt_bind_param($bedStmt, "i", $id);
        mysqli_stmt_execute($bedStmt);
        $bedResult = mysqli_stmt_get_result($bedStmt);
        $bedIds = [];
        while ($row = mysqli_fetch_assoc($bedResult)) {
            $bedIds[] = (int) $row['bed_id'];
        }

        if ($bedIds) {
            $bedUpdate = mysqli_prepare($conn, "UPDATE beds SET status='vacant' WHERE bed_id = ?");
            foreach ($bedIds as $bedId) {
                mysqli_stmt_bind_param($bedUpdate, "i", $bedId);
                if (!mysqli_stmt_execute($bedUpdate)) {
                    throw new Exception('Could not release assigned bed(s).');
                }
            }
        }

        $allocDelete = mysqli_prepare($conn, "DELETE FROM allocations WHERE user_id = ?");
        mysqli_stmt_bind_param($allocDelete, "i", $id);
        if (!mysqli_stmt_execute($allocDelete)) {
            throw new Exception('Could not clear allocations.');
        }

        $appDelete = mysqli_prepare($conn, "DELETE FROM applications WHERE user_id = ?");
        mysqli_stmt_bind_param($appDelete, "i", $id);
        if (!mysqli_stmt_execute($appDelete)) {
            throw new Exception('Could not clear applications.');
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ? AND role = 'student'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Could not delete student record.');
        }

        mysqli_commit($conn);
        $success = 'Student record deleted successfully and assigned beds were released.';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = "%$search%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE role='student' AND (full_name LIKE ? OR username LIKE ? OR student_id LIKE ? OR nic_no LIKE ?) ORDER BY full_name");
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
        <input type="text" name="q" class="input-luxury" placeholder="Search by name, username, student ID or NIC" value="<?= h($search) ?>" style="max-width:380px;">
        <button type="submit" class="btn btn-luxury btn-filled btn-sm">Search Students</button>
        <?php if ($search): ?>
            <a href="manage_students.php" class="btn btn-luxury btn-outline btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>NIC No.</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($students) > 0): while ($s = mysqli_fetch_assoc($students)): ?>
            <tr>
                <td style="font-weight:600;color:var(--primary-dark);"><?= h($s['full_name']) ?></td>
                <td><?= h($s['student_id'] ?: 'Pending') ?></td>
                <td><?= h($s['nic_no'] ?: '—') ?></td>
                <td><?= h($s['email'] ?: '—') ?></td>
                <td><?= h($s['contact_no'] ?: '—') ?></td>
                <td style="display:flex;gap:0.5rem;">
                    <a class="btn btn-sm btn-outline" href="student_detail.php?id=<?= $s['user_id'] ?>">View Details</a>
                    <a class="btn btn-sm btn-danger" href="manage_students.php?delete=<?= $s['user_id'] ?>" onclick="return confirm('Permanently delete this student record?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="6" class="empty-state">No student records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
