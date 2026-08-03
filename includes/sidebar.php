<?php
// Expects $base ('../') and $active (page key) set by caller
$base = $base ?? '../';
$active = $active ?? '';
$role = current_role();
?>
<div class="topnav">
    <div class="brand">
        <a href="<?= $base ?>index.php" style="color:inherit;text-decoration:none;">eHostel</a>
        <span class="tag"><?= strtoupper(h($role)) ?> PORTAL</span>
    </div>
    <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?></div>
        <div style="display:flex;flex-direction:column;">
            <span style="font-weight:600;font-size:0.88rem;color:var(--primary-dark);"><?= h($_SESSION['full_name'] ?? '') ?></span>
            <span style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;"><?= h($role) ?> Account</span>
        </div>
        <a href="<?= $base ?>logout.php" class="btn btn-sm btn-outline" style="margin-left:0.5rem;">Logout</a>
    </div>
</div>
<div class="app-layout">
    <div class="sidebar">
        <?php if ($role === 'admin'): ?>
            <a href="<?= $base ?>admin/dashboard.php" class="side-link <?= $active === 'dash' ? 'active' : '' ?>">🏠 Dashboard</a>
            <a href="<?= $base ?>admin/manage_rooms.php" class="side-link <?= $active === 'rooms' ? 'active' : '' ?>">🛏️ Rooms &amp; Beds</a>
            <a href="<?= $base ?>admin/manage_students.php" class="side-link <?= $active === 'students' ? 'active' : '' ?>">🧑‍🎓 Student Records</a>
            <a href="<?= $base ?>admin/applications.php" class="side-link <?= $active === 'apps' ? 'active' : '' ?>">📄 Review Applications</a>
            <a href="<?= $base ?>admin/announcements.php" class="side-link <?= $active === 'ann' ? 'active' : '' ?>">📢 Announcements</a>
            <a href="<?= $base ?>admin/reports.php" class="side-link <?= $active === 'reports' ? 'active' : '' ?>">📊 Summary Reports</a>
            <a href="<?= $base ?>functionalities.php" class="side-link <?= $active === 'func' ? 'active' : '' ?>">⚙️ Functionalities</a>
            <a href="<?= $base ?>help.php" class="side-link <?= $active === 'help' ? 'active' : '' ?>">❓ Help Guide</a>
        <?php else: ?>
            <a href="<?= $base ?>student/dashboard.php" class="side-link <?= $active === 'dash' ? 'active' : '' ?>">🏠 Student Dashboard</a>
            <a href="<?= $base ?>student/rooms.php" class="side-link <?= $active === 'st_rooms' ? 'active' : '' ?>">🏢 View Available Rooms</a>
            <a href="<?= $base ?>student/apply.php" class="side-link <?= $active === 'apply' ? 'active' : '' ?>">📝 Apply for Hostel</a>
            <a href="<?= $base ?>student/profile.php" class="side-link <?= $active === 'profile' ? 'active' : '' ?>">👤 My Profile</a>
            <a href="<?= $base ?>student/announcements.php" class="side-link <?= $active === 'ann' ? 'active' : '' ?>">📢 Notices &amp; Updates</a>
            <a href="<?= $base ?>functionalities.php" class="side-link <?= $active === 'func' ? 'active' : '' ?>">⚙️ Functionalities</a>
            <a href="<?= $base ?>help.php" class="side-link <?= $active === 'help' ? 'active' : '' ?>">❓ Student Help</a>
        <?php endif; ?>
    </div>
    <div class="main-content">
