-- ============================================================
-- eHostel - Online Hostel Management System
-- Database: ehostel
-- Import this file in phpMyAdmin (XAMPP/MySQL) to create the database
-- ============================================================

CREATE DATABASE IF NOT EXISTS ehostel CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ehostel;

-- ------------------------------------------------------------
-- Table: hostels (Hostel building premises)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hostels (
    hostel_id   INT AUTO_INCREMENT PRIMARY KEY,
    hostel_name VARCHAR(100) NOT NULL,
    address     VARCHAR(255),
    floors      INT DEFAULT 1,
    total_rooms INT DEFAULT 0,
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: users  (both students and admins, role-based)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id           INT AUTO_INCREMENT PRIMARY KEY,
    username          VARCHAR(50)  NOT NULL UNIQUE,
    password          VARCHAR(255) NOT NULL,
    role              ENUM('admin','student') NOT NULL DEFAULT 'student',
    full_name         VARCHAR(100) NOT NULL,
    email             VARCHAR(100),
    contact_no        VARCHAR(20),
    nic_no            VARCHAR(20) UNIQUE,
    emergency_contact VARCHAR(20),
    address           VARCHAR(255),
    district          VARCHAR(50),
    reg_no            VARCHAR(30) UNIQUE,
    student_id        VARCHAR(20) UNIQUE,
    academic_year    VARCHAR(20),
    age               INT,
    gender            ENUM('Male','Female','Other'),
    date_of_birth     DATE,
    distance_km       DECIMAL(6,1),
    campus            VARCHAR(100),
    faculty           VARCHAR(100),
    degree_program    VARCHAR(150),
    photo             VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: rooms
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rooms (
    room_id     INT AUTO_INCREMENT PRIMARY KEY,
    hostel_id   INT DEFAULT 1,
    floor       INT DEFAULT 1,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    room_type   ENUM('single','shared') NOT NULL DEFAULT 'shared',
    capacity    INT NOT NULL DEFAULT 2,
    status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    FOREIGN KEY (hostel_id) REFERENCES hostels(hostel_id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- Table: beds (each room has one or more beds)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS beds (
    bed_id      INT AUTO_INCREMENT PRIMARY KEY,
    room_id     INT NOT NULL,
    bed_number  VARCHAR(10) NOT NULL,
    status      ENUM('vacant','occupied') NOT NULL DEFAULT 'vacant',
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Table: applications (student applies for hostel accommodation)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
    application_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id              INT NOT NULL,
    preferred_room_type  ENUM('single','shared') NOT NULL DEFAULT 'shared',
    nic_no               VARCHAR(20),
    address              TEXT,
    academic_year       VARCHAR(20),
    applied_date         DATE NOT NULL,
    status               ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    remarks              VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Table: allocations (admin allocates a bed after approval)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS allocations (
    allocation_id   INT AUTO_INCREMENT PRIMARY KEY,
    application_id  INT NOT NULL,
    user_id         INT NOT NULL,
    bed_id          INT NOT NULL,
    allocation_date DATE NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (bed_id) REFERENCES beds(bed_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Table: announcements
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(150) NOT NULL,
    content         TEXT NOT NULL,
    posted_by       INT,
    posted_date     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ============================================================
-- Default data
-- ============================================================

INSERT INTO hostels (hostel_name, address, floors, total_rooms) VALUES
('eHostel Main Campus Residence', 'Colombo 03 Campus Grounds', 3, 36);

-- Default admin account -> username: admin   password: admin123
-- Default ordinary user -> username: uoc  password: uoc
INSERT INTO users (username, password, role, full_name, email) VALUES
('admin', '$2b$12$caL6zW1boM7VGJVxoYPPqOMiaymGG6DAOp4HHBBt0kc9gQ51xuWAi', 'admin', 'System Administrator', 'admin@ehostel.lk'),
('uoc',   '$2b$12$BtMQjOjMOaCDprVgD/Fi8.2CnaLID8UDzl1SQaLqaQmt5Oi6aPGrq', 'student', 'Default User', 'uoc@ucsc.cmb.ac.lk');

-- Sample rooms for the fixed floor layout
INSERT INTO rooms (hostel_id, floor, room_number, room_type, capacity, status) VALUES
(1, 1, 'F1/01', 'single', 1, 'active'),
(1, 1, 'F1/02', 'single', 1, 'active'),
(1, 1, 'F1/03', 'single', 1, 'active'),
(1, 1, 'F1/04', 'single', 1, 'active'),
(1, 1, 'F1/05', 'single', 1, 'active'),
(1, 1, 'F1/06', 'single', 1, 'active'),
(1, 1, 'F1/07', 'single', 1, 'active'),
(1, 1, 'F1/08', 'single', 1, 'active'),
(1, 1, 'F1/09', 'single', 1, 'active'),
(1, 1, 'F1/10', 'single', 1, 'active'),
(1, 2, 'F2/01', 'shared', 2, 'active'),
(1, 2, 'F2/02', 'shared', 2, 'active'),
(1, 2, 'F2/03', 'shared', 2, 'active'),
(1, 2, 'F2/04', 'shared', 2, 'active'),
(1, 2, 'F2/05', 'shared', 2, 'active'),
(1, 2, 'F2/06', 'shared', 2, 'active'),
(1, 2, 'F2/07', 'shared', 2, 'active'),
(1, 2, 'F2/08', 'shared', 2, 'active'),
(1, 2, 'F2/09', 'shared', 2, 'active'),
(1, 2, 'F2/10', 'shared', 2, 'active');

-- Beds for each room
INSERT INTO beds (room_id, bed_number, status) VALUES
(1, 'F1/01/A1', 'vacant'),
(2, 'F1/02/A2', 'vacant'),
(3, 'F1/03/A3', 'vacant'),
(4, 'F1/04/A4', 'vacant'),
(5, 'F1/05/A5', 'vacant'),
(6, 'F1/06/A6', 'vacant'),
(7, 'F1/07/A7', 'vacant'),
(8, 'F1/08/A8', 'vacant'),
(9, 'F1/09/A9', 'vacant'),
(10, 'F1/10/A10', 'vacant'),
(11, 'F2/01/A1', 'vacant'), (11, 'F2/01/B1', 'vacant'),
(12, 'F2/02/A2', 'vacant'), (12, 'F2/02/B2', 'vacant'),
(13, 'F2/03/A3', 'vacant'), (13, 'F2/03/B3', 'vacant'),
(14, 'F2/04/A4', 'vacant'), (14, 'F2/04/B4', 'vacant'),
(15, 'F2/05/A5', 'vacant'), (15, 'F2/05/B5', 'vacant'),
(16, 'F2/06/A6', 'vacant'), (16, 'F2/06/B6', 'vacant'),
(17, 'F2/07/A7', 'vacant'), (17, 'F2/07/B7', 'vacant'),
(18, 'F2/08/A8', 'vacant'), (18, 'F2/08/B8', 'vacant'),
(19, 'F2/09/A9', 'vacant'), (19, 'F2/09/B9', 'vacant'),
(20, 'F2/10/A10', 'vacant'), (20, 'F2/10/B10', 'vacant');

-- Sample announcement
INSERT INTO announcements (title, content, posted_by) VALUES
('Welcome to eHostel', 'The online hostel management portal is now live. Students can apply for accommodation through the "Apply" page.', 1);
