<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

// If logged in, redirect to dashboard
if (is_logged_in()) {
    if (current_role() === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit;
}

$base = '';
$active = 'home';

// Fetch stats for live availability
$vacant_count = 0;
$occupied_count = 0;
$total_beds = 0;
$student_count = 0;

$res1 = mysqli_query($conn, "SELECT COUNT(*) FROM beds WHERE status = 'vacant'");
if ($res1) $vacant_count = mysqli_fetch_row($res1)[0];

$res2 = mysqli_query($conn, "SELECT COUNT(*) FROM beds WHERE status = 'occupied'");
if ($res2) $occupied_count = mysqli_fetch_row($res2)[0];

$total_beds = $vacant_count + $occupied_count;

$res3 = mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'student'");
if ($res3) $student_count = mysqli_fetch_row($res3)[0];

// Latest announcement
$ann = mysqli_query($conn, "SELECT title, content, posted_date FROM announcements ORDER BY posted_date DESC LIMIT 1");
$latest = $ann ? mysqli_fetch_assoc($ann) : null;

$hero_images = [
    'images/hSpLU.jpg',
    'images/9dtpq.jpg',
    'images/zMAnY.jpg',
    'images/RV7BY.jpg',
    'images/7FEub.jpg',
    'images/s87QX.jpg',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>eHostel — Premier University Hostel Management System</title>
<link rel="stylesheet" href="css/style.css?v=20260729">
<style>
/* Custom Hero & Section Styles for FURNIVIZ Minimalist Landing Page */
.hero-wrapper {
    position: relative;
    width: 100%;
    min-height: 92vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    overflow: hidden;
}

.hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transform: scale(1.04);
    transition: opacity 1.1s ease, transform 1.4s ease;
}

.hero-slide.active {
    opacity: 1;
    transform: scale(1);
}

.hero-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, rgba(18, 40, 40, 0.88) 0%, rgba(18, 40, 40, 0.5) 60%, rgba(18, 40, 40, 0.75) 100%);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    padding: 3rem 2rem;
    text-align: center;
}

.hero-tagline {
    font-size: 0.85rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 1.2rem;
    font-weight: 600;
}

.hero-title {
    font-family: var(--font-serif);
    font-size: 4rem;
    font-weight: 400;
    line-height: 1.15;
    margin-bottom: 1.5rem;
    color: #ffffff;
}

.hero-desc {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, 0.88);
    max-width: 680px;
    margin: 0 auto 2.5rem;
    line-height: 1.7;
    font-weight: 300;
}

.hero-actions {
    display: flex;
    gap: 1.25rem;
    justify-content: center;
    flex-wrap: wrap;
}

.scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.scroll-indicator span {
    font-size: 0.68rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
}

.scroll-line {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.scroll-line::after {
    content: '';
    position: absolute;
    top: -100%; left: 0; width: 100%; height: 100%;
    background: var(--accent);
    animation: scrollAnimation 2s infinite ease-in-out;
}

@keyframes scrollAnimation {
    0% { top: -100%; }
    100% { top: 100%; }
}

/* Feature Grid & Split Sections */
.split-section {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    min-height: 75vh;
    align-items: center;
    background: var(--surface);
    overflow: hidden;
}

.split-section-reverse {
    grid-template-columns: 1fr 1.1fr;
}

.split-image-wrapper {
    position: relative;
    height: 100%;
    min-height: 450px;
    overflow: hidden;
}

.split-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 1.2s var(--ease-smooth);
}

.split-image-wrapper:hover .split-image {
    transform: scale(1.05);
}

.split-content {
    padding: 5rem 6rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Rooms Showcase */
.showcase-section {
    padding: 7rem 3rem;
    background: var(--bg-primary);
}

.room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
    max-width: 1300px;
    margin: 3rem auto 0;
}

.room-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--border);
    transition: all var(--transition-normal);
}

.room-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
    border-color: var(--border-accent);
}

.room-card-img {
    height: 240px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.8s var(--ease-smooth);
}

