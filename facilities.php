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
<title>Our Facilities — eHostel</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.page-hero {
    text-align: center;
    padding: 3rem 0 1rem;
}
.page-hero h1 { font-size: 2.75rem; }
.page-hero p { max-width: 700px; margin: 0 auto; font-size: 1.05rem; }
.facility-icon { font-size: 1.6rem; margin-right: 0.5rem; }
.section-block { margin-top: 5rem; }
.section-block h2 { text-align: center; margin-bottom: 0.5rem; }
.section-sub { text-align: center; max-width: 760px; margin: 0 auto; font-size: 1.02rem; }
.overview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2.5rem; }
.overview-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); text-align: center; padding: 1.75rem 1rem; box-shadow: var(--shadow-sm); }
.overview-icon { font-size: 2rem; color: var(--accent-dark); margin-bottom: 0.5rem; }
.overview-number { font-size: 2.5rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, var(--primary-dark) 0%, var(--accent-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.overview-label { color: var(--text-secondary); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.03em; margin-top: 0.4rem; }
.room-types-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 2.5rem; }
.room-type-card img { width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius-md); }
.bed-badge { display: inline-block; background: rgba(201, 169, 110, 0.14); color: var(--accent-dark); padding: 0.3rem 0.85rem; border-radius: 9999px; font-size: 0.95rem; font-weight: 600; margin: 0.85rem 0 0.5rem; }
.price-row { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-top: 1px solid var(--border); font-size: 0.95rem; }
.price-row:first-of-type { margin-top: 0.5rem; }
.price-row span:last-child { font-weight: 700; color: var(--success); }
.facility-detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-top: 2.5rem; }
.facility-detail-grid .card img { width: 100%; height: 230px; object-fit: cover; border-radius: var(--radius-md); }
.fact-list { list-style: none; padding: 0; margin: 1rem 0 0; }
.fact-list li { padding: 0.5rem 0; border-top: 1px solid var(--border); display: flex; gap: 0.6rem; align-items: center; }
.fact-list li:first-child { border-top: none; }
.fact-list i { color: var(--success); width: 1.2rem; text-align: center; }
.facilities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 2.5rem; }
.facilities-grid .card img { width: 100%; height: 220px; object-fit: cover; border-radius: var(--radius-md); }
.room-config-table { margin-top: 2.5rem; }
.room-config-table table { width: 100%; }
.cta-strip { margin-top: 5rem; text-align: center; background: var(--bg-secondary); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 3rem 2rem; }
.cta-strip h2 { margin-bottom: 0.5rem; }
.cta-strip .btn-primary { margin-top: 1rem; display: inline-block; }
@media (max-width: 768px) { .room-config-table table { display: block; overflow-x: auto; } }
</style>
</head>
<body>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/public_nav.php'; ?>
<?php endif; ?>

<section class="page-hero">
    <h1>Our <span style="background: linear-gradient(135deg, var(--primary-dark), var(--accent-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Facilities</span></h1>
    <p>Everything you need for comfortable, focused university living under one roof, with the fixed two-floor room structure now in use.</p>
</section>

