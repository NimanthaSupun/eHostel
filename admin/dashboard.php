<?php
/**
 * ============================================================
 *  ADMIN DASHBOARD — eHostel
 * ============================================================
 *
 *  CRUD Operations in this file:
 *  ─────────────────────────────
 *  [READ]  Select 5 most recent applications (JOIN with users)
 *
 *  Tables involved: applications, users
 * ============================================================
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

/* ──────────────────────────────────────────────────────────────
 *  [READ] — Retrieve the 5 most recent hostel applications
 *  SQL: SELECT ... FROM applications JOIN users ...
 *       ORDER BY application_id DESC LIMIT 5
 * ────────────────────────────────────────────────────────────── */
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

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;margin:0;">Recent Applications</h3>
        <a href="applications.php" style="font-size:0.82rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;">Review All Applications &rarr;</a>
    </div>

    <!-- [READ] Display results from the SELECT query above -->
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
