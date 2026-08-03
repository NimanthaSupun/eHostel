<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';
$active = 'contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us — eHostel</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.page-hero {
    text-align: center;
    padding: 3rem 0 1rem;
}
.page-hero h1 {
    font-size: 2.75rem;
}
.page-hero p {
    max-width: 700px;
    margin: 0 auto;
    font-size: 1.05rem;
}
.contact-layout {
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 3rem;
    margin-top: 3rem;
}
.contact-info-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.contact-info-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}
.contact-info-item i {
    width: 42px;
    height: 42px;
    min-width: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    background: rgba(201, 169, 110, 0.14);
    color: var(--accent);
    font-size: 1.1rem;
}
.contact-info-item h4 {
    color: var(--primary-dark);
    margin-bottom: 0.25rem;
    font-size: 1.15rem;
    font-weight: 700;
}
.contact-info-item p {
    margin-bottom: 0;
    font-size: 0.98rem;
    font-weight: 400;
    color: var(--text-secondary);
}
.contact-layout .card {
    background: var(--surface);
}
.map-embed {
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
}
.map-embed iframe {
    width: 100%;
    height: 460px;
    border: 0;
    display: block;
}
@media (max-width: 800px) {
    .contact-layout { grid-template-columns: 1fr; }
}
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
        <span class="section-label">GET IN TOUCH</span>
        <h1 class="serif-heading" style="font-size:2.6rem;">Contact Warden &amp; Administration</h1>
        <p>Questions about accommodation eligibility, room transfers, or maintenance requests?</p>
    </div>
</div>

<div style="max-width:1250px;margin:2.5rem auto 4rem;padding:0 2rem;display:grid;grid-template-columns:1fr 1.2fr;gap:2rem;">
    <div>
        <div class="card" style="margin-bottom:1.5rem;">
            <h3 class="serif-heading" style="font-size:1.5rem;color:var(--primary-dark);">Hostel Warden Office</h3>
            <p style="color:var(--text-secondary);font-size:0.95rem;line-height:1.7;margin-top:0.75rem;">
                📍 <strong>Address:</strong> eHostel Residential Premises, University Campus, Colombo 03, Sri Lanka.<br><br>
                ✉️ <strong>Email:</strong> ssp@ucsc.cmb.ac.lk<br><br>
                📞 <strong>Office Desk:</strong> +94 11 258 1234<br><br>
                ⏰ <strong>Office Hours:</strong> Monday – Friday (8:30 AM – 4:30 PM)
            </p>
        </div>

        <div class="card" style="border-top:3px solid var(--accent);">
            <h4 class="serif-heading" style="font-size:1.25rem;color:var(--primary-dark);">Emergency Contacts</h4>
            <p style="color:var(--text-muted);font-size:0.88rem;margin-top:0.5rem;">
                Resident Warden (24/7): <strong>+94 77 123 9988</strong><br>
                Campus Security Desk: <strong>+94 11 258 9900</strong>
            </p>
        </div>
    </div>

    <div class="map-embed">
        <iframe loading="lazy" src="https://maps.google.com/maps?q=Colombo%203%2C%20Sri%20Lanka&t=&z=14&ie=UTF8&iwloc=&output=embed"></iframe>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>

</body>
</html>
