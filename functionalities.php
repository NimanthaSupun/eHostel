<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';       // this file lives at the project root
$active = 'func';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Functionalities — eHostel</title>
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
        <span class="section-label">SYSTEM OVERVIEW</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">System Functionalities</h1>
        <p>Everything eHostel offers to university students and administrative wardens.</p>
    </div>
</div>

<div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));">
    <div class="card" style="border-top:4px solid var(--accent);">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);">🧑‍🎓 Student Functionalities</h3>
        <ul style="list-style:none;padding:0;margin-top:1rem;">
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> User registration, secure login and password management
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> Apply for hostel accommodation with custom room preferences
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> View real-time room availability across hostel buildings
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> Manage personal and academic profile details
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> Track room allocation status (Pending, Approved, Rejected)
            </li>
            <li style="padding:0.6rem 0;display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--accent);">✔</span> Access digital noticeboard for hostel announcements
            </li>
        </ul>
    </div>

    <div class="card" style="border-top:4px solid var(--primary);">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);">🛠️ Administrator Functionalities</h3>
        <ul style="list-style:none;padding:0;margin-top:1rem;">
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Role-based warden authentication and session control
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Review, approve or reject pending student applications
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Allocate specific vacant beds to approved student applicants
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Manage hostel buildings, floors, rooms, and auto-generated beds
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Search and manage student profiles (edit, search, delete)
            </li>
            <li style="padding:0.6rem 0;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Publish and manage digital noticeboard announcements
            </li>
            <li style="padding:0.6rem 0;display:flex;align-items:center;gap:0.6rem;">
                <span style="color:var(--primary);">✔</span> Generate real-time summary reports on occupancy rates
            </li>
        </ul>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>
</body>
</html>
