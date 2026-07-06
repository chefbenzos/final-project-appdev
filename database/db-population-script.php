<?php

/*==========================================================
 Populating database 
 2 registrars, 
 25 subjects (5 subjects across each of the 5 programs), 
 10 teachers (2 per program), 
 100 students (20 per program's section)
 125 schedule rows (5 subjects × 5 timeslots × 5 days × 5 programs)
 2,500 enrollments (each of the 125 schedules has 20 students from its program enrolled) 
 2,500 grade records (one grade row per enrollment)
 100 payment records 
 100 document records (one of each per student)
 20 hold records, holds are applied around 20% 
==========================================================*/
$first_name = ["Mark", "Vince", "Ken", "Yuan", "Chad", "Jako", "Andrew", "Jhon", "Miguel", "Calvin"];
$last_name  = ["Piosan", "Hanana", "Bacobod", "Crutz", "Sunicon", "Marinez", "Chanz", "Quejo", "Tagaltag", "Bacosta"];
$middle_initials = range('A', 'Z');

$courses = [
    "BSCS" => [
        "name" => "Bachelor of Science in Computer Science",
        "subjects" => [
            ["code" => "CC101", "name" => "Programming Fundamentals",       "units" => 3],
            ["code" => "CC102", "name" => "Object-Oriented Programming",     "units" => 3],
            ["code" => "CC103", "name" => "Data Structures and Algorithms",  "units" => 3],
            ["code" => "CC104", "name" => "Database Systems",                "units" => 3],
            ["code" => "CC105", "name" => "Web Development",                 "units" => 3],
        ]
    ],
    "BSIT" => [
        "name" => "Bachelor of Science in Information Technology",
        "subjects" => [
            ["code" => "IT101", "name" => "Computer Fundamentals",           "units" => 3],
            ["code" => "IT102", "name" => "Networking 1",                    "units" => 3],
            ["code" => "IT103", "name" => "Systems Analysis and Design",     "units" => 3],
            ["code" => "IT104", "name" => "Information Assurance",           "units" => 3],
            ["code" => "IT105", "name" => "Web Technologies",                "units" => 3],
        ]
    ],
     "BSCPE" => [
        "name" => "Bachelor of Science in Computer Engineering",
        "subjects" => [
            ["code" => "CPE101", "name" => "Circuits and Electronics",              "units" => 3],
            ["code" => "CPE102", "name" => "Logic Circuits and Design",             "units" => 3],
            ["code" => "CPE103", "name" => "Microprocessor Systems",                "units" => 3],
            ["code" => "CPE104", "name" => "Computer Architecture and Organization","units" => 3],
            ["code" => "CPE105", "name" => "Embedded Systems",                      "units" => 3],
        ]
    ],
    "BSMA" => [
        "name" => "Bachelor of Multimedia Arts",
        "subjects" => [
            ["code" => "MA101", "name" => "Introduction to Multimedia Arts",  "units" => 3],
            ["code" => "MA102", "name" => "Digital Illustration",             "units" => 3],
            ["code" => "MA103", "name" => "2D Animation",                     "units" => 3],
            ["code" => "MA104", "name" => "Video Editing and Production",     "units" => 3],
            ["code" => "MA105", "name" => "Interactive Media Design",         "units" => 3],
        ]
    ],
    "BSCE" => [
        "name" => "Bachelor of Science in Civil Engineering",
        "subjects" => [
            ["code" => "CE101", "name" => "Statics of Rigid Bodies",           "units" => 3],
            ["code" => "CE102", "name" => "Mechanics of Deformable Bodies",    "units" => 3],
            ["code" => "CE103", "name" => "Surveying",                        "units" => 3],
            ["code" => "CE104", "name" => "Construction Materials and Testing","units" => 3],
            ["code" => "CE105", "name" => "Structural Analysis",               "units" => 3],
        ]
    ]
];

