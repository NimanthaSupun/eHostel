<?php
// Expects $base ('' or '../') and $active (page key) to be set by caller
$base = $base ?? '';
$active = $active ?? '';
?>
<div class="topnav" id="public-topnav">
    <div class="brand">
        <a href="<?= $base ?>index.php" style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:0.75rem;">
            <!-- eHostel <span class="tag">University Accommodation</span> -->
            eHostel <span class="">            </span>

        </a>
    </div>
    <nav>
        <a href="<?= $base ?>index.php" class="<?= $active === 'home' ? 'active' : '' ?>">Home</a>
        <a href="<?= $base ?>facilities.php" class="<?= $active === 'facilities' ? 'active' : '' ?>">Facilities</a>
        <a href="<?= $base ?>rules.php" class="<?= $active === 'rules' ? 'active' : '' ?>">Rules</a>
        <a href="<?= $base ?>contact.php" class="<?= $active === 'contact' ? 'active' : '' ?>">Contact</a>
        <a href="<?= $base ?>functionalities.php" class="<?= $active === 'func' ? 'active' : '' ?>">Features</a>
        <a href="<?= $base ?>help.php" class="<?= $active === 'help' ? 'active' : '' ?>">Help</a>
    </nav>
    <div class="user-chip">
        <?php if (is_logged_in()): ?>
            <?php $dash = current_role() === 'admin' ? $base . 'admin/dashboard.php' : $base . 'student/dashboard.php'; ?>
            <a href="<?= $dash ?>" class="btn btn-sm btn-accent">Dashboard</a>
            <a href="<?= $base ?>logout.php" class="btn btn-sm btn-outline">Logout</a>
        <?php else: ?>
            <a href="<?= $base ?>login.php" class="btn btn-sm btn-outline">Student / Admin Login</a>
            <a href="<?= $base ?>register.php" class="btn btn-sm btn-accent">Apply Now</a>
        <?php endif; ?>
    </div>
</div>

<script>
window.addEventListener('scroll', function() {
    const nav = document.getElementById('public-topnav');
    if (nav) {
        if (window.scrollY > 40) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }
});
</script>
