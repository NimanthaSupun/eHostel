<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';
$active = 'help';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Help &amp; Guide — eHostel</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
</head>
<body>
<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/public_nav.php'; ?>
    <div style="max-width:1250px;margin:3rem auto;padding:0 2rem;">
<?php endif; ?>

<div class="page-header">
    <div>
        <span class="section-label">DOCUMENTATION</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Help &amp; User Guide</h1>
        <p>Step-by-step instructions for using the eHostel resident management portal.</p>
    </div>
</div>

<div class="card" style="max-width:900px;">
    <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:1rem;">Guide for Students</h3>
    <ol style="padding-left:1.2rem;line-height:1.8;color:var(--text-secondary);">
        <li style="margin-bottom:0.75rem;">
            Create an account on the <a href="register.php" style="font-weight:600;">Student Register</a> page, or log in if you already have credentials.
        </li>
        <li style="margin-bottom:0.75rem;">
            Explore available room configurations via <a href="facilities.php" style="font-weight:600;">Facilities</a> or the <a href="student/rooms.php" style="font-weight:600;">View Available Rooms</a> page.
        </li>
        <li style="margin-bottom:0.75rem;">
            From your student dashboard, click <strong>Apply for Hostel</strong> and fill out your personal, academic, and room preference details.
        </li>
        <li style="margin-bottom:0.75rem;">
            Track your application status (Pending, Approved, Rejected) directly on your student dashboard.
        </li>
        <li style="margin-bottom:0.75rem;">
            Once approved, your assigned room number and bed number will display in your active accommodation status.
        </li>
        <li style="margin-bottom:0.75rem;">
            Update your personal address and contact details anytime under <strong>My Profile</strong>.
        </li>
        <li style="margin-bottom:0.75rem;">
            Check <strong>Official Announcements</strong> regularly for maintenance updates, warden notices, and event alerts.
        </li>
    </ol>
</div>

<div class="card" style="max-width:900px;">
    <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:1rem;">Guide for Administrators &amp; Wardens</h3>
    <ol style="padding-left:1.2rem;line-height:1.8;color:var(--text-secondary);">
        <li style="margin-bottom:0.75rem;">
            Log in using administrator credentials.
        </li>
        <li style="margin-bottom:0.75rem;">
            Use <strong>Manage Students</strong> to view, search, edit details, or remove student accounts.
        </li>
        <li style="margin-bottom:0.75rem;">
            Use <strong>Manage Rooms &amp; Beds</strong> to configure room numbers, capacity limits, and auto-generate bed numbers.
        </li>
        <li style="margin-bottom:0.75rem;">
            Use <strong>Review Applications</strong> to review student applications, select a vacant bed from the dropdown, and approve accommodation.
        </li>
        <li style="margin-bottom:0.75rem;">
            Use <strong>Announcements</strong> to compose and publish notices visible to all students.
        </li>
        <li style="margin-bottom:0.75rem;">
            Use <strong>Summary Reports</strong> for real-time statistical breakdowns of bed occupancy rates.
        </li>
    </ol>
</div>

<div class="card" style="max-width:900px;">
    <h3 class="serif-heading" style="font-size:1.4rem;color:var(--primary-dark);margin-bottom:0.5rem;">Need Administrative Assistance?</h3>
    <p style="color:var(--text-secondary);margin:0;">
        For technical support or special accommodation inquiries, please contact the Hostel Warden Office at <strong>ssp@ucsc.cmb.ac.lk</strong> or visit the main office on Campus Floor 1.
    </p>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>
</body>
</html>
