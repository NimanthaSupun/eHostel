<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    if (current_role() === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: student/dashboard.php');
    }
    exit;
}

$vacant_count = 0;
$occupied_count = 0;
$student_count = 0;

$res1 = mysqli_query($conn, "SELECT COUNT(*) FROM beds WHERE status = 'vacant'");
if ($res1) {
    $vacant_count = (int) mysqli_fetch_row($res1)[0];
}

$res2 = mysqli_query($conn, "SELECT COUNT(*) FROM beds WHERE status = 'occupied'");
if ($res2) {
    $occupied_count = (int) mysqli_fetch_row($res2)[0];
}

$res3 = mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'student'");
if ($res3) {
    $student_count = (int) mysqli_fetch_row($res3)[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Hostel and Dormitory Resident Management System. Simplify registrations, room allocations, payments, complaints, and visitors.">
    <title>eHostel | Resident Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .hero {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 0;
            min-height: calc(100vh - 72px);
            height: calc(100vh - 72px);
            width: 100%;
            margin: 0;
            padding: 0 2rem;
            background: #ffffff;
            overflow: hidden;
        }
        .hero-content {
            flex: 0 0 42%;
            max-width: 620px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 2rem 2.5rem 0;
        }


        .hero-image {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            align-self: stretch;
            min-height: 100%;
            margin: 0;
            width: 100%;
            height: 100%;
            padding: 5rem 1.25rem;
            overflow: hidden;
        }

        .hero-image img {
            width: calc(100% - 2.5rem);
            height: calc(100% - 10rem);
            min-height: 100%;
            max-height: none;
            border-radius: 0;
            box-shadow: none;
            object-fit: cover;
            display: block;
        }

            
  



        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            color: var(--success);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .hero-title {
            font-size: 3.5rem;
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 4rem;
            color: var(--text-primary);
        }
        .hero-title span {
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .hero-buttons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            padding: 0.95rem 1.35rem;
            font-size: 0.84rem;
            letter-spacing: 0.08em;
            font-weight: 700;
            text-decoration: none;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), background var(--transition-normal), color var(--transition-normal), border-color var(--transition-normal);
            box-shadow: 0 12px 26px rgba(18, 40, 40, 0.08);
        }
        .hero-buttons .btn-primary-link {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #ffffff !important;
            border: 1px solid transparent;
            border-radius: 0;
        }
        .hero-buttons .btn-secondary {
            background: #ffffff;
            border-color: var(--border-strong);
            color: var(--primary-dark);
            border-radius: 0;
        }
        .hero-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(18, 40, 40, 0.12);
        }
        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-strong);
            color: var(--text-primary);
            padding: 0.6rem 1.25rem;
            border-radius: 0;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-normal);
        }
        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent-dark);
        }
        .about-panel {
            margin-top: 4rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(201, 169, 110, 0.12));
            border: 1px solid rgba(16, 185, 129, 0.18);
            border-radius: 0;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .about-kicker {
            display: inline-block;
            margin-bottom: 0.9rem;
            color: var(--accent-dark);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }
        .about-panel p {
            margin: 0;
            text-align: justify;
            font-size: 1.05rem;
            color: var(--text-secondary);
            line-height: 1.9;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
            max-width: 1250px;
            padding: 0 2rem;
            margin-left: auto;
            margin-right: auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }
        .stat-card {
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 2.25rem 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
        }
        .stat-card.vacant::before { background: var(--success); }
        .stat-card.occupied::before { background: var(--danger); }
        .stat-card.students::before { background: var(--accent); }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        .stat-icon.vacant-icon { color: var(--success); }
        .stat-icon.occupied-icon { color: var(--danger); }
        .stat-icon.students-icon { color: var(--accent-dark); }
        .stat-number {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary-dark);
        }
        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 0.5rem;
            margin-bottom: 0;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .section-shell {
            max-width: 1250px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .section-title {
            margin-top: 5rem;
            text-align: center;
        }
        .section-title h2 {
            font-family: var(--font-serif);
            font-size: 2.6rem;
            color: var(--primary-dark);
            margin-bottom: 0.75rem;
        }
        .section-title p {
            max-width: 760px;
            margin: 0 auto;
            color: var(--text-secondary);
        }
        .footer-simple {
            margin-top: 5rem;
            background: var(--primary-dark);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }
        .footer-simple-inner {
            max-width: 1250px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .social-icons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .social-icons a {
            color: #ffffff;
            font-size: 1.05rem;
            text-decoration: none;
        }
        @media (max-width: 968px) {
            .hero {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
                min-height: auto;
                padding: 2rem 1rem;
            }
            .hero-title {
                font-size: 2.75rem;
            }
            .hero-buttons {
                justify-content: center;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .hero-content {
                max-width: none;
            }
            .hero-image {
                align-self: auto;
                width: 100%;
                height: auto;
            }
            .hero-image img {
                min-height: 280px;
                max-height: 420px;
            }
            .hero-image {
                margin-top: 0;
            }
        }
        @media (max-width: 768px) {
            .hero {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .features-grid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/public_nav.php'; ?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title" id="welcome-title">Elevate Your <span>Hostel stay</span> Experience.</h1>
            <p id="welcome-description" style="text-align:justify; color:var(--text-secondary); font-size:1.05rem; line-height:1.8;">
                Welcome to eHostel, your ultimate home away from home. We provide safe, comfortable,
                and affordable living spaces designed specifically for university students. Enjoy 24/7 security, clean study areas,
                and a vibrant community. Book your perfect room in just a few clicks and experience hassle-free student living today.
            </p>
            <div class="hero-buttons" id="welcome-buttons">
                <a href="login.php" class="btn-primary-link">Get Started</a>
                <a href="facilities.php" class="btn-secondary">Explore Rooms Availability</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="images/Gemini_Generated_Image_3rnsg43rnsg43rns.png" alt="eHostel hostel room" />
        </div>
    </section>

    <section class="section-shell">
        <div class="about-panel">
            <span class="about-kicker">About us</span>
            <p>
                Welcome to eHostel, a comfortable and affordable hostel designed exclusively for university students in
                the heart of Colombo 3. Our mission is to provide a welcoming environment where students can focus on their academic goals while enjoying a convenient
                and enjoyable lifestyle. Located close to leading universities, the hostel offers easy access to public transportation, supermarkets, restaurants, cafés,
                pharmacies, hospitals, banks, bookstores, and recreational areas. With everything students need just minutes away, eHostel provides the perfect balance of comfort,
                convenience, and community, making it an ideal place to live, learn, and create lasting friendships throughout university life.
            </p>
        </div>
    </section>

    <section class="features-grid">
        <div class="card">
            <h3 style="text-align:center;">Bedrooms</h3>
            <img src="images/images-new/a-bunk-bed-with-two-desks-and-a-laptop-photo.jpeg" alt="Bedrooms" />
            <p style="text-align:justify">Explore our three room types, single, double and triple sharing room options, ensuring a comfortable stay for every student.</p>
        </div>

        <div class="card">
            <h3 style="text-align:center;">Study Area</h3>
            <img src="images/images-new/images.jpg" alt="Study Area" />
            <p style="text-align:justify">Dedicated study spaces are available on every floor, with three study areas that can comfortably accommodate up to 75 students at a time.</p>
        </div>

        <div class="card">
            <h3 style="text-align:center;">Kitchen &amp; Dinning Area</h3>
            <img src="images/images-new/youth-hostel-luxembourg-city-restaurant-melting-pot-24.avif" alt="Kitchen and Dining Area" />
            <p style="text-align:justify">The hostel provides one spacious dining area alongside a shared kitchen per each floor, where students can cook, dine together or enjoy meals delivered from local eateries.</p>
        </div>
    </section>

    <div class="section-shell" style="text-align:center; margin-top: 2rem;">
        <a href="facilities.php" class="btn-secondary" style="display:inline-flex;">Learn more</a>
    </div>

    <section class="section-shell">
        <h2 style="margin-top: 8rem; text-align: center; font-family:var(--font-serif); color:var(--primary-dark);">Live Availability</h2>
        <p style="text-align:center;font-size:1.1rem;color:var(--text-secondary);">A quick snapshot of where things stand across the hostel right now.</p>

        <section class="stats-grid" id="live-stats">
            <div class="card stat-card vacant">
                <div class="stat-icon vacant-icon"><i class="fa-solid fa-door-open"></i></div>
                <div class="stat-number" id="stat-vacant"><?= $vacant_count ?></div>
                <p class="stat-label">Vacant Beds Available</p>
            </div>

            <div class="card stat-card occupied">
                <div class="stat-icon occupied-icon"><i class="fa-solid fa-lock"></i></div>
                <div class="stat-number" id="stat-occupied"><?= $occupied_count ?></div>
                <p class="stat-label">Beds Currently Occupied</p>
            </div>

            <div class="card stat-card students">
                <div class="stat-icon students-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-number" id="stat-students"><?= $student_count ?></div>
                <p class="stat-label">Registered Students</p>
            </div>
        </section>
    </section>

    <section class="section-shell">
        <h2 style="margin-top: 6rem; text-align: center; font-family:var(--font-serif); color:var(--primary-dark);">Special Features</h2>

        <section class="features-grid" style="margin-top: 2.5rem;">
            <div class="card">
                <h3>🚪 Room Allocation</h3>
                <p>Track real-time room availability, occupants count, types of rooms, and apply instantly.</p>
            </div>
            <div class="card">
                <h3>📋 Visitor Records</h3>
                <p>Safety register logbook to track guest check-ins and check-outs securely.</p>
            </div>
            <div class="card">
                <h3>📢 Digital Noticeboard</h3>
                <p>Receive announcements about repairs, events, or regulatory policies.</p>
            </div>
            <div class="card">
                <h3>🔒 24/7 Security</h3>
                <p>CCTV-monitored entrances, a manned front desk around active for 24/7.</p>
            </div>
            <div class="card">
                <h3>🧺 Laundry Services</h3>
                <p>On-site self-service facilities are available daily, or you can pay for our convenient drop-off laundry service to have it done for you.</p>
            </div>
        </section>

        <div style="text-align:center; margin-top: 2rem;">
            <a href="facilities.php" class="btn-secondary" style="display:inline-flex;">Learn more</a>
        </div>
    </section>
</main>

<footer class="footer-simple">
    <div class="footer-simple-inner" style="justify-content:space-between; gap:1.5rem; align-items:center;">
        <div class="social-icons">
            <a href="https://www.google.com/"><i class="fa-brands fa-facebook"></i></a>
            <a href="https://www.google.com/"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://www.google.com/"><i class="fa-brands fa-twitter"></i></a>
            <a href="https://www.google.com/"><i class="fa-brands fa-instagram"></i></a>
        </div>
        <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; justify-content:center; color:rgba(255,255,255,0.94);">
            <span>📍 University Campus, Colombo 03, Sri Lanka</span>
            <span>✉️ ssp@ucsc.cmb.ac.lk</span>
            <span>📞 +94 11 258 1234</span>
        </div>
    </div>
</footer>

</body>
</html>