.room-card:hover .room-card-img {
    transform: scale(1.04);
}

.room-card-body {
    padding: 1.75rem;
}

.room-card-title {
    font-family: var(--font-serif);
    font-size: 1.5rem;
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
}

/* Parallax Breaks */
.parallax-break {
    position: relative;
    height: 60vh;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #ffffff;
}

.parallax-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(18, 40, 40, 0.76);
}

.parallax-content {
    position: relative;
    z-index: 2;
    max-width: 850px;
    padding: 2rem;
}

@media (max-width: 1024px) {
    .split-section, .split-section-reverse { grid-template-columns: 1fr; }
    .split-content { padding: 3.5rem 2rem; }
    .hero-title { font-size: 2.8rem; }
    .parallax-break { background-attachment: scroll; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/public_nav.php'; ?>

<!-- Hero Section with zMAnY.jpg -->
<section class="hero-wrapper" id="hero-wrapper">
    <?php foreach ($hero_images as $index => $heroImage): ?>
        <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= h($heroImage) ?>');"></div>
    <?php endforeach; ?>
    <div class="hero-overlay"></div>
    <div class="hero-content reveal">
        <span class="hero-tagline">University Resident Portal</span>
        <h1 class="hero-title">Elevate Your University Accommodation Experience</h1>
        <p class="hero-desc">
            Welcome to eHostel. Seamless room application, live occupancy tracking, secure resident records, and instant notices — all within a refined digital environment.
        </p>
        <div class="hero-actions">
            <a href="register.php" class="btn btn-luxury btn-accent">Apply For Hostel</a>
            <a href="facilities.php" class="btn btn-luxury btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.6);">Explore Facilities</a>
        </div>
    </div>

    <div class="scroll-indicator">
        <span>Scroll Down</span>
        <div class="scroll-line"></div>
    </div>
</section>

<!-- Section 1: Split Section - About eHostel -->
<section class="split-section reveal">
    <div class="split-image-wrapper">
        <img src="images/Gemini_Generated_Image_3rnsg43rnsg43rns.png" alt="University Hostel Living Room" class="split-image">
    </div>
    <div class="split-content">
        <span class="section-label">THE eHOSTEL EXPERIENCE</span>
        <h2 class="serif-heading" style="margin-bottom:1.25rem;">A Modern Standard in University Living</h2>
        <p style="font-size:1.02rem;line-height:1.8;margin-bottom:1.75rem;">
            Designed specifically for modern university students, eHostel combines real-time bed allocation, administrative transparency, and quiet study environments. Located within walking distance of key university faculties, libraries, and transport hubs.
        </p>
        <div>
            <a href="facilities.php" class="btn btn-luxury btn-outline">Read About Our Premises &rarr;</a>
        </div>
    </div>
</section>

<!-- Section 2: Showcase Section - Rooms -->
<section class="showcase-section reveal">
    <div style="text-align:center;max-width:700px;margin:0 auto;">
        <span class="section-label">ACCOMMODATION OPTIONS</span>
        <h2 class="section-heading-center">Designed for Focus &amp; Comfort</h2>
        <p>Explore our single and shared room configurations, equipped with dedicated study desks and high-speed Wi-Fi.</p>
    </div>

    <div class="room-grid">
        <div class="room-card">
            <div style="overflow:hidden;">
                <img src="images/Gemini_Generated_Image_5f4nif5f4nif5f4n.png" alt="Single Study Suite" class="room-card-img">
            </div>
            <div class="room-card-body">
                <span class="badge badge-success" style="margin-bottom:0.6rem;">Private Unit</span>
                <h3 class="room-card-title">Single Study Suite</h3>
                <p style="font-size:0.9rem;margin-bottom:1.25rem;">Private room designed for maximum quietness and individual academic focus with ergonomic workspace.</p>
                <a href="facilities.php" style="font-weight:600;font-size:0.85rem;color:var(--accent-dark);">View Room Details &rarr;</a>
            </div>
        </div>

        <div class="room-card">
            <div style="overflow:hidden;">
                <img src="images/Gemini_Generated_Image_99wmm299wmm299wm.png" alt="Double Shared Room" class="room-card-img">
            </div>
            <div class="room-card-body">
                <span class="badge badge-warning" style="margin-bottom:0.6rem;">2 Beds</span>
                <h3 class="room-card-title">Double Sharing Room</h3>
                <p style="font-size:0.9rem;margin-bottom:1.25rem;">Ideal balance of companionship and quiet study space. Includes personal wardrobes and twin desks.</p>
                <a href="facilities.php" style="font-weight:600;font-size:0.85rem;color:var(--accent-dark);">View Room Details &rarr;</a>
            </div>
        </div>

        <div class="room-card">
            <div style="overflow:hidden;">
                <img src="images/Gemini_Generated_Image_o2fwhdo2fwhdo2fw.png" alt="Triple Sharing Room" class="room-card-img">
            </div>
            <div class="room-card-body">
                <span class="badge badge-muted" style="margin-bottom:0.6rem;">3 Beds</span>
                <h3 class="room-card-title">Triple Shared Dormitory</h3>
                <p style="font-size:0.9rem;margin-bottom:1.25rem;">Budget-friendly option featuring custom bunk beds, ample storage space, and individual reading lights.</p>
                <a href="facilities.php" style="font-weight:600;font-size:0.85rem;color:var(--accent-dark);">View Room Details &rarr;</a>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Parallax Image Break -->
<section class="parallax-break reveal" style="background-image: url('images/Gz95b.jpg');">
    <div class="parallax-overlay"></div>
    <div class="parallax-content">
        <span class="section-label" style="color:var(--accent);">STREAMLINED PROCESS</span>
        <h2 class="serif-heading" style="color:#fff;font-size:3rem;margin-bottom:1.5rem;">Apply Online &rarr; Instant Verification &rarr; Move In</h2>
        <a href="register.php" class="btn btn-luxury btn-accent">Submit Student Application</a>
    </div>
</section>

<!-- Section 4: Live Availability Stats Section -->
<section style="padding:6rem 3rem;background:var(--surface);" class="reveal">
    <div style="max-width:1200px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:3.5rem;">
            <span class="section-label">REAL-TIME DASHBOARD</span>
            <h2 class="section-heading-center">Current Hostel Status</h2>
            <p>Live metrics pulled directly from our central bed and occupancy database.</p>
        </div>

        <div class="card-grid">
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--success);"><?= $vacant_count ?></div>
                <div class="label">Vacant Beds Available</div>
            </div>
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--warning);"><?= $occupied_count ?></div>
                <div class="label">Beds Currently Occupied</div>
            </div>
            <div class="stat-card" style="text-align:center;">
                <div class="num" style="font-size:3rem;color:var(--primary);"><?= $student_count ?></div>
                <div class="label">Registered Students</div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Split Section - Key Features & Capabilities -->
