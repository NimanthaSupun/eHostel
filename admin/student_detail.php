<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$userId = (int) ($_GET['id'] ?? 0);
$error = '';
$success = '';

function load_student_detail(mysqli $conn, int $userId): ?array {
    $stmt = mysqli_prepare($conn, "SELECT u.*, a.application_id, a.preferred_room_type, a.nic_no AS app_nic, a.address AS app_address, a.academic_year AS app_year, a.status AS app_status, a.applied_date
                                   FROM users u
                                   LEFT JOIN applications a ON a.user_id = u.user_id
                                       AND a.application_id = (SELECT MAX(application_id) FROM applications WHERE user_id = u.user_id)
                                   WHERE u.user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    return $student ?: null;
}

function field_value(array $student, string $key, string $fallback = ''): string {
    if (isset($_POST[$key])) {
        return (string) $_POST[$key];
    }
    return (string) ($student[$key] ?? $fallback);
}

if ($userId <= 0) {
    header('Location: manage_students.php');
    exit;
}

$student = load_student_detail($conn, $userId);

if (!$student) {
    header('Location: manage_students.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contactNo = trim($_POST['contact_no'] ?? '');
    $emergencyContact = trim($_POST['emergency_contact'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $nicNo = trim($_POST['nic_no'] ?? '');
    $academicYear = trim($_POST['academic_year'] ?? '');
    $campus = trim($_POST['campus'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $degreeProgram = trim($_POST['degree_program'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $distanceKm = trim($_POST['distance_km'] ?? '');

    if ($fullName === '' || $username === '') {
        $error = 'Full name and username are required.';
    } elseif ($nicNo !== '' && !preg_match('/^(\d{9}[VvXx]|\d{12})$/', $nicNo)) {
        $error = 'Please enter a valid NIC number.';
    } elseif ($distanceKm !== '' && !is_numeric($distanceKm)) {
        $error = 'Distance from campus must be a number.';
    } elseif ($dateOfBirth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
        $error = 'Date of birth must be a valid date.';
    } else {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE user_id <> ? AND (username = ? OR (nic_no IS NOT NULL AND nic_no <> '' AND nic_no = ?))");
        mysqli_stmt_bind_param($check, 'iss', $userId, $username, $nicNo);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That username or NIC number is already in use.';
        } else {
            mysqli_begin_transaction($conn);
            try {
                $updateStudent = mysqli_prepare($conn, "UPDATE users SET full_name = ?, username = ?, email = ?, contact_no = ?, emergency_contact = ?, district = ?, nic_no = ?, academic_year = ?, campus = ?, faculty = ?, address = ?, degree_program = ?, gender = ?, date_of_birth = ?, distance_km = ? WHERE user_id = ? AND role = 'student'");
                $distanceValue = $distanceKm === '' ? null : (float) $distanceKm;
                $dateValue = $dateOfBirth === '' ? null : $dateOfBirth;
                mysqli_stmt_bind_param(
                    $updateStudent,
                    'ssssssssssssssdi',
                    $fullName,
                    $username,
                    $email,
                    $contactNo,
                    $emergencyContact,
                    $district,
                    $nicNo,
                    $academicYear,
                    $campus,
                    $faculty,
                    $address,
                    $degreeProgram,
                    $gender,
                    $dateValue,
                    $distanceValue,
                    $userId
                );

                if (!mysqli_stmt_execute($updateStudent)) {
                    throw new Exception('Could not update the student profile.');
                }

                if (!empty($student['application_id'])) {
                    $updateApplication = mysqli_prepare($conn, "UPDATE applications SET nic_no = ?, address = ?, academic_year = ? WHERE application_id = ?");
                    mysqli_stmt_bind_param($updateApplication, 'sssi', $nicNo, $address, $academicYear, $student['application_id']);
                    if (!mysqli_stmt_execute($updateApplication)) {
                        throw new Exception('Could not update the latest application snapshot.');
                    }
                }

                mysqli_commit($conn);
                $success = 'Student information updated successfully.';
                $student = load_student_detail($conn, $userId);
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = $e->getMessage();
            }
        }
    }
}

// Latest allocation (if any)
$allocation = null;
$allocStmt = mysqli_prepare($conn, "SELECT al.allocation_date, b.bed_number, r.room_number, r.room_type FROM allocations al JOIN beds b ON al.bed_id = b.bed_id JOIN rooms r ON b.room_id = r.room_id WHERE al.user_id = ? ORDER BY al.allocation_id DESC LIMIT 1");
mysqli_stmt_bind_param($allocStmt, 'i', $userId);
mysqli_stmt_execute($allocStmt);
$allocation = mysqli_stmt_get_result($allocStmt)->fetch_assoc();

$badgeClass = $student['role'] === 'student' ? 'badge badge-success' : 'badge badge-warning';

$base = '../';
$active = 'students';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Details — eHostel</title>
<link rel="stylesheet" href="../css/style.css?v=20260729">
</head>
<body>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="page-header">
    <div>
        <span class="section-label">STUDENT RECORD</span>
        <h1 class="serif-heading" style="font-size:2.4rem;"><?= h($student['full_name']) ?></h1>
        <p>Detailed view of the student account and application information.</p>
    </div>
    <div>
        <a href="manage_students.php" class="btn btn-luxury btn-outline">&larr; Back to Students</a>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="POST" action="student_detail.php?id=<?= $userId ?>" class="card" style="max-width:900px;">
    <input type="hidden" name="update_student" value="1">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h3 class="serif-heading" style="font-size:1.5rem;margin:0;">Student Profile</h3>
            <p style="margin:0.25rem 0 0;color:var(--text-muted);">Student ID: <?= h($student['student_id'] ?: 'Pending') ?></p>
        </div>
        <span class="<?= h($badgeClass) ?>"><?= h(strtoupper($student['role'])) ?></span>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="input-luxury" value="<?= h(field_value($student, 'full_name', $student['full_name'])) ?>" required>
        </div>
        <div class="form-group">
            <label>Student ID</label>
            <input type="text" class="input-luxury" value="<?= h($student['student_id'] ?: 'Pending') ?>" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="input-luxury" value="<?= h(field_value($student, 'username', $student['username'])) ?>" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <input type="text" class="input-luxury" value="<?= h(strtoupper($student['role'])) ?>" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="input-luxury" value="<?= h(field_value($student, 'email', $student['email'])) ?>">
        </div>
        <div class="form-group">
            <label for="contact_no">Contact No.</label>
            <input type="text" id="contact_no" name="contact_no" class="input-luxury" value="<?= h(field_value($student, 'contact_no', $student['contact_no'])) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="emergency_contact">Emergency Contact</label>
            <input type="text" id="emergency_contact" name="emergency_contact" class="input-luxury" value="<?= h(field_value($student, 'emergency_contact', $student['emergency_contact'])) ?>">
        </div>
        <div class="form-group">
            <label for="district">District</label>
            <input type="text" id="district" name="district" class="input-luxury" value="<?= h(field_value($student, 'district', $student['district'])) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="nic_no">NIC No.</label>
            <input type="text" id="nic_no" name="nic_no" class="input-luxury" value="<?= h(field_value($student, 'nic_no', $student['nic_no'])) ?>">
        </div>
        <div class="form-group">
            <label for="academic_year">Academic Year</label>
            <input type="text" id="academic_year" name="academic_year" class="input-luxury" value="<?= h(field_value($student, 'academic_year', $student['academic_year'])) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="campus">Campus</label>
            <input type="text" id="campus" name="campus" class="input-luxury" value="<?= h(field_value($student, 'campus', $student['campus'])) ?>">
        </div>
        <div class="form-group">
            <label for="faculty">Faculty</label>
            <input type="text" id="faculty" name="faculty" class="input-luxury" value="<?= h(field_value($student, 'faculty', $student['faculty'])) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="degree_program">Degree Program</label>
            <input type="text" id="degree_program" name="degree_program" class="input-luxury" value="<?= h(field_value($student, 'degree_program', $student['degree_program'])) ?>">
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="input-luxury">
                <?php $genderValue = field_value($student, 'gender', (string) ($student['gender'] ?? '')); ?>
                <option value="">Select Gender</option>
                <option value="Male" <?= $genderValue === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $genderValue === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $genderValue === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" class="input-luxury" value="<?= h(field_value($student, 'date_of_birth', (string) ($student['date_of_birth'] ?? ''))) ?>">
        </div>
        <div class="form-group">
            <label for="distance_km">Distance from Campus (km)</label>
            <input type="number" step="0.1" min="0" id="distance_km" name="distance_km" class="input-luxury" value="<?= h(field_value($student, 'distance_km', isset($student['distance_km']) ? (string) $student['distance_km'] : '')) ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="input-luxury" rows="3"><?= h(field_value($student, 'address', $student['app_address'] ?: (string) ($student['address'] ?? ''))) ?></textarea>
        </div>
        <div class="form-group">
            <label>Allocated Bed</label>
            <input type="text" class="input-luxury" value="<?= $allocation ? h($allocation['bed_number']) : 'Pending' ?>" disabled>
        </div>
    </div>

    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;margin-top:1.25rem;">
        <button type="submit" class="btn btn-luxury btn-accent">Save Changes</button>
        <a href="manage_students.php" class="btn btn-luxury btn-outline">Cancel</a>
    </div>
</form>

<div class="card" style="max-width:900px;margin-top:1.5rem;">
    <div class="form-row">
        <div class="form-group">
            <label>Application Status</label>
            <input type="text" class="input-luxury" value="<?= h(ucfirst($student['app_status'] ?: 'No application')) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Preferred Room Type</label>
            <input type="text" class="input-luxury" value="<?= h($student['preferred_room_type'] ? ucfirst($student['preferred_room_type']) : '—') ?>" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Allocation Date</label>
            <input type="text" class="input-luxury" value="<?= $allocation ? h(date('d M Y', strtotime($allocation['allocation_date']))) : '—' ?>" disabled>
        </div>
        <div class="form-group">
            <label>Latest Application Date</label>
            <input type="text" class="input-luxury" value="<?= $student['applied_date'] ? h(date('d M Y', strtotime($student['applied_date']))) : '—' ?>" disabled>
        </div>
    </div>

    <div class="form-group">
        <label>Latest Application Address</label>
        <textarea class="input-luxury" rows="3" disabled><?= h($student['app_address'] ?: '—') ?></textarea>
    </div>
</div>

<?php include __DIR__ . '/../includes/sidebar_close.php'; ?>
</body>
</html>



    