<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';
$active = 'rules';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hostel Rules &amp; Policies — eHostel</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
</head>
<body>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/public_nav.php'; ?>
<?php endif; ?>

<div class="page-header" style="max-width:1250px;margin:3rem auto 0;padding:0 2rem;">
    <div>
        <span class="section-label">CODE OF CONDUCT</span>
        <h1 class="serif-heading" style="font-size:2.6rem;">Hostel Rules &amp; Regulations</h1>
        <p>Standards maintained to ensure safety, mutual respect, and quiet academic environments for all residents.</p>
    </div>
</div>

<div style="max-width:1250px;margin:2.5rem auto 4rem;padding:0 2rem;">
    <div class="card" style="border-left:4px solid var(--accent);margin-bottom:2rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:0.75rem;">1. Entry Gate &amp; Curfew Policy</h3>
        <ul style="line-height:1.8;color:var(--text-secondary);padding-left:1.2rem;">
            <li>Main hostel entrance gates close at <strong>10:00 PM</strong> daily on weekdays and <strong>10:30 PM</strong> on weekends.</li>
            <li>Late entries require prior written permission from the Chief Warden at least 6 hours in advance.</li>
            <li>All residents must scan their student ID card or sign the entrance register upon entering after 9:00 PM.</li>
        </ul>
    </div>

    <div class="card" style="border-left:4px solid var(--primary);margin-bottom:2rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:0.75rem;">2. Visitor &amp; Guest Policies</h3>
        <ul style="line-height:1.8;color:var(--text-secondary);padding-left:1.2rem;">
            <li>Visitors are permitted in the ground floor main lobby area only between <strong>9:00 AM and 6:00 PM</strong>.</li>
            <li>No external guests or non-residents are allowed inside student bedrooms under any circumstances.</li>
            <li>Overnight stays by non-allocated students are strictly prohibited without warden approval.</li>
        </ul>
    </div>

    <div class="card" style="border-left:4px solid var(--accent);margin-bottom:2rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:0.75rem;">3. Quiet Hours &amp; Academic Atmosphere</h3>
        <ul style="line-height:1.8;color:var(--text-secondary);padding-left:1.2rem;">
            <li>Official quiet hours are enforced from <strong>10:00 PM to 6:00 AM</strong> across all floors and study lounges.</li>
            <li>Loud music, amplified speakers, or disruptive noise in corridors is strictly forbidden at all times.</li>
            <li>Headphones must be used in shared rooms during late-night study hours.</li>
        </ul>
    </div>

    <div class="card" style="border-left:4px solid var(--danger);margin-bottom:2rem;">
        <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:0.75rem;">4. Safety, Security &amp; Substance Prohibition</h3>
        <ul style="line-height:1.8;color:var(--text-secondary);padding-left:1.2rem;">
            <li>eHostel operates a strict <strong>zero-tolerance policy</strong> regarding smoking, alcohol, and illicit substances on premises.</li>
            <li>High-power electric cooking appliances (heaters, hot plates) inside individual bedrooms are prohibited due to fire safety protocols.</li>
            <li>Tampering with fire alarms, CCTV cameras, or emergency exits will result in immediate disciplinary action.</li>
        </ul>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>

</body>
</html>