<div style="max-width:1250px;margin:3.5rem auto;padding:0 2rem;">
    <section class="section-block">
        <h2>Building Overview</h2>
        <p class="section-sub" style="margin-bottom:3rem;">A quick look at how eHostel is laid out, floor by floor.</p>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-icon"><i class="fa-solid fa-building"></i></div>
                <div class="overview-number">2</div>
                <div class="overview-label">Residential Floors</div>
            </div>
            <div class="overview-card">
                <div class="overview-icon"><i class="fa-solid fa-door-closed"></i></div>
                <div class="overview-number">10</div>
                <div class="overview-label">Rooms Per Floor</div>
            </div>
            <div class="overview-card">
                <div class="overview-icon"><i class="fa-solid fa-bed"></i></div>
                <div class="overview-number">20</div>
                <div class="overview-label">Total Rooms</div>
            </div>
            <div class="overview-card">
                <div class="overview-icon"><i class="fa-solid fa-users"></i></div>
                <div class="overview-number">30</div>
                <div class="overview-label">Total Beds</div>
            </div>
        </div>
    </section>

    <section class="section-block">
        <h2>Room Configuration</h2>
        <p class="section-sub">The hostel now uses a fixed layout: first floor single rooms and second floor double rooms.</p>

        <div class="room-config-table card">
            <table>
                <thead>
                    <tr>
                        <th>Floor</th>
                        <th>Room Type</th>
                        <th>Room Numbers</th>
                        <th>Bed Numbers</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>First Floor</td>
                        <td>Single</td>
                        <td>F1/01 - F1/10</td>
                        <td>F1/01/A1 - F1/10/A10</td>
                        <td>1 bed per room</td>
                    </tr>
                    <tr>
                        <td>Second Floor</td>
                        <td>Double</td>
                        <td>F2/01 - F2/10</td>
                        <td>F2/01/A1, F2/01/B1 ... F2/10/A10, F2/10/B10</td>
                        <td>2 beds per room</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="section-block">
        <h2>Bedrooms</h2>
        <p class="section-sub">Choose from the two room types now supported by the hostel layout. Every room includes a study desk, chair, and wardrobe.</p>

        <div class="room-types-grid">
            <div class="card room-type-card">
                <img src="images/images-new/One-Room-Living-How-To-Organise-Your-Hostel-Room-19.webp" alt="Single room">
                <h3>First Floor Single Room</h3>
                <span class="bed-badge"><i class="fa-solid fa-bed"></i> 1 Bed</span>
                <p style="text-align:justify">Private room for students who want extra quiet and space to focus.</p>
                <div class="price-row"><span>Room Range</span><span>F1/01 - F1/10</span></div>
                <div class="price-row"><span>Bed Range</span><span>A1 - A10</span></div>
            </div>

            <div class="card room-type-card">
                <img src="images/images-new/images.jpg" alt="Double sharing room">
                <h3>Second Floor Double Room</h3>
                <span class="bed-badge"><i class="fa-solid fa-bed"></i> 2 Beds</span>
                <p style="text-align:justify">Shared room for two students with separate bed space and study positions.</p>
                <div class="price-row"><span>Room Range</span><span>F2/01 - F2/10</span></div>
                <div class="price-row"><span>Bed Range</span><span>A1/B1 - A10/B10</span></div>
            </div>

            <div class="card room-type-card">
                <img src="images/images-new/a-bunk-bed-with-two-desks-and-a-laptop-photo.jpeg" alt="Fixed hostel bed layout">
                <h3>Fixed Bed Allocation</h3>
                <span class="bed-badge"><i class="fa-solid fa-list"></i> 30 Beds</span>
                <p style="text-align:justify">The hostel uses a fixed allocation model, so admin room creation is no longer required.</p>
                <div class="price-row"><span>System Layout</span><span>2 Floors</span></div>
                <div class="price-row"><span>Status</span><span>Fixed</span></div>
            </div>
        </div>
    </section>

    <section class="section-block">
        <h2>Kitchen, Dining &amp; Study Spaces</h2>
        <p class="section-sub">Common areas are placed on each floor so residents never have far to go.</p>

        <div class="facility-detail-grid">
            <div class="card">
                <img src="images/images-new/youth-hostel-luxembourg-city-restaurant-melting-pot-24.avif" alt="Kitchen and dining area">
                <h3><span class="facility-icon">🍽️</span>Kitchen &amp; Dining Area</h3>
                <ul class="fact-list">
                    <li><i class="fa-solid fa-layer-group"></i> 1 kitchen &amp; dining area per floor</li>
                    <li><i class="fa-solid fa-building"></i> 2 in total across the hostel</li>
                    <li><i class="fa-solid fa-utensils"></i> Shared cooking space with stove, fridge &amp; sink</li>
                    <li><i class="fa-solid fa-chair"></i> Communal dining tables for eating together</li>
                </ul>
            </div>

            <div class="card">
                <img src="images/images-new/images.jpg" alt="Study area">
                <h3><span class="facility-icon">📚</span>Study Area</h3>
                <ul class="fact-list">
                    <li><i class="fa-solid fa-layer-group"></i> 1 study area per floor</li>
                    <li><i class="fa-solid fa-building"></i> 2 in total across the hostel</li>
                    <li><i class="fa-solid fa-users"></i> Quiet study capacity for residents</li>
                    <li><i class="fa-solid fa-volume-xmark"></i> Quiet hours enforced for focused work</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section-block">
        <h2>Special Facilities</h2>
        <p class="section-sub">Beyond rooms and study spaces, eHostel is built around everyday convenience.</p>

        <div class="facilities-grid">
            <div class="card">
                <h3><span class="facility-icon">🔒</span>24/7 Security</h3>
                <p style="text-align:justify">CCTV-monitored entrances, a manned front desk around the clock, and each student is given a key to their own room.</p>
            </div>

            <div class="card">
                <h3><span class="facility-icon">🧺</span>Laundry Services</h3>
                <p style="text-align:justify">On-site self-service and drop-off laundry facilities available daily, with dedicated drying areas on every floor.</p>
            </div>

            <div class="card">
                <h3><span class="facility-icon">📶</span>High speed Wi-Fi facility</h3>
                <p style="text-align:justify">Pay and get high-speed internet access in every room, study area, and common space — built for coursework, research, and downtime alike.</p>
            </div>

            <div class="card">
                <h3><span class="facility-icon">🚑</span>Health &amp; First Aid</h3>
                <p style="text-align:justify">An on-site first-aid station with a resident warden trained in basic emergency response, and quick access to nearby hospitals and pharmacies.</p>
            </div>

            <div class="card">
                <h3><span class="facility-icon">🎮</span>Common Lounge</h3>
                <p style="text-align:justify">A shared recreational lounge with seating, a television, and board games — a space to unwind and connect with fellow residents.</p>
            </div>

            <div class="card">
                <h3><span class="facility-icon">🅿️</span>Bicycle &amp; Parking</h3>
                <p style="text-align:justify">Secure bicycle racks and limited two-wheeler parking available for residents commuting around Colombo 3.</p>
            </div>
        </div>
    </section>

    <div class="cta-strip">
        <h2>Ready to Apply?</h2>
        <p>Review the fixed room layout, then submit your accommodation request online.</p>
        <a href="register.php" class="btn btn-primary">Apply For Accommodation</a>
    </div>
</div>

<?php if ($loggedIn): ?>
    <?php include __DIR__ . '/includes/sidebar_close.php'; ?>
<?php else: ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
<?php endif; ?>

</body>
</html>
