<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact_no'] ?? '');
    $nic_no = trim($_POST['nic_no'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $campus = trim($_POST['campus'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $degree_program = trim($_POST['degree_program'] ?? '');
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['date_of_birth'] ?? '');
    $new_password = trim($_POST['password'] ?? '');

    if ($full_name === '') {
        $error = 'Full name is required.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $updateFields = [
                'full_name' => $full_name,
                'email' => $email === '' ? ($user['email'] ?? null) : $email,
                'contact_no' => $contact === '' ? ($user['contact_no'] ?? null) : $contact,
                'nic_no' => $nic_no === '' ? ($user['nic_no'] ?? null) : $nic_no,
                'address' => $address === '' ? ($user['address'] ?? null) : $address,
                'academic_year' => $academic_year === '' ? ($user['academic_year'] ?? null) : $academic_year,
                'district' => $district === '' ? ($user['district'] ?? null) : $district,
                'campus' => $campus === '' ? ($user['campus'] ?? null) : $campus,
                'faculty' => $faculty === '' ? ($user['faculty'] ?? null) : $faculty,
                'degree_program' => $degree_program === '' ? ($user['degree_program'] ?? null) : $degree_program,
                'emergency_contact' => $emergency_contact === '' ? ($user['emergency_contact'] ?? null) : $emergency_contact,
                'gender' => $gender === '' ? null : $gender,
                'date_of_birth' => $dob === '' ? null : $dob,
            ];

            $setSql = [];
            $types = '';
            $params = [];

            foreach ($updateFields as $field => $value) {
                $setSql[] = "$field = ?";
                $types .= 's';
                $params[] = $value;
            }

            if ($new_password !== '') {
                if (strlen($new_password) < 4) {
                    throw new Exception('Password must be at least 4 characters long.');
                }
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $setSql[] = 'password = ?';
                $types .= 's';
                $params[] = $hash;
            }

            $stmt = mysqli_prepare($conn, "UPDATE users SET " . implode(', ', $setSql) . " WHERE user_id=?");
            $types .= 'i';
            $params[] = $uid;

            $refs = [];
            foreach ($params as $key => $value) {
                $refs[$key] = &$params[$key];
            }
            $args = array_merge([$stmt, $types], $refs);
            call_user_func_array('mysqli_stmt_bind_param', $args);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Could not update profile details.');
            }

            $_SESSION['full_name'] = $full_name;
            mysqli_commit($conn);
            $success = 'Profile details updated successfully.';
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

$studentId = $user['student_id'] ?? '';
$studentIdLabel = $studentId !== '' ? h($studentId) : 'Pending';

$base = '../';
$active = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">STUDENT ACCOUNT</span>
        <h1 class="serif-heading" style="font-size:2.4rem;">My Profile</h1>
        <p>Update your personal information and contact details.</p>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card" style="max-width:680px;">
    <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
        <div style="width:68px;height:68px;border-radius:50%;background:var(--primary);color:var(--accent);border:2px solid var(--accent);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:600;">
            <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
            <h3 class="serif-heading" style="margin:0;font-size:1.6rem;"><?= h($user['full_name']) ?></h3>
            <span style="font-size:0.85rem;color:var(--text-muted);">Registered Student &middot; <?= $studentIdLabel ?></span>
        </div>
    </div>

    <form method="POST" action="profile.php">
        <div class="form-group">
            <label>Username (System Identifier)</label>
            <input type="text" class="input-luxury" value="<?= h($user['username']) ?>" disabled style="background:var(--bg-secondary);">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="input-luxury" required value="<?= h($user['full_name']) ?>">
            </div>
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" class="input-luxury" value="<?= $studentIdLabel ?>" disabled style="background:var(--bg-secondary);">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="nic_no">NIC No.</label>
                <input type="text" id="nic_no" name="nic_no" class="input-luxury" value="<?= h($user['nic_no'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="academic_year">Academic Year</label>
                <input type="text" id="academic_year" name="academic_year" class="input-luxury" value="<?= h($user['academic_year'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="input-luxury" value="<?= h($user['email']) ?>">
            </div>
            <div class="form-group">
                <label for="contact_no">Contact No.</label>
                <input type="tel" id="contact_no" name="contact_no" class="input-luxury" value="<?= h($user['contact_no']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="district">District</label>
                <input type="text" id="district" name="district" class="input-luxury" value="<?= h($user['district'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="campus">Campus</label>
                <input type="text" id="campus" name="campus" class="input-luxury" value="<?= h($user['campus'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="faculty">Faculty</label>
                <input type="text" id="faculty" name="faculty" class="input-luxury" value="<?= h($user['faculty'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="degree_program">Degree Program</label>
                <input type="text" id="degree_program" name="degree_program" class="input-luxury" value="<?= h($user['degree_program'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="emergency_contact">Emergency Contact</label>
                <input type="tel" id="emergency_contact" name="emergency_contact" class="input-luxury" value="<?= h($user['emergency_contact'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="input-luxury">
                    <option value="" <?= ($user['gender'] ?? '') === '' ? 'selected' : '' ?>>Select Gender</option>
                    <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="input-luxury" value="<?= h($user['date_of_birth'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="input-luxury" placeholder="Leave blank to keep current password">
            </div>
        </div>
        <div class="form-group">
            <label for="address">Permanent Address</label>
            <textarea id="address" name="address" class="input-luxury" rows="3"><?= h($user['address']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-luxury btn-accent">Save Profile Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>
