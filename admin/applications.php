<?php
/**
 * ============================================================
 *  APPLICATION REVIEW — eHostel Admin
 * ============================================================
 *
 *  CRUD Operations in this file:
 *  ─────────────────────────────
 *  [CREATE]  Insert a new allocation record when approving
 *  [READ]    Select the application's user_id and student_id
 *  [READ]    Select all applications (JOIN with users)
 *  [READ]    Select all vacant beds (JOIN rooms, LEFT JOIN allocations)
 *  [UPDATE]  Update application status to 'approved'
 *  [UPDATE]  Update bed status to 'occupied'
 *  [UPDATE]  Assign a student_id if the student doesn't have one
 *  [UPDATE]  Update application status to 'rejected'
 *
 *  Tables involved: applications, users, beds, rooms, allocations
 * ============================================================
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';


/* ══════════════════════════════════════════════════════════════
 *  [CREATE + UPDATE] — Approve an application & allocate a bed
 *  ────────────────────────────────────────────────────────────
 *  This operation uses a TRANSACTION containing:
 *    1. [READ]   SELECT user_id, student_id FROM applications JOIN users
 *    2. [UPDATE] UPDATE applications SET status='approved'
 *    3. [UPDATE] UPDATE beds SET status='occupied'
 *    4. [CREATE] INSERT INTO allocations (application_id, user_id, bed_id, allocation_date)
 *    5. [UPDATE] UPDATE users SET student_id=? (only if student has no ID yet)
 * ══════════════════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $app_id = (int) $_POST['approve_id'];
    $bed_id = (int) $_POST['bed_id'];

    mysqli_begin_transaction($conn);
    try {
        /* Step 1: [READ] Fetch application details and check student_id
         * SQL: SELECT applications.user_id AS app_user_id, users.student_id
         *      FROM applications JOIN users ON users.user_id = applications.user_id
         *      WHERE applications.application_id = ? */
        $stmt = mysqli_prepare($conn, "SELECT applications.user_id AS app_user_id, users.student_id FROM applications JOIN users ON users.user_id = applications.user_id WHERE applications.application_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $app_id);
        mysqli_stmt_execute($stmt);
        $app = mysqli_stmt_get_result($stmt)->fetch_assoc();

        if (!$app) throw new Exception('Application not found.');

        /* Step 2: [UPDATE] Set application status to 'approved'
         * SQL: UPDATE applications SET status='approved'
         *      WHERE application_id = ? */
        $upd = mysqli_prepare($conn, "UPDATE applications SET status='approved' WHERE application_id=?");
        mysqli_stmt_bind_param($upd, "i", $app_id);
        mysqli_stmt_execute($upd);

        /* Step 3: [UPDATE] Mark the selected bed as 'occupied'
         * SQL: UPDATE beds SET status='occupied'
         *      WHERE bed_id = ? AND status='vacant' */
        $bedUpd = mysqli_prepare($conn, "UPDATE beds SET status='occupied' WHERE bed_id=? AND status='vacant'");
        mysqli_stmt_bind_param($bedUpd, "i", $bed_id);
        mysqli_stmt_execute($bedUpd);
        if (mysqli_stmt_affected_rows($bedUpd) === 0) throw new Exception('Selected bed is no longer available.');

        /* Step 4: [CREATE] Insert new allocation record
         * SQL: INSERT INTO allocations
         *      (application_id, user_id, bed_id, allocation_date)
         *      VALUES (?, ?, ?, CURDATE()) */
        $alloc = mysqli_prepare($conn, "INSERT INTO allocations (application_id, user_id, bed_id, allocation_date) VALUES (?, ?, ?, CURDATE())");
        mysqli_stmt_bind_param($alloc, "iii", $app_id, $app['app_user_id'], $bed_id);
        mysqli_stmt_execute($alloc);

        /* Step 5: [UPDATE] Auto-generate student_id if not already assigned
         * SQL: UPDATE users SET student_id = ? WHERE user_id = ? */
        if (empty($app['student_id'])) {
            $newStudentId = generate_student_id($conn);
            $studentIdStmt = mysqli_prepare($conn, "UPDATE users SET student_id=? WHERE user_id=?");
            mysqli_stmt_bind_param($studentIdStmt, "si", $newStudentId, $app['app_user_id']);
            mysqli_stmt_execute($studentIdStmt);
        }

        mysqli_commit($conn);
        $success = 'Application approved and bed allocated successfully.';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}


/* ══════════════════════════════════════════════════════════════
 *  [UPDATE] — Reject an application
 *  ────────────────────────────────────────────────────────────
 *  SQL: UPDATE applications SET status='rejected'
 *       WHERE application_id = ?
 * ══════════════════════════════════════════════════════════════ */
if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $stmt = mysqli_prepare($conn, "UPDATE applications SET status='rejected' WHERE application_id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Application status set to rejected.'; }
}


