<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as an admin.");
        exit();
    }

    $programs = getAllPrograms($conn);

    $teacherErrors = [];
    $teacherSuccess = null;
    $studentErrors = [];
    $studentSuccess = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'teacher') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') {
            $teacherErrors[] = "First name, last name, and email are all required.";
        }

        if (empty($teacherErrors)) {
            $tempPassword = generateTempPassword();

            $result = addTeacher($conn, [
                'last_name'  => $lastName,
                'first_name' => $firstName,
                'email'      => $email,
                'department' => $department,
                'password'   => $tempPassword,
            ]);

            if ($result['success']) {
                $teacherSuccess = [
                    'email'         => $email,
                    'temp_password' => $tempPassword,
                ];
            } else {
                $teacherErrors[] = "Error adding teacher: " . $result['error'];
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'student') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $middleInitial = trim($_POST['middle_initial'] ?? '');
        $personalEmail = trim($_POST['personal_email'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $yearLevel = trim($_POST['year_level'] ?? '1st Year');

        if ($firstName === '' || $lastName === '' || $personalEmail === '' || $program === '') {
            $studentErrors[] = "First name, last name, personal email, and course are all required.";
        }

        if (empty($studentErrors)) {
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
                'citizenship'         => 'Filipino',
                'curriculum_code'     => $program,
                'year_level'          => $yearLevel,
                'registration_status' => 'Enrolled',
                'student_type'        => 'Regular',
                'scholarship'         => 'None',
                'password'            => $tempPassword,
            ]);

            if ($result['success']) {
                $studentSuccess = [
                    'student_number'  => $studentNumber,
                    'institute_email' => $instituteEmail,
                    'temp_password'   => $tempPassword,
                ];
            } else {
                $studentErrors[] = "Error adding student: " . $result['error'];
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
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Add Teacher / Student</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <h1>Admin Portal</h1>
                <p>System administration dashboard</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-admin.php">Admin Profile</a>
                <a class="active" href="add-users.php">Add Teacher / Student</a>
                <a href="manage-courses.php">Manage Courses</a>
                <a href="assign-courses.php">Assign Courses to Teacher</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Add Teacher / Student</h2>
                    <p>Create new teacher and student accounts. Temporary passwords are generated automatically.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Add Teacher</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($teacherErrors)): ?>
                        <div class="notice">
                            <?php foreach ($teacherErrors as $err): ?>
                                <?php echo htmlspecialchars($err); ?><br>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($teacherSuccess): ?>
                        <div class="notice">
                            <strong>Teacher added successfully.</strong><br>
                            Email: <strong><?php echo htmlspecialchars($teacherSuccess['email']); ?></strong><br>
                            Temporary Password: <strong><?php echo htmlspecialchars($teacherSuccess['temp_password']); ?></strong><br>
                            Please record the temporary password now — it will not be shown again.
                        </div>
                        <?php endif; ?>

                        <form method="post" action="add-users.php">
                            <input type="hidden" name="form_type" value="teacher">
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="teacher_first_name">First Name</label>
                                    <input type="text" id="teacher_first_name" name="first_name" required>
                                </div>

                                <div class="form-field">
                                    <label for="teacher_last_name">Last Name</label>
                                    <input type="text" id="teacher_last_name" name="last_name" required>
                                </div>

                                <div class="form-field">
                                    <label for="teacher_email">Email</label>
                                    <input type="email" id="teacher_email" name="email" required>
                                </div>

                                <div class="form-field">
                                    <label for="teacher_department">Department</label>
                                    <input type="text" id="teacher_department" name="department" placeholder="e.g. College of Computer Studies">
                                </div>
                            </div>

                            <button type="submit">Add Teacher</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Add Student</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($studentErrors)): ?>
                        <div class="notice">
                            <?php foreach ($studentErrors as $err): ?>
                                <?php echo htmlspecialchars($err); ?><br>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($studentSuccess): ?>
                        <div class="notice">
                            <strong>Student added successfully.</strong><br>
                            Student Number: <strong><?php echo htmlspecialchars($studentSuccess['student_number']); ?></strong><br>
                            Institute Email: <strong><?php echo htmlspecialchars($studentSuccess['institute_email']); ?></strong><br>
                            Temporary Password: <strong><?php echo htmlspecialchars($studentSuccess['temp_password']); ?></strong><br>
                            Please record the temporary password now — it will not be shown again.
                        </div>
                        <?php endif; ?>

                        <form method="post" action="add-users.php">
                            <input type="hidden" name="form_type" value="student">
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="student_first_name">First Name</label>
                                    <input type="text" id="student_first_name" name="first_name" required>
                                </div>

                                <div class="form-field">
                                    <label for="student_last_name">Last Name</label>
                                    <input type="text" id="student_last_name" name="last_name" required>
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
                            </div>

                            <button type="submit">Add Student</button>
                        </form>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
