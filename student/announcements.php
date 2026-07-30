<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$anns = mysqli_query($conn, "SELECT a.title, a.content, a.posted_date, u.full_name
                              FROM announcements a
                              LEFT JOIN users u ON a.posted_by = u.user_id
                              ORDER BY a.posted_date DESC");

$base = '../';
$active = 'ann';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">NOTICE BOARD</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Official Announcements</h1>
        <p>Stay informed with official notices from the hostel warden and university administration.</p>
    </div>
</div>

<div class="card" style="max-width:900px;">
    <?php if ($anns && mysqli_num_rows($anns) > 0): ?>
        <?php while ($a = mysqli_fetch_assoc($anns)): ?>
            <div style="padding:1.5rem 0;border-bottom:1px solid var(--border);">
                <h3 class="serif-heading" style="font-size:1.5rem;color:var(--primary-dark);margin-bottom:0.5rem;"><?= h($a['title']) ?></h3>
                <p style="margin:0 0 0.85rem;color:var(--text-secondary);font-size:0.98rem;line-height:1.7;"><?= h($a['content']) ?></p>
                <div style="display:flex;align-items:center;gap:0.75rem;font-size:0.8rem;color:var(--text-muted);">
                    <span class="badge badge-muted">Official Notice</span>
                    <span>Posted by <?= h($a['full_name'] ?? 'Hostel Admin') ?> &middot; <?= date('d M Y, h:i A', strtotime($a['posted_date'])) ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-state">No announcements have been published yet.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
