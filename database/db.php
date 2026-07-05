<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'feu_portal');
define('DB_PORT', 3306);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db(DB_NAME);
$conn->set_charset('utf8mb4');
//student table
$conn->query("
    CREATE TABLE IF NOT EXISTS students (
        student_id          INT AUTO_INCREMENT PRIMARY KEY,
        student_number      VARCHAR(20)  NOT NULL UNIQUE,
        last_name           VARCHAR(100) NOT NULL,
        first_name          VARCHAR(100) NOT NULL,
        middle_initial      CHAR(1)      DEFAULT '',
        personal_email      VARCHAR(150) NOT NULL,
        institute_email     VARCHAR(150) NOT NULL UNIQUE,
        citizenship         VARCHAR(100) DEFAULT 'Filipino',
        curriculum_code     VARCHAR(50)  NOT NULL,
        year_level          VARCHAR(20)  DEFAULT '1st Year',
        registration_status VARCHAR(50)  DEFAULT 'Enrolled',
        student_type        VARCHAR(50)  DEFAULT 'Regular',
        scholarship         VARCHAR(100) DEFAULT 'None',
        password_hash       VARCHAR(255) NOT NULL,
        created_at          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    )
");

//registrar table
$conn->query("
    CREATE TABLE IF NOT EXISTS registrars (
        registrar_id    INT AUTO_INCREMENT PRIMARY KEY,
        last_name       VARCHAR(100) NOT NULL,
        first_name      VARCHAR(100) NOT NULL,
        email           VARCHAR(150) NOT NULL UNIQUE,
        password_hash   VARCHAR(255) NOT NULL,
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

//teachers table
$conn->query("
    CREATE TABLE IF NOT EXISTS teachers (
        teacher_id      INT AUTO_INCREMENT PRIMARY KEY,
        last_name       VARCHAR(100) NOT NULL,
        first_name      VARCHAR(100) NOT NULL,
        email           VARCHAR(150) NOT NULL UNIQUE,
        department      VARCHAR(100) DEFAULT '',
        password_hash   VARCHAR(255) NOT NULL,
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

//subjects
$conn->query("
    CREATE TABLE IF NOT EXISTS subjects (
        subject_id      INT AUTO_INCREMENT PRIMARY KEY,
        subject_code    VARCHAR(20)  NOT NULL UNIQUE,
        subject_name    VARCHAR(150) NOT NULL,
        units           INT          NOT NULL DEFAULT 3,
        department      VARCHAR(100) DEFAULT '',
        program         VARCHAR(50)  NOT NULL
    )
");

//sched
$conn->query("
    CREATE TABLE IF NOT EXISTS schedules (
        schedule_id     INT AUTO_INCREMENT PRIMARY KEY,
        subject_id      INT         NOT NULL,
        teacher_id      INT         NOT NULL,
        section         VARCHAR(20) NOT NULL,
        day             VARCHAR(20) NOT NULL,
        time_start      TIME        NOT NULL,
        time_end        TIME        NOT NULL,
        room            VARCHAR(50) DEFAULT '',
        semester        VARCHAR(20) NOT NULL,
        school_year     INT         NOT NULL,
        FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)  ON DELETE CASCADE,
        FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)  ON DELETE CASCADE
    )
");

//enrollment
$conn->query("
    CREATE TABLE IF NOT EXISTS enrollments (
        enrollment_id   INT AUTO_INCREMENT PRIMARY KEY,
        student_id      INT         NOT NULL,
        schedule_id     INT         NOT NULL,
        status          VARCHAR(20) DEFAULT 'active',
        enrolled_at     TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_enrollment (student_id, schedule_id),
        FOREIGN KEY (student_id)  REFERENCES students(student_id)   ON DELETE CASCADE,
        FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id) ON DELETE CASCADE
    )
");

//grades
$conn->query("
    CREATE TABLE IF NOT EXISTS grades (
        grade_id        INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_id   INT           NOT NULL UNIQUE,
        student_id      INT           NOT NULL,
        schedule_id     INT           NOT NULL,
        midterm         DECIMAL(5,2)  DEFAULT 0.00,
        finals          DECIMAL(5,2)  DEFAULT 0.00,
        final_grade     DECIMAL(5,2)  DEFAULT 0.00,
        remarks         VARCHAR(10)   DEFAULT 'INC',
        FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
        FOREIGN KEY (student_id)    REFERENCES students(student_id)        ON DELETE CASCADE,
        FOREIGN KEY (schedule_id)   REFERENCES schedules(schedule_id)      ON DELETE CASCADE
    )
");

//payment
$conn->query("
    CREATE TABLE IF NOT EXISTS payments (
        payment_id      INT AUTO_INCREMENT PRIMARY KEY,
        student_id      INT            NOT NULL,
        total_fees      DECIMAL(10,2)  DEFAULT 0.00,
        amount_paid     DECIMAL(10,2)  DEFAULT 0.00,
        balance         DECIMAL(10,2)  DEFAULT 0.00,
        payment_type    VARCHAR(50)    DEFAULT 'Cash',
        payment_date    DATE           NOT NULL,
        semester        VARCHAR(20)    NOT NULL,
        school_year     INT            NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    )
");

//docu
$conn->query("
    CREATE TABLE IF NOT EXISTS documents (
        document_id     INT AUTO_INCREMENT PRIMARY KEY,
        student_id      INT          NOT NULL,
        doc_type        VARCHAR(100) NOT NULL,
        status          VARCHAR(20)  DEFAULT 'pending',
        submitted_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        remarks         TEXT         DEFAULT NULL,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    )
");

//holds
$conn->query("
    CREATE TABLE IF NOT EXISTS holds (
        hold_id         INT AUTO_INCREMENT PRIMARY KEY,
        student_id      INT          NOT NULL,
        hold_type       VARCHAR(100) NOT NULL,
        description     TEXT         NOT NULL,
        resolved        TINYINT(1)   DEFAULT 0,
        created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    )
");

?>