$timeSlots = [
    ["07:30:00", "09:00:00"],
    ["09:00:00", "10:30:00"],
    ["10:30:00", "12:00:00"],
    ["13:00:00", "14:30:00"],
    ["14:30:00", "16:00:00"]
];

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

$rooms = ["LAB101", "LAB102", "ROOM201", "ROOM202", "ROOM203", "ROOM204", "ROOM205"];

$sections = [
    "BSCS" => "BSCS-1A",
    "BSIT" => "BSIT-1A",
    "BSCPE"  => "BSCPE-1A",
    "BSMA" => "BSMA-1A",
    "BSCE" => "BSCE-1A"
];

$scholarships = ["None", "Academic", "Athletic", "Entrance", "Dean's Lister"];

$passwords = ["secret", "class"];

require "db.php";

$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$tables = [
    "grades",
    "enrollments",
    "payments",
    "documents",
    "holds",
    "schedules",
    "subjects",
    "teachers",
    "registrars",
    "students"
];

foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

function randItem($arr) { return $arr[array_rand($arr)]; }

function randDateWithinLastMonths($months = 3) {
    $ts = strtotime("-" . rand(0, $months * 30) . " days");
    return date('Y-m-d', $ts);
}

function makeEmail($first, $last, $domain, $counter) {
    return strtolower($first) . "." . strtolower($last) . $counter . "@" . $domain;
}

$hasher = fn($plain) => password_hash($plain, PASSWORD_BCRYPT);

echo "Seeding started...\n";

// =========================================================
// 1. REGISTRARS (2)
// =========================================================
$registrarIds = [];
for ($i = 1; $i <= 2; $i++) {
    $fn = randItem($first_name);
    $ln = randItem($last_name);
    $email = makeEmail($fn, $ln, "feu-registrar.edu.ph", $i);
    $hash = $hasher(randItem($passwords));

    $stmt = $conn->prepare("INSERT INTO registrars (last_name, first_name, email, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $ln, $fn, $email, $hash);
    $stmt->execute();
    $registrarIds[] = $conn->insert_id;
    $stmt->close();
}
echo "Inserted " . count($registrarIds) . " registrars.\n";

// =========================================================
// 2. SUBJECTS (5 subjects per program = 25 total)
// =========================================================
$subjectIdsByProgram = [];
foreach ($courses as $programCode => $data) {
    $subjectIdsByProgram[$programCode] = [];
    foreach ($data["subjects"] as $subj) {
        $department = $data["name"];
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, units, department, program) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $subj["code"], $subj["name"], $subj["units"], $department, $programCode);
        $stmt->execute();
        $subjectId = $conn->insert_id;
        $stmt->close();

        $subjectIdsByProgram[$programCode][] = [
            "subject_id" => $subjectId,
            "code" => $subj["code"],
            "name" => $subj["name"],
        ];
    }
}
echo "Inserted subjects for " . count($courses) . " programs.\n";

// =========================================================
// 3. TEACHERS (2 per program = 10 total)
// =========================================================
$teachersByProgram = [];
$teacherAssignment  = [];

