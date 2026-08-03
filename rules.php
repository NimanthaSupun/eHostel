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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.page-hero {
    text-align: center;
    padding: 3rem 0 1rem;
}
.page-hero h1 { font-size: 2.75rem; }
.page-hero p { max-width: 700px; margin: 0 auto; font-size: 1.05rem; }
.rules-list {
    margin-top: 3rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.rule-card {
    display: grid;
    grid-template-columns: 56px 1fr;
    gap: 1.25rem;
    align-items: flex-start;
}
.rule-number {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.3rem;
    color: white;
}
.rule-card h3 { margin-bottom: 0.5rem; }
.rule-card ul {
    margin: 0.5rem 0 0 1.1rem;
    color: var(--text-secondary);
}
.rule-card ul li { margin-bottom: 0.4rem; text-align: justify; }
.notice-strip {
    margin-top: 3rem;
    background: rgba(196, 130, 10, 0.08);
    border: 1px solid rgba(196, 130, 10, 0.22);
    border-radius: var(--radius-lg);
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}
.notice-strip i { color: var(--warning); font-size: 1.3rem; margin-top: 0.15rem; }
.notice-strip p { margin-bottom: 0; }
</style>
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
    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">1</div>
        <div>
            <h3>Curfew &amp; compliance:</h3>
            <ul>
                <li>Students must strictly adhere to the designated check-in and check-out times.</li>
                <li>Students should check in before 10.00 p.m.</li>
                <li>Students must submit a written request to the office in advance to get proper permission if they plan to stay out past 10:00 p.m. This rule keeps everyone safe and lets supervisors know where students are.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">2</div>
        <div>
            <h3>Anti ragging policy</h3>
            <ul>
                <li>Residents must treat staff and fellow residents with courtesy and respect at all times.</li>
                <li>Ragging, harassment, and any form of bullying will result in immediate disciplinary action.</li>
                <li>Damage to hostel property must be reported and may be charged to the responsible resident.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">3</div>
        <div>
            <h3>Quiet Hours</h3>
            <ul>
                <li>Quiet hours are observed daily from 10:00 PM to 6:00 AM in rooms and study areas.</li>
                <li>Loud music, gatherings, or noise that disturbs other residents is not permitted during this time.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">4</div>
        <div>
            <h3>Visitors restrictions</h3>
            <ul>
                <li>All visitors must be signed in and out at the front desk logbook.</li>
                <li>Visiting hours are 9:00 AM to 8:00 PM; overnight guests are not permitted without prior administrator approval.</li>
                <li>Residents are responsible for the conduct of their visitors while on the premises.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">5</div>
        <div>
            <h3>Safety &amp; Prohibited Items</h3>
            <ul>
                <li>Smoking, alcohol, and illegal substances are strictly prohibited on hostel premises.</li>
                <li>Cooking appliances (other than those provided in the shared kitchen), candles, and open flames are not allowed in rooms.</li>
                <li>Tampering with fire safety equipment or CCTV systems is a serious violation.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">6</div>
        <div>
            <h3>Sick bay reporting:</h3>
            <ul>
                <li>Any illness, contagious disease, or medical emergency must be reported immediately to the warden.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">7</div>
        <div>
            <h3>Prohibited &amp; areas</h3>
            <ul>
                <li>Access to the hostel terrace, kitchen, or opposite-gender wings is strictly forbidden.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">8</div>
        <div>
            <h3>Payments</h3>
            <ul>
                <li>Room rent is due on or before the 14th of every month and should be handed over to the manager.</li>
                <li>Late payments beyond 7 days may incur a surcharge and could result in suspension of hostel access.</li>
                <li>Security deposits are refundable within 14 days of checkout, subject to a room condition inspection.</li>
            </ul>
        </div>
    </div>

    <div class="card rule-card" style="margin-bottom:2rem;">
        <div class="rule-number">9</div>
        <div>
            <h3>Maintenance &amp; Complaints</h3>
            <ul>
                <li>Maintenance issues should be directed to the manager via email.</li>
                <li>Residents should allow maintenance staff reasonable access to rooms for repairs and safety checks.</li>
            </ul>
        </div>
    </div>

    <div class="notice-strip">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <p>Repeated or serious violations of these rules may result in disciplinary action, including suspension of hostel privileges or termination of residency, at the discretion of hostel administration.</p>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>

</body>
</html>
