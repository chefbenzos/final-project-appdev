<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a registrar
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'registrar') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a registrar.");
        exit();
    }

    $programs = getAllPrograms($conn);

    // Document slots the registrar can attach files to during enrollment.
    $documentSlots = ['Birth Certificate', 'Form 138', 'Good Moral Certificate', 'NBI Clearance'];

    $errors = [];
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $middleInitial = trim($_POST['middle_initial'] ?? '');
        $personalEmail = trim($_POST['personal_email'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $yearLevel = trim($_POST['year_level'] ?? '1st Year');
        $citizenship = trim($_POST['citizenship'] ?? 'Filipino');
        $studentType = trim($_POST['student_type'] ?? 'Regular');
        $scholarship = trim($_POST['scholarship'] ?? 'None');

        if ($firstName === '' || $lastName === '' || $personalEmail === '' || $program === '') {
            $errors[] = "First name, last name, personal email, and course are all required.";
        }

        if (empty($errors)) {
            // Auto-assigned fields — the registrar never types these in
            $studentNumber = generateStudentNumber($conn);
            $instituteEmail = generateInstituteEmail($firstName, $lastName, $studentNumber);
            $tempPassword = generateTempPassword();

            $result = addStudent($conn, [
                'student_number'      => $studentNumber,
                'last_name'           => $lastName,
                'first_name'          => $firstName,
                'middle_initial'      => $middleInitial,
                'personal_email'      => $personalEmail,
                'institute_email'     => $instituteEmail,
                'citizenship'         => $citizenship,
                'curriculum_code'     => $program,
                'year_level'          => $yearLevel,
                'registration_status' => 'Enrolled',
                'student_type'        => $studentType,
                'scholarship'         => $scholarship,
                'password'            => $tempPassword,
            ]);

            if ($result['success']) {
                $newStudentId = $result['student_id'];

                // Every newly enrolled student is assessed a standard 40,000 initial bill
                addPayment($conn, [
                    'student_id'   => $newStudentId,
                    'total_fees'   => 40000,
                    'amount_paid'  => 0,
                    'payment_type' => 'Initial Assessment',
                    'payment_date' => date('Y-m-d'),
                    'semester'     => '1st Semester',
                    'school_year'  => (int) date('Y'),
                ]);

                $uploadedDocs = [];

                // Handle any of the document slots that actually had a file attached
                foreach ($documentSlots as $index => $docType) {
                    $fieldName = "document_$index";
                    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = ROOT_PATH . '/uploads/documents/';
                        $originalName = basename($_FILES[$fieldName]['name']);
                        $safeName = $studentNumber . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
                        $destination = $uploadDir . $safeName;

                        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
                            addDocument($conn, [
                                'student_id' => $newStudentId,
                                'doc_type'   => $docType,
                                'status'     => 'submitted',
                                'remarks'    => 'Uploaded during enrollment.',
                                'file_path'  => 'uploads/documents/' . $safeName,
                            ]);
                            $uploadedDocs[] = $docType;
                        }
                    }
                }

                $success = [
                    'student_number'  => $studentNumber,
                    'institute_email' => $instituteEmail,
                    'temp_password'   => $tempPassword,
                    'uploaded_docs'   => $uploadedDocs,
                ];
            } else {
                $errors[] = "Error enrolling student: " . $result['error'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <title>Enroll Student</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
                <h1>Registrar Portal</h1>
                <p>Student records management</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-registrar.php">Registrar Profile</a>
                <a class="active" href="enroll-student.php">Enroll Student</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Enroll Student</h2>
                    <p>Register a new student. Student number, institute email, and password are assigned automatically.</p>
                </header>

                <?php if (!empty($errors)): ?>
                <div class="notice">
                    <?php foreach ($errors as $err): ?>
                        <?php echo htmlspecialchars($err); ?><br>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="notice">
                    <strong>Student enrolled successfully.</strong><br>
                    Student Number: <strong><?php echo htmlspecialchars($success['student_number']); ?></strong><br>
                    Institute Email: <strong><?php echo htmlspecialchars($success['institute_email']); ?></strong><br>
                    Temporary Password: <strong><?php echo htmlspecialchars($success['temp_password']); ?></strong><br>
                    Initial Bill: <strong>PHP 40,000.00</strong> assessed automatically<br>
                    <?php if (!empty($success['uploaded_docs'])): ?>
                    Documents Uploaded: <?php echo htmlspecialchars(implode(', ', $success['uploaded_docs'])); ?><br>
                    <?php endif; ?>
                    Please record the temporary password now — provide it to the student securely, as it will not be shown again.
                </div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Student Enrollment Form</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="enroll-student.php" enctype="multipart/form-data" class="enroll-form">
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" required>
                                </div>

                                <div class="form-field">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" required>
                                </div>

                                <div class="form-field">
                                    <label for="middle_initial">Middle Initial</label>
                                    <input type="text" id="middle_initial" name="middle_initial" maxlength="1">
                                </div>

                                <div class="form-field">
                                    <label for="personal_email">Personal Email</label>
                                    <input type="email" id="personal_email" name="personal_email" required>
                                </div>

                                <div class="form-field">
                                    <label for="program">Course</label>
                                    <select id="program" name="program" required>
                                        <option value="">-- Select a Course --</option>
                                        <?php foreach ($programs as $p): ?>
                                        <option value="<?php echo htmlspecialchars($p['program']); ?>">
                                            <?php echo htmlspecialchars($p['program'] . ' - ' . $p['department']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="year_level">Year Level</label>
                                    <select id="year_level" name="year_level">
                                        <option>1st Year</option>
                                        <option>2nd Year</option>
                                        <option>3rd Year</option>
                                        <option>4th Year</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="citizenship">Citizenship</label>
                                    <input type="text" id="citizenship" name="citizenship" value="Filipino">
                                </div>

                                <div class="form-field">
                                    <label for="student_type">Student Type</label>
                                    <select id="student_type" name="student_type">
                                        <option>Regular</option>
                                        <option>Irregular</option>
                                        <option>Transferee</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="scholarship">Scholarship</label>
                                    <select id="scholarship" name="scholarship">
                                        <option>None</option>
                                        <option>Academic</option>
                                        <option>Athletic</option>
                                        <option>Entrance</option>
                                        <option>Dean's Lister</option>
                                    </select>
                                </div>
                            </div>

                            <h4 class="form-section-title">Document Uploads (optional)</h4>
                            <div class="form-grid">
                                <?php foreach ($documentSlots as $index => $docType): ?>
                                <div class="form-field">
                                    <label for="document_<?php echo $index; ?>"><?php echo htmlspecialchars($docType); ?></label>
                                    <input type="file" id="document_<?php echo $index; ?>" name="document_<?php echo $index; ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit">Enroll Student</button>
                        </form>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