<section class="split-section split-section-reverse reveal">
    <div class="split-content" style="padding:5rem 6rem;">
        <span class="section-label">CORE CAPABILITIES</span>
        <h2 class="serif-heading" style="margin-bottom:1.25rem;">Built for Administrative Transparency &amp; Convenience</h2>
        <p style="font-size:1.02rem;line-height:1.8;margin-bottom:1.75rem;">
            eHostel powers every stage of university accommodation — from initial online application forms to automated bed assignments, warden reviews, and noticeboard announcements.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
            <div>
                <strong style="color:var(--primary-dark);display:block;font-size:0.95rem;margin-bottom:0.25rem;">🔐 Role Access</strong>
                <span style="font-size:0.85rem;color:var(--text-secondary);">Role-based student and warden portals.</span>
            </div>
            <div>
                <strong style="color:var(--primary-dark);display:block;font-size:0.95rem;margin-bottom:0.25rem;">📄 Online Apply</strong>
                <span style="font-size:0.85rem;color:var(--text-secondary);">Direct room preference applications.</span>
            </div>
            <div>
                <strong style="color:var(--primary-dark);display:block;font-size:0.95rem;margin-bottom:0.25rem;">🛏️ Bed Allocation</strong>
                <span style="font-size:0.85rem;color:var(--text-secondary);">Automated vacant bed assignment.</span>
            </div>
            <div>
                <strong style="color:var(--primary-dark);display:block;font-size:0.95rem;margin-bottom:0.25rem;">📢 Noticeboard</strong>
                <span style="font-size:0.85rem;color:var(--text-secondary);">Instant official warden notices.</span>
            </div>
        </div>

        <div>
            <a href="functionalities.php" class="btn btn-luxury btn-outline">Explore All System Features &rarr;</a>
        </div>
    </div>

    <div class="split-image-wrapper">
        <img src="images/Gemini_Generated_Image_pq1l7vpq1l7vpq1l.png" alt="Hostel Common Study Area" class="split-image">
    </div>
