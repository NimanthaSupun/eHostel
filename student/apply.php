<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

// check for an existing pending/approved application
$stmt = mysqli_prepare($conn, "SELECT * FROM applications WHERE user_id = ? ORDER BY application_id DESC LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt)->fetch_assoc();

$canApply = !$existing || $existing['status'] === 'rejected';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canApply) {
    $room_type = $_POST['preferred_room_type'] ?? 'shared';
    $allowed_types = ['single', 'shared', 'single_ac', 'single_fan', 'double_ac', 'double_fan', 'triple_ac', 'triple_fan'];
    $room_type = in_array($room_type, $allowed_types) ? $room_type : 'shared';

    // Simplified room type map to existing enum ('single','shared') or fallback
    $db_room_type = (strpos($room_type, 'single') !== false) ? 'single' : 'shared';

    $ins = mysqli_prepare($conn, "INSERT INTO applications (user_id, preferred_room_type, applied_date, status) VALUES (?, ?, CURDATE(), 'pending')");
    mysqli_stmt_bind_param($ins, "is", $uid, $db_room_type);

    if (mysqli_stmt_execute($ins)) {
        $success = 'Your hostel application has been submitted successfully and is pending administrative review.';
        $canApply = false;
    } else {
        $error = 'Something went wrong. Please try again.';
    }
}

$base = '../';
$active = 'apply';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Hostel — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">ACCOMMODATION APPLICATION</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">Apply for Hostel Accommodation</h1>
        <p>Complete the student application form below to request room allocation.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card" style="max-width:850px;">
    <?php if ($existing && $existing['status'] !== 'rejected'): ?>
        <div style="padding:1rem 0;">
            <h3 class="serif-heading" style="color:var(--primary-dark);">Existing Application On File</h3>
            <p>You currently have an active application with status:
               <span class="badge badge-<?= $existing['status'] === 'approved' ? 'success' : 'warning' ?>" style="font-size:0.85rem;">
                   <?= h(strtoupper($existing['status'])) ?>
               </span>
            </p>
            <p style="color:var(--text-muted);font-size:0.9rem;margin-top:0.75rem;">
                Students may only re-apply if their previous application has been processed as rejected.
            </p>
            <div style="margin-top:1.5rem;">
                <a href="dashboard.php" class="btn btn-luxury btn-outline">&larr; Return to Dashboard</a>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" action="apply.php">
            
            <div style="border-bottom:1px solid var(--border);padding-bottom:0.75rem;margin-bottom:1.5rem;">
                <span style="font-family:var(--font-serif);font-size:1.35rem;color:var(--primary-dark);">1. Personal &amp; Contact Details</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="student_name">Full Name *</label>
                    <input type="text" id="student_name" name="student_name" class="input-luxury" required value="<?= h($_SESSION['full_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" class="input-luxury" min="16" max="40" placeholder="e.g. 21">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="input-luxury">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="input-luxury">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact No. *</label>
                    <input type="tel" id="emergency_contact" name="emergency_contact" class="input-luxury" placeholder="e.g. 071 987 6543">
                </div>
                <div class="form-group">
                    <label for="district">Home District</label>
                    <select id="district" name="district" class="input-luxury">
                        <option value="Colombo">Colombo</option>
                        <option value="Gampaha">Gampaha</option>
                        <option value="Kalutara">Kalutara</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Galle">Galle</option>
                        <option value="Matara">Matara</option>
                        <option value="Kurunegala">Kurunegala</option>
                        <option value="Jaffna">Jaffna</option>
                        <option value="Other">Other District</option>
                    </select>
                </div>
            </div>

            <div style="border-bottom:1px solid var(--border);padding-bottom:0.75rem;margin:2rem 0 1.5rem;">
                <span style="font-family:var(--font-serif);font-size:1.35rem;color:var(--primary-dark);">2. Academic &amp; Distance Info</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <input type="text" id="faculty" name="faculty" class="input-luxury" placeholder="e.g. Faculty of Science">
                </div>
                <div class="form-group">
                    <label for="distance_km">Distance from Home to Campus (KM)</label>
                    <input type="number" step="0.1" id="distance_km" name="distance_km" class="input-luxury" placeholder="e.g. 45.5">
                </div>
            </div>

            <div style="border-bottom:1px solid var(--border);padding-bottom:0.75rem;margin:2rem 0 1.5rem;">
                <span style="font-family:var(--font-serif);font-size:1.35rem;color:var(--primary-dark);">3. Room Preference</span>
            </div>

            <div class="form-group">
                <label for="preferred_room_type">Preferred Accommodation Type *</label>
                <select id="preferred_room_type" name="preferred_room_type" class="input-luxury" required>
                    <option value="single">Single Room (Private Study Unit)</option>
                    <option value="shared" selected>Shared Room (Double / Triple Sharing)</option>
                </select>
                <div class="form-hint">Note: Final room assignments depend on vacant bed availability.</div>
            </div>

            <div style="margin-top:2.25rem;">
                <button type="submit" class="btn btn-luxury btn-accent btn-block">Submit Application to Warden</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
