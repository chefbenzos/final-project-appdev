<?php
/*
loginUser(conn, table, emailColumn, identifier, password) => authenticate a user and return their profile
addStudent(conn, data) => insert a new student record
addTeacher(conn, data) => insert a new teacher record
addRegistrar(conn, data) => insert a new registrar record
addSubject(conn, data) => insert a new subject/course record
addSchedule(conn, data) => insert a new class schedule slot
addPayment(conn, data) => insert a new payment/billing record, auto-computes balance
addDocument(conn, data) => insert a new student document submission
addHold(conn, data) => place a new hold on a student record
searchStudents(conn, keyword) => search students by number, name, or email
getStudentById(conn, studentId) => fetch a single student's full profile
getAllStudents(conn) => list all students
getAllTeachers(conn) => list all teachers
getAllRegistrars(conn) => list all registrars
updateStudent(conn, studentId, data) => update one or more fields on a student
changePassword(conn, table, idColumn, id, newPassword) => reset a login password for any role
viewBills(conn, studentId) => list a student's full payment history
getStudentBalance(conn, studentId) => sum a student's outstanding balance
viewGrades(conn, studentId, semester, schoolYear) => list a student's grades, optionally filtered
updateGrade(conn, enrollmentId, midterm, finals, passingGrade) => update grades, recomputes final grade + remarks
viewStudentSchedule(conn, studentId) => a student's full weekly class schedule
viewTeacherSchedule(conn, teacherId) => a teacher's full weekly teaching load
viewSectionSchedule(conn, section) => a whole section's weekly timetable
getCourseOfferings(conn, program) => all available class schedules for every subject in a program
getProgramCurriculum(conn, program) => the list of subjects that make up a program's curriculum
getEnrollmentCountForSchedule(conn, scheduleId) => how many active students are in a class schedule
getScheduleEnrolledStudents(conn, scheduleId) => full class list + grades for one schedule
enrollStudent(conn, studentId, scheduleId) => enroll a student into a class schedule
dropEnrollment(conn, enrollmentId) => mark an enrollment as dropped
isRoomAvailable(conn, room, day, timeStart, timeEnd, excludeScheduleId) => check for room booking conflicts
updateDocumentStatus(conn, documentId, status, remarks) => approve/reject a submitted document
viewDocuments(conn, studentId) => list a student's submitted documents
resolveHold(conn, holdId) => mark a hold as resolved
viewHolds(conn, studentId, unresolvedOnly) => list a student's holds
hasUnresolvedHolds(conn, studentId) => check if a student has any active holds
getStudentDashboardSummary(conn, studentId) => bundles profile, schedule, grades, balance, holds, documents
markAttendance(conn, data) => record or update one student's attendance for a class meeting date
getAttendanceForSchedule(conn, scheduleId, date) => attendance already recorded for a schedule/date
getAllStudentBalances(conn) => every student's total assessment, paid, and remaining balance
getAllPayments(conn) => a flat list of every payment transaction across all students
getPendingDocumentRequests(conn) => all documents across all students awaiting registrar action
getAllDocuments(conn) => every document across every student
getStudentIdByNumber(conn, studentNumber) => look up a student_id from a student_number
*/

// =========================================================
// PASSWORD HELPERS
// =========================================================
function hashPassword($plainPassword) {
    return password_hash($plainPassword, PASSWORD_BCRYPT);
}

function verifyPassword($plainPassword, $hash) {
    return password_verify($plainPassword, $hash);
}

// =========================================================
// LOGIN
// =========================================================

function loginUser($conn, $table, $emailColumn, $identifier, $password) {
    $allowedTables = ['students', 'teachers', 'registrars'];
    $allowedColumns = ['institute_email', 'email'];

    if (!in_array($table, $allowedTables) || !in_array($emailColumn, $allowedColumns)) {
        return false;
    }

    $query = "SELECT * FROM $table WHERE $emailColumn = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $identifier);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    if ($user && verifyPassword($password, $user['password_hash'])) {
        unset($user['password_hash']);
        return $user;
    }

    return false;
}