</section>

<!-- Section 6: Latest Noticeboard Section -->
<?php if ($latest): ?>
<section style="padding:6rem 3rem;background:var(--bg-primary);" class="reveal">
    <div style="max-width:1100px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:3rem;">
            <span class="section-label">COMMUNICATION BOARD</span>
            <h2 class="section-heading-center">Latest Hostel Notice</h2>
        </div>
        <div class="card" style="border-left:4px solid var(--accent);box-shadow:var(--shadow-md);padding:3rem;background:var(--surface);">
            <h3 style="color:var(--primary-dark);font-family:var(--font-serif);font-size:2.2rem;margin-bottom:0.85rem;"><?= h($latest['title']) ?></h3>
            <p style="color:var(--text-secondary);font-size:1.05rem;margin:1rem 0 1.5rem;line-height:1.8;"><?= h($latest['content']) ?></p>
            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1.25rem;border-top:1px solid var(--border);flex-wrap:wrap;gap:1rem;">
                <span class="badge badge-success" style="padding:0.35rem 0.85rem;font-size:0.75rem;">Official Notice</span>
                <span style="font-size:0.85rem;color:var(--text-muted);font-weight:500;">
                    Posted on <?= date('d M Y, h:i A', strtotime($latest['posted_date'])) ?> &middot; Hostel Administration Office
                </span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 7: Parallax CTA Break -->
<section class="parallax-break reveal" style="background-image: url('images/6Smer.jpg');min-height:55vh;">
    <div class="parallax-overlay" style="background:rgba(18, 40, 40, 0.82);"></div>
    <div class="parallax-content">
        <span class="section-label" style="color:var(--accent);">READY TO JOIN eHOSTEL?</span>
        <h2 class="serif-heading" style="color:#fff;font-size:3.2rem;margin-bottom:1.25rem;">Reserve Your Campus Room Today</h2>
        <p style="color:rgba(255,255,255,0.85);font-size:1.1rem;margin-bottom:2.25rem;max-width:650px;margin-left:auto;margin-right:auto;">
            Submit your student accommodation application online in just a few clicks and monitor approval status.
        </p>
        <div style="display:flex;gap:1.25rem;justify-content:center;flex-wrap:wrap;">
            <a href="register.php" class="btn btn-luxury btn-accent">Apply For Accommodation</a>
            <a href="contact.php" class="btn btn-luxury btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.6);">Contact Warden Office</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Scroll Reveal Script (Pure Vanilla JS) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -20px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal').forEach(el => {
        observer.observe(el);
    });

    const hero = document.getElementById('hero-wrapper');
    if (hero) {
        const slides = Array.from(hero.querySelectorAll('.hero-slide'));

        if (slides.length > 0) {
            let currentIndex = 0;

            const preloadImage = (src) => {
                const image = new Image();
                image.src = src;
            };

            slides.forEach(slide => {
                const backgroundImage = slide.style.backgroundImage;
                const match = backgroundImage.match(/url\(["']?(.*?)["']?\)/);
                if (match && match[1]) {
                    preloadImage(match[1]);
                }
            });

            window.setInterval(() => {
                slides[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].classList.add('active');
            }, 3500);
        }
    }
});
</script>

</body>
</html>