foreach ($courses as $programCode => $data) {
    $teachersByProgram[$programCode] = [];
    $department = $data["name"];

    for ($t = 1; $t <= 2; $t++) {
        $fn = randItem($first_name);
        $ln = randItem($last_name);
        $email = makeEmail($fn, $ln, "feu-faculty.edu.ph", $programCode . $t);
        $hash = $hasher(randItem($passwords));

        $stmt = $conn->prepare("INSERT INTO teachers (last_name, first_name, email, department, password_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $ln, $fn, $email, $department, $hash);
        $stmt->execute();
        $teachersByProgram[$programCode][] = $conn->insert_id;
        $stmt->close();
    }

    $subjects = $subjectIdsByProgram[$programCode];
    $teacherPair = $teachersByProgram[$programCode];
    shuffle($subjects);

    $teacherAssignment[$programCode] = [];
    $teacherAssignment[$programCode][$subjects[0]["subject_id"]] = $teacherPair[0];
    $teacherAssignment[$programCode][$subjects[1]["subject_id"]] = $teacherPair[1];
    for ($i = 2; $i < count($subjects); $i++) {
        $teacherAssignment[$programCode][$subjects[$i]["subject_id"]] = randItem($teacherPair);
    }
}
echo "Inserted 10 teachers (2 per program) and assigned subjects.\n";

// =========================================================
// 4. STUDENTS (20 per section = 100 total)
// =========================================================
$studentsByProgram = [];
$studentCounter = 1;

foreach ($courses as $programCode => $data) {
    $studentsByProgram[$programCode] = [];

    for ($s = 1; $s <= 20; $s++) {
        $fn = randItem($first_name);
        $ln = randItem($last_name);
        $mi = randItem($middle_initials);
        $studentNumber = date('Y') . str_pad($studentCounter, 5, '0', STR_PAD_LEFT);
        $personalEmail  = makeEmail($fn, $ln, "gmail.com", $studentCounter);
        $instituteEmail = makeEmail($fn, $ln, "students.feu.edu.ph", $studentCounter);
        $scholarship = randItem($scholarships);
        $hash = $hasher(randItem($passwords));

        $stmt = $conn->prepare("
            INSERT INTO students
                (student_number, last_name, first_name, middle_initial, personal_email,
                 institute_email, citizenship, curriculum_code, year_level,
                 registration_status, student_type, scholarship, password_hash)
            VALUES (?, ?, ?, ?, ?, ?, 'Filipino', ?, '1st Year', 'Enrolled', 'Regular', ?, ?)
        ");
        $stmt->bind_param(
            "sssssssss",
            $studentNumber, $ln, $fn, $mi, $personalEmail,
            $instituteEmail, $programCode, $scholarship, $hash
        );
        $stmt->execute();
        $studentId = $conn->insert_id;
        $stmt->close();

        $studentsByProgram[$programCode][] = $studentId;
        $studentCounter++;
    }
}
echo "Inserted " . ($studentCounter - 1) . " students.\n";

// =========================================================
// 5. SCHEDULES
// =========================================================
$scheduleRows = [];

foreach ($courses as $programCode => $data) {
    $subjects = $subjectIdsByProgram[$programCode];
    $section  = $sections[$programCode];

    foreach ($subjects as $idx => $subj) {
        $slot = $timeSlots[$idx % count($timeSlots)];
        $room = randItem($rooms);
        $teacherId = $teacherAssignment[$programCode][$subj["subject_id"]];

        foreach ($days as $day) {
            $schoolYear = (int) date('Y');
            $stmt = $conn->prepare("
                INSERT INTO schedules
                    (subject_id, teacher_id, section, day, time_start, time_end, room, semester, school_year)
                VALUES (?, ?, ?, ?, ?, ?, ?, '1st Semester', ?)
            ");
            $stmt->bind_param(
                "iisssssi",
                $subj["subject_id"], $teacherId, $section, $day,
                $slot[0], $slot[1], $room, $schoolYear
            );
            $stmt->execute();
            $scheduleId = $conn->insert_id;
            $stmt->close();

            $scheduleRows[] = [
                "schedule_id" => $scheduleId,
                "subject_id"  => $subj["subject_id"],
                "teacher_id"  => $teacherId,
                "program"     => $programCode,
            ];
        }
    }
}
echo "Inserted " . count($scheduleRows) . " schedule rows.\n";

// =========================================================
// 6. ENROLLMENTS + GRADES
// =========================================================
$enrollmentCount = 0;
$gradeCount = 0;

foreach ($scheduleRows as $row) {
    $programCode = $row["program"];
    $studentIds = $studentsByProgram[$programCode];

    foreach ($studentIds as $studentId) {
        $status = "active";
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, schedule_id, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $studentId, $row["schedule_id"], $status);
        $stmt->execute();
        $enrollmentId = $conn->insert_id;
        $stmt->close();
        $enrollmentCount++;

        // Grades: random midterm/finals, compute final grade + remarks
        $midterm = round(mt_rand(700, 990) / 10, 2); // 70.0 - 99.0
        $finals  = round(mt_rand(700, 990) / 10, 2);
        $finalGrade = round(($midterm + $finals) / 2, 2);
        $remarks = $finalGrade >= 75 ? "PASSED" : "FAILED";

        $stmt = $conn->prepare("
            INSERT INTO grades (enrollment_id, student_id, schedule_id, midterm, finals, final_grade, remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiiddds",
            $enrollmentId, $studentId, $row["schedule_id"], $midterm, $finals, $finalGrade, $remarks
        );
        $stmt->execute();
        $stmt->close();
        $gradeCount++;
    }
}
echo "Inserted $enrollmentCount enrollments and $gradeCount grade records.\n";

// =========================================================
// 7. PAYMENTS (one per student)
// =========================================================

$paymentCount = 0;
foreach ($studentsByProgram as $programCode => $studentIds) {
    foreach ($studentIds as $studentId) {
        $totalFees = round(mt_rand(25000, 45000), 2);
        $amountPaid = round($totalFees * (mt_rand(50, 100) / 100), 2);
        $balance = round($totalFees - $amountPaid, 2);
        $paymentType = randItem(["Cash", "Bank Transfer", "Credit Card", "Installment"]);
        $paymentDate = randDateWithinLastMonths(3);
        $schoolYear = (int) date('Y');

        $stmt = $conn->prepare("
            INSERT INTO payments
                (student_id, total_fees, amount_paid, balance, payment_type, payment_date, semester, school_year)
            VALUES (?, ?, ?, ?, ?, ?, '1st Semester', ?)
        ");
        $stmt->bind_param(
            "idddssi",
            $studentId, $totalFees, $amountPaid, $balance, $paymentType, $paymentDate, $schoolYear
        );
        $stmt->execute();
        $stmt->close();
        $paymentCount++;
    }
}
echo "Inserted $paymentCount payment records.\n";

// =========================================================
// 8. DOCUMENTS (one per student)
// =========================================================

$docTypes = ["Form 137", "Good Moral Certificate", "Birth Certificate", "TOR (Transcript of Records)", "NBI Clearance"];
$docStatuses = ["pending", "approved", "rejected"];
$documentCount = 0;

foreach ($studentsByProgram as $programCode => $studentIds) {
    foreach ($studentIds as $studentId) {
        $docType = randItem($docTypes);
        $status = randItem($docStatuses);
        $remarks = $status === "rejected" ? "Document unclear, please resubmit." : "Verified by registrar.";

        $stmt = $conn->prepare("INSERT INTO documents (student_id, doc_type, status, remarks) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $studentId, $docType, $status, $remarks);
        $stmt->execute();
        $stmt->close();
        $documentCount++;
    }
}
echo "Inserted $documentCount document records.\n";

// =========================================================
// 9. HOLDS (1 in 5)
// =========================================================

$holdTypes = ["Financial Hold", "Clearance Hold", "Disciplinary Hold", "Library Hold"];
$holdCount = 0;

foreach ($studentsByProgram as $programCode => $studentIds) {
    foreach ($studentIds as $studentId) {
        if (mt_rand(1, 5) === 1) { // ~20% of students get a hold
            $holdType = randItem($holdTypes);
            $description = "$holdType placed on record pending resolution.";
            $resolved = mt_rand(0, 1);

            $stmt = $conn->prepare("INSERT INTO holds (student_id, hold_type, description, resolved) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $studentId, $holdType, $description, $resolved);
            $stmt->execute();
            $stmt->close();
            $holdCount++;
        }
    }
}
echo "Inserted $holdCount hold records.\n";

echo "Seeding complete!\n";

?>