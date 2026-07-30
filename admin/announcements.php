<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ann'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO announcements (title, content, posted_by) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $title, $content, $_SESSION['user_id']);
        if (mysqli_stmt_execute($stmt)) { $success = 'Announcement published successfully.'; }
        else { $error = 'Could not publish announcement.'; }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM announcements WHERE announcement_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) { $success = 'Announcement removed.'; }
}

$anns = mysqli_query($conn, "SELECT * FROM announcements ORDER BY posted_date DESC");

$base = '../';
$active = 'ann';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements — eHostel Admin</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">COMMUNICATION</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Publish Announcements</h1>
        <p>Post official notices visible to all registered students.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card" style="max-width:720px;">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">New Announcement</h3>
    <form method="POST" action="announcements.php">
        <div class="form-group">
            <label for="title">Announcement Title *</label>
            <input type="text" id="title" name="title" class="input-luxury" required placeholder="e.g. Maintenance Notice: Water Supply Interruption">
        </div>
        <div class="form-group">
            <label for="content">Announcement Content *</label>
            <textarea id="content" name="content" class="input-luxury" rows="4" required placeholder="Provide details of the notice..."></textarea>
        </div>
        <button type="submit" name="add_ann" class="btn btn-luxury btn-accent">Publish Announcement</button>
    </form>
</div>

<div class="card" style="max-width:900px;">
    <h3 class="serif-heading" style="font-size:1.5rem;margin-bottom:1.5rem;">Published Announcements</h3>
    <?php if (mysqli_num_rows($anns) > 0): while ($a = mysqli_fetch_assoc($anns)): ?>
        <div style="padding:1.25rem 0;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:1.5rem;align-items:flex-start;">
            <div>
                <h4 style="color:var(--primary-dark);margin-bottom:0.4rem;font-size:1.1rem;"><?= h($a['title']) ?></h4>
                <p style="margin:0 0 0.5rem;color:var(--text-secondary);font-size:0.92rem;line-height:1.6;"><?= h($a['content']) ?></p>
                <span style="font-size:0.75rem;color:var(--text-muted);"><?= date('d M Y, h:i A', strtotime($a['posted_date'])) ?></span>
            </div>
            <a class="btn btn-sm btn-danger" style="flex-shrink:0;" href="announcements.php?delete=<?= $a['announcement_id'] ?>" onclick="return confirm('Remove this announcement?')">Remove Notice</a>
        </div>
    <?php endwhile; else: ?>
        <p class="empty-state">No announcements published yet.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
