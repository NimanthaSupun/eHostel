<?php

function ensure_hostel_schema($conn) {
    $ensure_columns = [
        ['users', 'student_id', 'VARCHAR(20) UNIQUE'],
        ['users', 'academic_year', 'VARCHAR(20)'],
        ['users', 'campus', 'VARCHAR(100)']
    ];

    foreach ($ensure_columns as [$table, $column, $definition]) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check && mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    }

    $obsolete_user_columns = ['emergency_contact', 'district', 'faculty', 'degree_program', 'distance_km'];
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

    $hostelCount = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hostels"))[0];
    if ($hostelCount === 0) {
        mysqli_query($conn, "INSERT INTO hostels (hostel_name, address, floors, total_rooms, status) VALUES ('eHostel Main Campus Residence', 'Colombo 03 Campus Grounds', 2, 20, 'active')");
    }

    $hostelId = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT hostel_id FROM hostels ORDER BY hostel_id LIMIT 1"))[0];
    $roomCount = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM rooms"))[0];

    if ($roomCount === 0) {
        for ($i = 1; $i <= 10; $i++) {
            $roomNumber = 'F1/' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $roomStmt = mysqli_prepare($conn, "INSERT INTO rooms (hostel_id, floor, room_number, room_type, capacity, status) VALUES (?, 1, ?, 'single', 1, 'active')");
            mysqli_stmt_bind_param($roomStmt, 'is', $hostelId, $roomNumber);
            mysqli_stmt_execute($roomStmt);
            $roomId = mysqli_insert_id($conn);
            $bedNumber = $roomNumber . '/A' . $i;
            $bedStmt = mysqli_prepare($conn, "INSERT INTO beds (room_id, bed_number, status) VALUES (?, ?, 'vacant')");
            mysqli_stmt_bind_param($bedStmt, 'is', $roomId, $bedNumber);
            mysqli_stmt_execute($bedStmt);
        }

        for ($i = 1; $i <= 10; $i++) {
            $roomNumber = 'F2/' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $roomStmt = mysqli_prepare($conn, "INSERT INTO rooms (hostel_id, floor, room_number, room_type, capacity, status) VALUES (?, 2, ?, 'shared', 2, 'active')");
            mysqli_stmt_bind_param($roomStmt, 'is', $hostelId, $roomNumber);
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
