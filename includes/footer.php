<?php
$base = $base ?? '';
?>
<footer class="footer-luxury">
    <div class="footer-container">
        <div class="footer-intro">
            <div class="footer-intro-copy">
                <span class="footer-kicker">University hostel portal</span>
                <h3 class="footer-intro-title">A calmer way to manage accommodation, occupancy, and student support.</h3>
                <p class="footer-intro-desc">Built for students and administrators who need clear hostel information, fast access, and a polished digital experience.</p>
            </div>
            <div class="footer-intro-actions">
                <a href="<?= $base ?>login.php" class="footer-cta footer-cta-primary">Login</a>
                <a href="<?= $base ?>facilities.php" class="footer-cta footer-cta-secondary">Explore rooms</a>
            </div>
        </div>

        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-panel">
                <div class="footer-brand-title">
                    eHostel <span class="footer-tag">UNIVERSITY</span>
                </div>
                <p class="footer-brand-desc">
                    Premier University Resident Management Portal. Engineered for safety, comfort, transparent bed allocation, and student success.
                </p>
                <div class="footer-socials">
                    <a href="https://github.com/NimanthaSupun" target="_blank" rel="noopener noreferrer" class="social-icon-btn" title="GitHub">
                        <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.649.5.5 5.774.5 12.296c0 5.215 3.438 9.635 8.207 11.201.6.113.82-.271.82-.603 0-.297-.011-1.083-.017-2.127-3.338.745-4.042-1.667-4.042-1.667-.546-1.437-1.333-1.819-1.333-1.819-1.09-.772.083-.756.083-.756 1.205.087 1.84 1.27 1.84 1.27 1.07 1.88 2.808 1.337 3.493 1.022.107-.798.419-1.338.762-1.646-2.665-.314-5.467-1.382-5.467-6.153 0-1.358.47-2.467 1.237-3.337-.124-.314-.536-1.577.117-3.287 0 0 1.008-.333 3.3 1.275a11.184 11.184 0 013.005-.418c1.019.005 2.047.143 3.006.418 2.29-1.608 3.296-1.275 3.296-1.275.655 1.71.243 2.973.119 3.287.769.87 1.235 1.979 1.235 3.337 0 4.783-2.808 5.835-5.483 6.141.431.381.815 1.132.815 2.281 0 1.647-.015 2.976-.015 3.381 0 .335.218.722.828.6C20.065 21.928 23.5 17.51 23.5 12.296 23.5 5.774 18.351.5 12 .5z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-panel">
                <h4 class="footer-column-title">Quick Links</h4>
                <a href="<?= $base ?>index.php" class="footer-link">Home Portal</a>
                <a href="<?= $base ?>facilities.php" class="footer-link">Hostel Facilities &amp; Pricing</a>
                <a href="<?= $base ?>rules.php" class="footer-link">Hostel Rules &amp; Policies</a>
                <a href="<?= $base ?>functionalities.php" class="footer-link">System Features</a>
                <a href="<?= $base ?>help.php" class="footer-link">Help &amp; Documentation</a>
            </div>

            <!-- Student Portal Links -->
            <div class="footer-panel">
                <h4 class="footer-column-title">Resident Portal</h4>
                <a href="<?= $base ?>login.php" class="footer-link">Student / Admin Login</a>
                <a href="<?= $base ?>register.php" class="footer-link">Create Student Account</a>
                <a href="<?= $base ?>student/apply.php" class="footer-link">Submit Accommodation Request</a>
                <a href="<?= $base ?>contact.php" class="footer-link">Contact Administration</a>
            </div>

            <!-- Contact & Office -->
            <div class="footer-panel">
                <h4 class="footer-column-title">Contact &amp; Warden Office</h4>
                <div class="footer-contact-item">
                    <span>📍</span> <span>University Campus, Colombo 03, Sri Lanka</span>
                </div>
                <div class="footer-contact-item">
                    <span>✉️</span> <span>ssp@ucsc.cmb.ac.lk</span>
                </div>
                <div class="footer-contact-item">
                    <span>📞</span> <span>+94 11 258 1234</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> eHostel University Management. All Rights Reserved.</span>
            <span class="footer-bottom-note">Designed with luxury minimalism &amp; precision</span>
        </div>
    </div>
</footer>
