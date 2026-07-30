<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';
$active = 'contact';

$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us — eHostel Warden Office</title>
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
        <span class="section-label">GET IN TOUCH</span>
        <h1 class="serif-heading" style="font-size:2.6rem;">Contact Warden &amp; Administration</h1>
        <p>Have questions regarding accommodation eligibility, room transfers, or maintenance requests?</p>
    </div>
</div>

<div style="max-width:1250px;margin:2.5rem auto 4rem;padding:0 2rem;display:grid;grid-template-columns:1fr 1.2fr;gap:2.5rem;">
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

    <div>
        <div class="card">
            <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);margin-bottom:1rem;">Send an Inquiry</h3>

            <?php if ($submitted): ?>
                <div class="alert alert-success">
                    Thank you! Your message has been sent to the hostel administration office. We will reply shortly.
                </div>
            <?php else: ?>
                <form method="POST" action="contact.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cname">Your Name *</label>
                            <input type="text" id="cname" name="cname" class="input-luxury" required placeholder="Full Name">
                        </div>
                        <div class="form-group">
                            <label for="cemail">Email Address *</label>
                            <input type="email" id="cemail" name="cemail" class="input-luxury" required placeholder="student@univ.ac.lk">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" class="input-luxury" required placeholder="Inquiry topic (e.g. Room allocation status)">
                    </div>
                    <div class="form-group">
                        <label for="msg">Message Details *</label>
                        <textarea id="msg" name="msg" class="input-luxury" rows="5" required placeholder="Write your inquiry here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-luxury btn-accent">Submit Message</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>

</body>
</html>