/* ══════════════════════════════════════════════════════════════
 *  [READ] — Retrieve all applications with student details
 *  ────────────────────────────────────────────────────────────
 *  SQL: SELECT ap.*, u.full_name, u.email, u.student_id,
 *              u.address, u.contact_no
 *       FROM applications ap
 *       JOIN users u ON ap.user_id = u.user_id
 *       ORDER BY FIELD(ap.status,'pending','approved','rejected'),
 *                ap.application_id DESC
 *
 *  Uses FIELD() to sort: pending first, then approved, then rejected.
 * ══════════════════════════════════════════════════════════════ */
$apps = mysqli_query($conn, "SELECT ap.*, u.full_name, u.email, u.student_id, u.address, u.contact_no
                              FROM applications ap JOIN users u ON ap.user_id = u.user_id
                              ORDER BY FIELD(ap.status,'pending','approved','rejected'), ap.application_id DESC");


/* ══════════════════════════════════════════════════════════════
 *  [READ] — Retrieve all currently vacant beds for allocation
 *  ────────────────────────────────────────────────────────────
 *  SQL: SELECT b.bed_id, b.bed_number, r.room_number, r.room_type
 *       FROM beds b
 *       JOIN rooms r ON b.room_id = r.room_id
 *       LEFT JOIN allocations a ON a.bed_id = b.bed_id
 *       WHERE b.status='vacant' AND a.bed_id IS NULL
 *         AND (r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%')
 *       ORDER BY r.room_number, b.bed_number
 *
 *  Uses LEFT JOIN + IS NULL to exclude already-allocated beds.
 * ══════════════════════════════════════════════════════════════ */
$vacantBeds = mysqli_query($conn, "SELECT b.bed_id, b.bed_number, r.room_number, r.room_type
                                    FROM beds b
                                    JOIN rooms r ON b.room_id = r.room_id
                                    LEFT JOIN allocations a ON a.bed_id = b.bed_id
                                    WHERE b.status='vacant' AND a.bed_id IS NULL
                                      AND (r.room_number LIKE 'F1/%' OR r.room_number LIKE 'F2/%')
                                    ORDER BY r.room_number, b.bed_number");
$bedOptions = [];
while ($b = mysqli_fetch_assoc($vacantBeds)) { $bedOptions[] = $b; }

$base = '../';
$active = 'apps';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Applications — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">APPLICATION REVIEW</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Student Applications</h1>
        <p>Review student requests, allocate vacant beds upon approval, or reject requests.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card">
    <!-- [READ] Display all applications from the SELECT query -->
    <table>
        <thead>
            <tr>
                <th>Student Details</th>
                <th>Student ID</th>
                <th>Address</th>
                <th>Preferred Type</th>
                <th>Applied Date</th>
                <th>Status</th>
                <th>Action &amp; Allocation</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($apps) > 0): while ($a = mysqli_fetch_assoc($apps)): ?>
            <tr>
                <td>
                    <strong style="color:var(--primary-dark);"><?= h($a['full_name']) ?></strong>
                </td>
                <td><?= h($a['student_id'] ?: 'Pending') ?></td>
                <td><?= h($a['address'] ?: '—') ?></td>
                <td><?= h(ucfirst($a['preferred_room_type'])) ?></td>
                <td><?= date('d M Y', strtotime($a['applied_date'])) ?></td>
                <td>
                    <span class="badge badge-<?= $a['status'] === 'approved' ? 'success' : ($a['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= h(strtoupper($a['status'])) ?>
                    </span>
                </td>
                <td>
                    <?php if ($a['status'] === 'pending'): ?>
                        <?php if (count($bedOptions) > 0): ?>
                        <!-- [CREATE + UPDATE] Approve form — triggers INSERT allocation + UPDATE beds/applications -->
                        <form method="POST" action="applications.php" style="display:flex;flex-direction:column;align-items:stretch;gap:0.45rem;max-width:260px;">
                            <input type="hidden" name="approve_id" value="<?= $a['application_id'] ?>">
                            <!-- [READ] Vacant bed options from the SELECT query above -->
                            <select name="bed_id" required class="input-luxury" style="padding:0.4rem 0.6rem;font-size:0.8rem;width:100%;">
                                <option value="">Select Vacant Bed</option>
                                <?php foreach ($bedOptions as $b): ?>
                                    <option value="<?= $b['bed_id'] ?>">Room <?= h($b['room_number']) ?> — Bed <?= h($b['bed_number']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-luxury btn-accent">Approve</button>
                        </form>
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:var(--danger);font-weight:600;">No Vacant Beds</span>
                        <?php endif; ?>
                        <!-- [UPDATE] Reject link — triggers UPDATE applications SET status='rejected' -->
                        <a class="btn btn-sm btn-danger" style="margin-top:0.25rem;" href="applications.php?reject=<?= $a['application_id'] ?>" onclick="return confirm('Reject this application?')">Reject</a>
                    <?php else: ?>
                        <span style="font-size:0.85rem;color:var(--text-muted);">Process Complete</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="empty-state">No student applications submitted yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