// =========================================================
// ADD RECORDS
// =========================================================

function addStudent($conn, $data) {
    $required = ['student_number', 'last_name', 'first_name', 'personal_email', 'institute_email', 'curriculum_code', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $hash = hashPassword($data['password']);
    $middleInitial      = $data['middle_initial']      ?? '';
    $citizenship        = $data['citizenship']         ?? 'Filipino';
    $yearLevel          = $data['year_level']          ?? '1st Year';
    $registrationStatus = $data['registration_status'] ?? 'Enrolled';
    $studentType        = $data['student_type']        ?? 'Regular';
    $scholarship        = $data['scholarship']         ?? 'None';

    $stmt = $conn->prepare("
        INSERT INTO students
            (student_number, last_name, first_name, middle_initial, personal_email,
             institute_email, citizenship, curriculum_code, year_level,
             registration_status, student_type, scholarship, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssssssss",
        $data['student_number'], $data['last_name'], $data['first_name'], $middleInitial,
        $data['personal_email'], $data['institute_email'], $citizenship, $data['curriculum_code'],
        $yearLevel, $registrationStatus, $studentType, $scholarship, $hash
    );

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'student_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}
function addTeacher($conn, $data) {
    $required = ['last_name', 'first_name', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $hash = hashPassword($data['password']);
    $department = $data['department'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO teachers (last_name, first_name, email, department, password_hash)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $data['last_name'], $data['first_name'], $data['email'], $department, $hash);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'teacher_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}
function addRegistrar($conn, $data) {
    $required = ['last_name', 'first_name', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $hash = hashPassword($data['password']);

    $stmt = $conn->prepare("
        INSERT INTO registrars (last_name, first_name, email, password_hash)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $data['last_name'], $data['first_name'], $data['email'], $hash);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'registrar_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

// =========================================================
// SEARCH / LOOKUP
// =========================================================

function searchStudents($conn, $keyword) {
    $like = "%$keyword%";
    $stmt = $conn->prepare("
        SELECT student_id, student_number, last_name, first_name, middle_initial,
               institute_email, curriculum_code, year_level, registration_status
        FROM students
        WHERE student_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR institute_email LIKE ?
        ORDER BY last_name, first_name
    ");
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function getStudentById($conn, $studentId) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    if ($student) unset($student['password_hash']);
    return $student;
}

function getAllStudents($conn) {
    $result = $conn->query("SELECT student_id, student_number, last_name, first_name, curriculum_code, year_level, registration_status FROM students ORDER BY last_name, first_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllTeachers($conn) {
    $result = $conn->query("SELECT teacher_id, last_name, first_name, email, department FROM teachers ORDER BY last_name, first_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllRegistrars($conn) {
    $result = $conn->query("SELECT registrar_id, last_name, first_name, email FROM registrars ORDER BY last_name, first_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// =========================================================
// UPDATE / DELETE
// =========================================================

function updateStudent($conn, $studentId, $data) {
    $allowedFields = ['last_name', 'first_name', 'middle_initial', 'personal_email', 'institute_email',
                       'citizenship', 'curriculum_code', 'year_level', 'registration_status', 'student_type', 'scholarship'];

    $setParts = [];
    $values = [];
    $types = '';

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $setParts[] = "$field = ?";
            $values[] = $data[$field];
            $types .= 's';
        }
    }

    if (empty($setParts)) {
        return ['success' => false, 'error' => 'No valid fields to update'];
    }

    $values[] = $studentId;
    $types .= 'i';

    $sql = "UPDATE students SET " . implode(', ', $setParts) . " WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();

    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

function changePassword($conn, $table, $idColumn, $id, $newPassword) {
    $allowedTables = ['students' => 'student_id', 'teachers' => 'teacher_id', 'registrars' => 'registrar_id'];
    if (!isset($allowedTables[$table]) || $allowedTables[$table] !== $idColumn) {
        return ['success' => false, 'error' => 'Invalid table or id column'];
    }

    $hash = hashPassword($newPassword);
    $sql = "UPDATE `$table` SET password_hash = ? WHERE `$idColumn` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hash, $id);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();

    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

// =========================================================
// PAYMENTS / BILLING
// =========================================================

function addPayment($conn, $data) {
    $required = ['student_id', 'total_fees', 'amount_paid', 'payment_type', 'payment_date', 'semester', 'school_year'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $balance = $data['total_fees'] - $data['amount_paid'];

    $stmt = $conn->prepare("
        INSERT INTO payments (student_id, total_fees, amount_paid, balance, payment_type, payment_date, semester, school_year)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "idddssi",
        $data['student_id'], $data['total_fees'], $data['amount_paid'], $balance,
        $data['payment_type'], $data['payment_date'], $data['semester'], $data['school_year']
    );

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'payment_id' => $id, 'balance' => $balance];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function viewBills($conn, $studentId) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE student_id = ? ORDER BY payment_date DESC");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function getStudentBalance($conn, $studentId) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(balance), 0) AS total_balance FROM payments WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total_balance'];
}

// =========================================================
// GRADES
// =========================================================

function viewGrades($conn, $studentId, $semester = null, $schoolYear = null) {
    $sql = "
        SELECT g.grade_id, s.subject_code, s.subject_name, s.units,
               g.midterm, g.finals, g.final_grade, g.remarks,
               sch.semester, sch.school_year, sch.section
        FROM grades g
        JOIN schedules sch ON g.schedule_id = sch.schedule_id
        JOIN subjects s ON sch.subject_id = s.subject_id
        WHERE g.student_id = ?
    ";
    $types = "i";
    $params = [$studentId];

    if ($semester !== null) {
        $sql .= " AND sch.semester = ?";
        $types .= "s";
        $params[] = $semester;
    }
    if ($schoolYear !== null) {
        $sql .= " AND sch.school_year = ?";
        $types .= "i";
        $params[] = $schoolYear;
    }
    $sql .= " ORDER BY sch.school_year DESC, sch.semester, s.subject_code";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function updateGrade($conn, $enrollmentId, $midterm, $finals, $passingGrade = 75) {
    $finalGrade = round(($midterm + $finals) / 2, 2);
    $remarks = $finalGrade >= $passingGrade ? "PASSED" : "FAILED";

    $stmt = $conn->prepare("
        UPDATE grades SET midterm = ?, finals = ?, final_grade = ?, remarks = ?
        WHERE enrollment_id = ?
    ");
    $stmt->bind_param("dddsi", $midterm, $finals, $finalGrade, $remarks, $enrollmentId);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();

    return $success
        ? ['success' => true, 'final_grade' => $finalGrade, 'remarks' => $remarks]
        : ['success' => false, 'error' => $error];
}

// =========================================================
// SCHEDULES
// =========================================================

function viewStudentSchedule($conn, $studentId) {
    $stmt = $conn->prepare("
        SELECT sch.schedule_id, s.subject_code, s.subject_name, s.units,
               t.first_name AS teacher_first_name, t.last_name AS teacher_last_name,
               sch.section, sch.day, sch.time_start, sch.time_end, sch.room,
               sch.semester, sch.school_year, e.status AS enrollment_status
        FROM enrollments e
        JOIN schedules sch ON e.schedule_id = sch.schedule_id
        JOIN subjects s ON sch.subject_id = s.subject_id
        JOIN teachers t ON sch.teacher_id = t.teacher_id
        WHERE e.student_id = ?
        ORDER BY FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday'), sch.time_start
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function viewTeacherSchedule($conn, $teacherId) {
    $stmt = $conn->prepare("
        SELECT sch.schedule_id, s.subject_code, s.subject_name, s.units,
               sch.section, sch.day, sch.time_start, sch.time_end, sch.room,
               sch.semester, sch.school_year
        FROM schedules sch
        JOIN subjects s ON sch.subject_id = s.subject_id
        WHERE sch.teacher_id = ?
        ORDER BY FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday'), sch.time_start
    ");
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function viewSectionSchedule($conn, $section) {
    $stmt = $conn->prepare("
        SELECT sch.schedule_id, s.subject_code, s.subject_name,
               t.first_name AS teacher_first_name, t.last_name AS teacher_last_name,
               sch.day, sch.time_start, sch.time_end, sch.room, sch.semester, sch.school_year
        FROM schedules sch
        JOIN subjects s ON sch.subject_id = s.subject_id
        JOIN teachers t ON sch.teacher_id = t.teacher_id
        WHERE sch.section = ?
        ORDER BY FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday'), sch.time_start
    ");
    $stmt->bind_param("s", $section);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// All available class schedules for every subject under a given program (e.g. "BSCS"),
// regardless of which specific section a student is in. Used for "Course Offerings".
function getCourseOfferings($conn, $program) {
    $stmt = $conn->prepare("
        SELECT sch.schedule_id, s.subject_code, s.subject_name, s.units,
               t.first_name AS teacher_first_name, t.last_name AS teacher_last_name,
               sch.section, sch.day, sch.time_start, sch.time_end, sch.room,
               sch.semester, sch.school_year
        FROM schedules sch
        JOIN subjects s ON sch.subject_id = s.subject_id
        JOIN teachers t ON sch.teacher_id = t.teacher_id
        WHERE s.program = ?
        ORDER BY s.subject_code, FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday'), sch.time_start
    ");
    $stmt->bind_param("s", $program);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// The list of subjects that make up a program's curriculum (e.g. "BSCS").
function getProgramCurriculum($conn, $program) {
    $stmt = $conn->prepare("
        SELECT subject_code, subject_name, units
        FROM subjects
        WHERE program = ?
        ORDER BY subject_code
    ");
    $stmt->bind_param("s", $program);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// =========================================================
// TEACHER-SIDE CLASS LIST HELPERS
// =========================================================

// How many active students are enrolled in a given class schedule.
function getEnrollmentCountForSchedule($conn, $scheduleId) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM enrollments WHERE schedule_id = ? AND status = 'active'");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['cnt'];
}

// Full class list (with grade + enrollment info) for one specific schedule.
// Used by the teacher's Manage Grades and Manage Attendance pages.
function getScheduleEnrolledStudents($conn, $scheduleId) {
    $stmt = $conn->prepare("
        SELECT e.enrollment_id, e.student_id, e.status AS enrollment_status,
               st.student_number, st.last_name, st.first_name,
               g.grade_id, g.midterm, g.finals, g.final_grade, g.remarks
        FROM enrollments e
        JOIN students st ON e.student_id = st.student_id
        LEFT JOIN grades g ON g.enrollment_id = e.enrollment_id
        WHERE e.schedule_id = ? AND e.status = 'active'
        ORDER BY st.last_name, st.first_name
    ");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// =========================================================
// ENROLLMENT
// =========================================================

function enrollStudent($conn, $studentId, $scheduleId) {
    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, schedule_id, status) VALUES (?, ?, 'active')");
    $stmt->bind_param("ii", $studentId, $scheduleId);

    if ($stmt->execute()) {
        $enrollmentId = $conn->insert_id;
        $stmt->close();

        $stmt2 = $conn->prepare("
            INSERT INTO grades (enrollment_id, student_id, schedule_id, midterm, finals, final_grade, remarks)
            VALUES (?, ?, ?, 0.00, 0.00, 0.00, 'INC')
        ");
        $stmt2->bind_param("iii", $enrollmentId, $studentId, $scheduleId);
        $stmt2->execute();
        $stmt2->close();

        return ['success' => true, 'enrollment_id' => $enrollmentId];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function dropEnrollment($conn, $enrollmentId) {
    $stmt = $conn->prepare("UPDATE enrollments SET status = 'dropped' WHERE enrollment_id = ?");
    $stmt->bind_param("i", $enrollmentId);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

// =========================================================
// SUBJECTS / SCHEDULES (ADMIN / REGISTRAR SIDE)
// =========================================================

function addSubject($conn, $data) {
    $required = ['subject_code', 'subject_name', 'units', 'program'];
    foreach ($required as $field) {
        if (empty($data[$field]) && $data[$field] !== 0) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }
    $department = $data['department'] ?? '';

    $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, units, department, program) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $data['subject_code'], $data['subject_name'], $data['units'], $department, $data['program']);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'subject_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function addSchedule($conn, $data) {
    $required = ['subject_id', 'teacher_id', 'section', 'day', 'time_start', 'time_end', 'semester', 'school_year'];
    foreach ($required as $field) {
        if (empty($data[$field]) && $data[$field] !== 0) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }
    $room = $data['room'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO schedules (subject_id, teacher_id, section, day, time_start, time_end, room, semester, school_year)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iisssssi",
        $data['subject_id'], $data['teacher_id'], $data['section'], $data['day'],
        $data['time_start'], $data['time_end'], $room, $data['semester'], $data['school_year']
    );

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'schedule_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function isRoomAvailable($conn, $room, $day, $timeStart, $timeEnd, $excludeScheduleId = null) {
    $sql = "
        SELECT COUNT(*) AS conflict_count FROM schedules
        WHERE room = ? AND day = ?
          AND time_start < ? AND time_end > ?
    ";
    $types = "ssss";
    $params = [$room, $day, $timeEnd, $timeStart];

    if ($excludeScheduleId !== null) {
        $sql .= " AND schedule_id != ?";
        $types .= "i";
        $params[] = $excludeScheduleId;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['conflict_count'] == 0;
}

// =========================================================
// DOCUMENTS
// =========================================================

function addDocument($conn, $data) {
    if (empty($data['student_id']) || empty($data['doc_type'])) {
        return ['success' => false, 'error' => 'Missing required field: student_id or doc_type'];
    }
    $status = $data['status'] ?? 'pending';
    $remarks = $data['remarks'] ?? 'Awaiting review.';

    $stmt = $conn->prepare("INSERT INTO documents (student_id, doc_type, status, remarks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $data['student_id'], $data['doc_type'], $status, $remarks);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'document_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function updateDocumentStatus($conn, $documentId, $status, $remarks) {
    $stmt = $conn->prepare("UPDATE documents SET status = ?, remarks = ? WHERE document_id = ?");
    $stmt->bind_param("ssi", $status, $remarks, $documentId);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

function viewDocuments($conn, $studentId) {
    $stmt = $conn->prepare("SELECT * FROM documents WHERE student_id = ? ORDER BY submitted_at DESC");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// =========================================================
// HOLDS
// =========================================================

function addHold($conn, $data) {
    $required = ['student_id', 'hold_type', 'description'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $stmt = $conn->prepare("INSERT INTO holds (student_id, hold_type, description, resolved) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("iss", $data['student_id'], $data['hold_type'], $data['description']);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        $stmt->close();
        return ['success' => true, 'hold_id' => $id];
    }
    $error = $stmt->error;
    $stmt->close();
    return ['success' => false, 'error' => $error];
}

function resolveHold($conn, $holdId) {
    $stmt = $conn->prepare("UPDATE holds SET resolved = 1 WHERE hold_id = ?");
    $stmt->bind_param("i", $holdId);
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

function viewHolds($conn, $studentId, $unresolvedOnly = false) {
    $sql = "SELECT * FROM holds WHERE student_id = ?";
    if ($unresolvedOnly) {
        $sql .= " AND resolved = 0";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function hasUnresolvedHolds($conn, $studentId) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM holds WHERE student_id = ? AND resolved = 0");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['cnt'] > 0;
}

// =========================================================
// DASHBOARD SUMMARY
// =========================================================

function getStudentDashboardSummary($conn, $studentId) {
    return [
        'profile'   => getStudentById($conn, $studentId),
        'schedule'  => viewStudentSchedule($conn, $studentId),
        'grades'    => viewGrades($conn, $studentId),
        'balance'   => getStudentBalance($conn, $studentId),
        'holds'     => viewHolds($conn, $studentId, true),
        'documents' => viewDocuments($conn, $studentId),
    ];
}

// =========================================================
// ATTENDANCE (teacher-side)
// =========================================================

// Records or updates one student's attendance for a specific class meeting date.
// $data keys: enrollment_id, student_id, schedule_id, attendance_date, status
function markAttendance($conn, $data) {
    $required = ['enrollment_id', 'student_id', 'schedule_id', 'attendance_date', 'status'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'error' => "Missing required field: $field"];
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO attendance (enrollment_id, student_id, schedule_id, attendance_date, status)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->bind_param(
        "iiiss",
        $data['enrollment_id'], $data['student_id'], $data['schedule_id'],
        $data['attendance_date'], $data['status']
    );
    $success = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();

    return $success ? ['success' => true] : ['success' => false, 'error' => $error];
}

// The attendance already recorded for a schedule on a specific date, keyed by enrollment_id.
function getAttendanceForSchedule($conn, $scheduleId, $date) {
    $stmt = $conn->prepare("SELECT enrollment_id, status FROM attendance WHERE schedule_id = ? AND attendance_date = ?");
    $stmt->bind_param("is", $scheduleId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $byEnrollment = [];
    foreach ($rows as $row) {
        $byEnrollment[$row['enrollment_id']] = $row['status'];
    }
    return $byEnrollment;
}

// =========================================================
// REGISTRAR-SIDE AGGREGATE VIEWS
// =========================================================

// Every student's name plus their total assessment, amount paid, and remaining balance,
// summed across all of their payment records. Used on Manage Balance.
function getAllStudentBalances($conn) {
    $sql = "
        SELECT st.student_id, st.student_number, st.last_name, st.first_name,
               COALESCE(SUM(p.total_fees), 0)  AS total_assessment,
               COALESCE(SUM(p.amount_paid), 0) AS total_paid,
               COALESCE(SUM(p.balance), 0)     AS remaining_balance
        FROM students st
        LEFT JOIN payments p ON p.student_id = st.student_id
        GROUP BY st.student_id, st.student_number, st.last_name, st.first_name
        ORDER BY st.last_name, st.first_name
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// A flat list of every payment transaction across all students, most recent first.
// Used on Manage Transactions.
function getAllPayments($conn) {
    $sql = "
        SELECT p.payment_id, p.payment_date, st.student_number,
               st.last_name, st.first_name, p.payment_type, p.amount_paid
        FROM payments p
        JOIN students st ON p.student_id = st.student_id
        ORDER BY p.payment_date DESC, p.payment_id DESC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// All documents across every student that are still awaiting registrar action.
// Used on the registrar dashboard and Manage Requirements.
function getPendingDocumentRequests($conn) {
    $stmt = $conn->prepare("
        SELECT d.document_id, st.student_number, d.doc_type, d.status, d.submitted_at, d.remarks
        FROM documents d
        JOIN students st ON d.student_id = st.student_id
        WHERE d.status = 'pending'
        ORDER BY d.submitted_at ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// Every document across every student, for the full Manage Requirements table.
function getAllDocuments($conn) {
    $sql = "
        SELECT d.document_id, st.student_id, st.student_number, st.last_name, st.first_name,
               d.doc_type, d.status, d.submitted_at, d.remarks
        FROM documents d
        JOIN students st ON d.student_id = st.student_id
        ORDER BY d.submitted_at DESC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Look up a student_id from a student_number (registrar forms take student numbers, not raw IDs).
function getStudentIdByNumber($conn, $studentNumber) {
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_number = ? LIMIT 1");
    $stmt->bind_param("s", $studentNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['student_id'] : null;
}
?>