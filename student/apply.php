<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

$userStmt = mysqli_prepare($conn, "SELECT full_name, nic_no, contact_no, emergency_contact, address, district, academic_year, campus, faculty, degree_program, distance_km, gender, date_of_birth FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($userStmt, "i", $uid);
mysqli_stmt_execute($userStmt);
$studentProfile = mysqli_stmt_get_result($userStmt)->fetch_assoc() ?: [];

$stmt = mysqli_prepare($conn, "SELECT * FROM applications WHERE user_id = ? ORDER BY application_id DESC LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt)->fetch_assoc();

$canApply = !$existing || $existing['status'] === 'rejected';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canApply) {
    $preferredRoomType = $_POST['preferred_room_type'] ?? 'shared';
    $dbRoomType = $preferredRoomType === 'single' ? 'single' : 'shared';
    $nicNo = trim($_POST['nic_no'] ?? '');
    $contactNo = trim($_POST['contact_no'] ?? '');
    $emergencyContact = trim($_POST['emergency_contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $academicYear = trim($_POST['academic_year'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $campus = trim($_POST['campus'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $degreeProgram = trim($_POST['degree_program'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $distanceKmRaw = trim($_POST['distance_km'] ?? '');
    $distanceKm = $distanceKmRaw === '' ? null : (float) $distanceKmRaw;
    $gender = $gender === '' ? null : $gender;
    $dateOfBirth = $dateOfBirth === '' ? null : $dateOfBirth;

    if ($nicNo === '' || $contactNo === '' || $address === '' || $academicYear === '') {
        $error = 'Please complete NIC, contact number, address, and academic year.';
    } elseif ($distanceKm !== null && $distanceKm < 0) {
        $error = 'Distance from campus must be a positive value.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $updateUserStmt = mysqli_prepare($conn, "UPDATE users SET nic_no=?, contact_no=?, emergency_contact=?, address=?, academic_year=?, district=?, campus=?, faculty=?, degree_program=?, distance_km=?, gender=?, date_of_birth=? WHERE user_id=?");
            mysqli_stmt_bind_param($updateUserStmt, "sssssssssdssi", $nicNo, $contactNo, $emergencyContact, $address, $academicYear, $district, $campus, $faculty, $degreeProgram, $distanceKm, $gender, $dateOfBirth, $uid);
            mysqli_stmt_execute($updateUserStmt);

            $ins = mysqli_prepare($conn, "INSERT INTO applications (user_id, preferred_room_type, nic_no, address, academic_year, applied_date, status) VALUES (?, ?, ?, ?, ?, CURDATE(), 'pending')");
            mysqli_stmt_bind_param($ins, "issss", $uid, $dbRoomType, $nicNo, $address, $academicYear);
            mysqli_stmt_execute($ins);

            mysqli_commit($conn);
            $success = 'Your hostel application has been submitted successfully and is pending administrative review.';
            $canApply = false;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = 'Application submission failed: ' . $e->getMessage();
        }
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
                <span style="font-family:var(--font-serif);font-size:1.35rem;color:var(--primary-dark);">1. Student &amp; Residence Details</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" class="input-luxury" value="<?= h($_SESSION['full_name'] ?? '') ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="nic_no">NIC No. *</label>
                    <input type="text" id="nic_no" name="nic_no" class="input-luxury" required value="<?= h($_POST['nic_no'] ?? ($studentProfile['nic_no'] ?? '')) ?>" placeholder="e.g. 200012345678 or 200012345V">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_no">Contact No. *</label>
                    <input type="text" id="contact_no" name="contact_no" class="input-luxury" required value="<?= h($_POST['contact_no'] ?? ($studentProfile['contact_no'] ?? '')) ?>" placeholder="e.g. 0771234567">
                </div>
                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact</label>
                    <input type="text" id="emergency_contact" name="emergency_contact" class="input-luxury" value="<?= h($_POST['emergency_contact'] ?? ($studentProfile['emergency_contact'] ?? '')) ?>" placeholder="Parent/Guardian contact">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="input-luxury">
                        <option value="" <?= (($_POST['gender'] ?? ($studentProfile['gender'] ?? '')) === '') ? 'selected' : '' ?>>Select Gender</option>
                        <option value="Male" <?= (($_POST['gender'] ?? ($studentProfile['gender'] ?? '')) === 'Male') ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= (($_POST['gender'] ?? ($studentProfile['gender'] ?? '')) === 'Female') ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= (($_POST['gender'] ?? ($studentProfile['gender'] ?? '')) === 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="input-luxury" value="<?= h($_POST['date_of_birth'] ?? ($studentProfile['date_of_birth'] ?? '')) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Permanent Address *</label>
                <textarea id="address" name="address" class="input-luxury" rows="3" required placeholder="Enter your residential address"><?= h($_POST['address'] ?? ($studentProfile['address'] ?? '')) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="district">District</label>
                    <input type="text" id="district" name="district" class="input-luxury" value="<?= h($_POST['district'] ?? ($studentProfile['district'] ?? '')) ?>" placeholder="e.g. Colombo">
                </div>
                <div class="form-group">
                    <label for="distance_km">Distance from Campus (km)</label>
                    <input type="number" id="distance_km" name="distance_km" min="0" step="0.1" class="input-luxury" value="<?= h($_POST['distance_km'] ?? ($studentProfile['distance_km'] ?? '')) ?>" placeholder="e.g. 42.5">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="academic_year">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" class="input-luxury" required value="<?= h($_POST['academic_year'] ?? ($studentProfile['academic_year'] ?? '')) ?>" placeholder="e.g. 2nd Year">
                </div>
                <div class="form-group">
                    <label for="preferred_room_type">Preferred Accommodation Type *</label>
                    <select id="preferred_room_type" name="preferred_room_type" class="input-luxury" required>
                        <option value="single" <?= (($_POST['preferred_room_type'] ?? 'shared') === 'single') ? 'selected' : '' ?>>Single Room</option>
                        <option value="shared" <?= (($_POST['preferred_room_type'] ?? 'shared') === 'shared') ? 'selected' : '' ?>>Double Room</option>
                    </select>
                </div>
            </div>

            <div style="border-bottom:1px solid var(--border);padding:1rem 0 0.75rem;margin:0.75rem 0 1.5rem;">
                <span style="font-family:var(--font-serif);font-size:1.35rem;color:var(--primary-dark);">2. Academic Information</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="campus">Campus</label>
                    <input type="text" id="campus" name="campus" class="input-luxury" value="<?= h($_POST['campus'] ?? ($studentProfile['campus'] ?? '')) ?>" placeholder="e.g. Colombo Campus">
                </div>
                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <input type="text" id="faculty" name="faculty" class="input-luxury" value="<?= h($_POST['faculty'] ?? ($studentProfile['faculty'] ?? '')) ?>" placeholder="e.g. Faculty of Science">
                </div>
            </div>

            <div class="form-group">
                <label for="degree_program">Degree Program</label>
                <input type="text" id="degree_program" name="degree_program" class="input-luxury" value="<?= h($_POST['degree_program'] ?? ($studentProfile['degree_program'] ?? '')) ?>" placeholder="e.g. BSc in Computer Science">
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
