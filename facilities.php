<?php
require_once __DIR__ . '/includes/auth.php';
$loggedIn = is_logged_in();
$base = '';
$active = 'facilities';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Facilities — eHostel Premises</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
<style>
.facilities-hero {
    text-align: center;
    padding: 5rem 2rem 3rem;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
}

.facility-card-img {
    height: 220px;
    width: 100%;
    object-fit: cover;
    border-radius: var(--radius-md);
    margin-bottom: 1.25rem;
}

.spec-list {
    list-style: none;
    padding: 0;
    margin: 1rem 0 0;
}

.spec-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
</style>
</head>
<body>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/public_nav.php'; ?>
<?php endif; ?>

<section class="facilities-hero">
    <div style="max-width:800px;margin:0 auto;">
        <span class="section-label">PREMISES &amp; AMENITIES</span>
        <h1 class="serif-heading" style="font-size:3.2rem;margin-bottom:1rem;">World-Class Hostel Facilities</h1>
        <p style="font-size:1.1rem;color:var(--text-secondary);">
            Designed to foster academic focus, personal safety, and comfortable university living — located right next to campus.
        </p>
    </div>
</section>

<div style="max-width:1250px;margin:3.5rem auto;padding:0 2rem;">
    <!-- Building Overview Stats -->
    <div style="margin-bottom:4rem;">
        <div style="text-align:center;max-width:650px;margin:0 auto 2.5rem;">
            <span class="section-label">INFRASTRUCTURE</span>
            <h2 class="section-heading-center">Hostel Layout &amp; Capacity</h2>
        </div>

        <div class="card-grid">
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--primary-dark);">3</div>
                <div class="label">Residential Floors</div>
            </div>
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--accent-dark);">12</div>
                <div class="label">Rooms Per Floor</div>
            </div>
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--primary-dark);">36</div>
                <div class="label">Total Student Rooms</div>
            </div>
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--success);">1 : 3</div>
                <div class="label">Bathroom Ratio</div>
            </div>
        </div>
    </div>

    <!-- Room Types & Specs -->
    <div style="margin-bottom:4rem;">
        <div style="text-align:center;max-width:650px;margin:0 auto 2.5rem;">
            <span class="section-label">ACCOMMODATION</span>
            <h2 class="section-heading-center">Room Configurations</h2>
        </div>

        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
            <div class="card">
                <img src="images/Gemini_Generated_Image_oe8xefoe8xefoe8x.png" alt="Single Room" class="facility-card-img">
                <span class="badge badge-success" style="margin-bottom:0.5rem;">Single Private</span>
                <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);">Single Study Suite</h3>
                <p>Private room engineered for students who demand quiet study environments and individual privacy.</p>
                <ul class="spec-list">
                    <li><span style="color:var(--accent);">✓</span> Personal ergonomic study desk &amp; chair</li>
                    <li><span style="color:var(--accent);">✓</span> Dedicated high-capacity wardrobe</li>
                    <li><span style="color:var(--accent);">✓</span> Air-conditioned or fan-cooled options</li>
                </ul>
            </div>

            <div class="card">
                <img src="images/Gemini_Generated_Image_mfa57smfa57smfa5 (1).png" alt="Double Sharing Room" class="facility-card-img">
                <span class="badge badge-warning" style="margin-bottom:0.5rem;">2 Beds</span>
                <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);">Double Sharing Room</h3>
                <p>Shared between two students — offering a balance of social companionship and individual study desks.</p>
                <ul class="spec-list">
                    <li><span style="color:var(--accent);">✓</span> Twin study desks with individual lamps</li>
                    <li><span style="color:var(--accent);">✓</span> Dual lockers and secure storage unit</li>
                    <li><span style="color:var(--accent);">✓</span> High-speed Wi-Fi router in-room</li>
                </ul>
            </div>

            <div class="card">
                <img src="images/Gemini_Generated_Image_oo6nezoo6nezoo6n.png" alt="Triple Sharing Room" class="facility-card-img">
                <span class="badge badge-muted" style="margin-bottom:0.5rem;">3 Beds</span>
                <h3 class="serif-heading" style="font-size:1.6rem;color:var(--primary-dark);">Triple Shared Dormitory</h3>
                <p>Budget-friendly dormitory layout with custom wooden bunk beds and private reading lights.</p>
                <ul class="spec-list">
                    <li><span style="color:var(--accent);">✓</span> Individual lockers &amp; reading lights</li>
                    <li><span style="color:var(--accent);">✓</span> Shared central study table</li>
                    <li><span style="color:var(--accent);">✓</span> Daily room cleaning service</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Special Amenities Grid -->
    <div>
        <div style="text-align:center;max-width:650px;margin:0 auto 2.5rem;">
            <span class="section-label">EVERYDAY CONVENIENCE</span>
            <h2 class="section-heading-center">Special Premises &amp; Services</h2>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3>🛡️ 24/7 Security &amp; CCTV</h3>
                <p>Continuous entrance monitoring, digital keycard entry gates, and resident warden on-site at all hours.</p>
            </div>
            <div class="card">
                <h3>📶 High-Speed Wi-Fi Network</h3>
                <p>Fiber-optic Wi-Fi covering all bedrooms, common lounges, and study floors for uninterrupted research.</p>
            </div>
            <div class="card">
                <h3>📚 Quiet Study Lounges</h3>
                <p>Dedicated quiet floor on every level with individual carrels and communal group project tables.</p>
            </div>
            <div class="card">
                <h3>🍽️ Kitchen &amp; Dining Spaces</h3>
                <p>Fully equipped kitchen space per floor with induction stoves, microwaves, refrigerators, and dining areas.</p>
            </div>
            <div class="card">
                <h3>🧺 On-Site Laundry Room</h3>
                <p>Automatic washing machines, tumble dryers, and covered clothes drying balconies on each floor.</p>
            </div>
            <div class="card">
                <h3>🅿️ Secure Bicycle &amp; Vehicle Parking</h3>
                <p>Covered bicycle racks and two-wheeler parking slots with 24-hour security supervision.</p>
            </div>
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
