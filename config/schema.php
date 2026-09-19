<?php

function ensure_hostel_schema($conn) {
    $ensure_columns = [
        ['users', 'student_id', 'VARCHAR(20) UNIQUE'],
        ['users', 'academic_year', 'VARCHAR(20)'],
        ['users', 'degree_program', 'VARCHAR(100)']
    ];

    foreach ($ensure_columns as [$table, $column, $definition]) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check && mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    }

    $roomHostelKey = mysqli_query(
        $conn,
        "SELECT CONSTRAINT_NAME
         FROM information_schema.KEY_COLUMN_USAGE
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'rooms'
           AND COLUMN_NAME = 'hostel_id'
           AND REFERENCED_TABLE_NAME = 'hostels'"
    );
    if ($roomHostelKey && mysqli_num_rows($roomHostelKey) > 0) {
        while ($fk = mysqli_fetch_assoc($roomHostelKey)) {
            mysqli_query($conn, "ALTER TABLE `rooms` DROP FOREIGN KEY `" . $fk['CONSTRAINT_NAME'] . "`");
        }
    }

    $legacyHostelId = mysqli_query($conn, "SHOW COLUMNS FROM `rooms` LIKE 'hostel_id'");
    if ($legacyHostelId && mysqli_num_rows($legacyHostelId) > 0) {
        mysqli_query($conn, "ALTER TABLE `rooms` DROP COLUMN `hostel_id`");
    }

    $legacyCampusCheck = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'campus'");
    $degreeProgramCheck = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'degree_program'");
    if ($legacyCampusCheck && mysqli_num_rows($legacyCampusCheck) > 0) {
        if ($degreeProgramCheck && mysqli_num_rows($degreeProgramCheck) === 0) {
            mysqli_query($conn, "ALTER TABLE `users` CHANGE `campus` `degree_program` VARCHAR(100)");
        } else {
            mysqli_query($conn, "UPDATE `users` SET `degree_program` = COALESCE(`degree_program`, `campus`) WHERE `degree_program` IS NULL AND `campus` IS NOT NULL");
            mysqli_query($conn, "ALTER TABLE `users` DROP COLUMN `campus`");
        }
    }

    $regNoCheck = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'reg_no'");
    if ($regNoCheck && mysqli_num_rows($regNoCheck) > 0) {
        mysqli_query($conn, "ALTER TABLE `users` DROP COLUMN `reg_no`");
    }

    $obsolete_user_columns = ['emergency_contact', 'district', 'faculty', 'distance_km'];
    foreach ($obsolete_user_columns as $column) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE '$column'");
        if ($check && mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "ALTER TABLE `users` DROP COLUMN `$column`");
        }
    }

    $obsolete_application_columns = ['nic_no', 'address', 'academic_year'];
    foreach ($obsolete_application_columns as $column) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `applications` LIKE '$column'");
        if ($check && mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "ALTER TABLE `applications` DROP COLUMN `$column`");
        }
    }

    $roomCount = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM rooms"))[0];

    if ($roomCount === 0) {
        for ($i = 1; $i <= 10; $i++) {
            $roomNumber = 'F1/' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $roomStmt = mysqli_prepare($conn, "INSERT INTO rooms (floor, room_number, room_type, capacity, status) VALUES (1, ?, 'single', 1, 'active')");
            mysqli_stmt_bind_param($roomStmt, 's', $roomNumber);
            mysqli_stmt_execute($roomStmt);
            $roomId = mysqli_insert_id($conn);
            $bedNumber = $roomNumber . '/A' . $i;
            $bedStmt = mysqli_prepare($conn, "INSERT INTO beds (room_id, bed_number, status) VALUES (?, ?, 'vacant')");
            mysqli_stmt_bind_param($bedStmt, 'is', $roomId, $bedNumber);
            mysqli_stmt_execute($bedStmt);
        }

        for ($i = 1; $i <= 10; $i++) {
            $roomNumber = 'F2/' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $roomStmt = mysqli_prepare($conn, "INSERT INTO rooms (floor, room_number, room_type, capacity, status) VALUES (2, ?, 'shared', 2, 'active')");
            mysqli_stmt_bind_param($roomStmt, 's', $roomNumber);
            mysqli_stmt_execute($roomStmt);
            $roomId = mysqli_insert_id($conn);
            $bedOne = $roomNumber . '/A' . $i;
            $bedTwo = $roomNumber . '/B' . $i;

            $bedStmt1 = mysqli_prepare($conn, "INSERT INTO beds (room_id, bed_number, status) VALUES (?, ?, 'vacant')");
            mysqli_stmt_bind_param($bedStmt1, 'is', $roomId, $bedOne);
            mysqli_stmt_execute($bedStmt1);

            $bedStmt2 = mysqli_prepare($conn, "INSERT INTO beds (room_id, bed_number, status) VALUES (?, ?, 'vacant')");
            mysqli_stmt_bind_param($bedStmt2, 'is', $roomId, $bedTwo);
            mysqli_stmt_execute($bedStmt2);
        }
    }
}

function generate_student_id($conn) {
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(student_id, '/', -1) AS UNSIGNED)), 0) + 1 AS next_seq FROM users WHERE student_id LIKE 'ST/%'");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $nextSeq = (int) ($row['next_seq'] ?? 1);
    return 'ST/' . str_pad((string) $nextSeq, 2, '0', STR_PAD_LEFT);
}
