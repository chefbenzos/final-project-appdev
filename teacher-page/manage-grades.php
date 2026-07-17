<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a teacher
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a teacher.");
        exit();
    }

    $teacherId = $_SESSION['user_id'];
    $teacherSchedule = viewTeacherSchedule($conn, $teacherId);

    // Dedupe the 5-day-a-week rows down to one dropdown entry per subject/section
    // (teacherSchedule has one row per weekday meeting, so without this a course
    // would show up 5 times in the dropdown with an identical label). The first
    // occurrence per subject/section is kept, which is always its Monday row.
    $seenCourses = [];
    $courseOptions = [];
    foreach ($teacherSchedule as $row) {
        $key = $row['subject_code'] . '|' . $row['section'];
        if (!isset($seenCourses[$key])) {
            $seenCourses[$key] = true;
            $courseOptions[$row['schedule_id']] = $row['subject_code'] . ' - ' . $row['subject_name'] . ' (' . $row['section'] . ')';
        }
    }

    $selectedScheduleId = (int) ($_GET['course_select'] ?? 0);
    if ($selectedScheduleId !== 0 && !array_key_exists($selectedScheduleId, $courseOptions)) {
        $selectedScheduleId = 0;
    }

    $saveMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'])) {
        $selectedScheduleId = (int) $_POST['schedule_id'];

        if (array_key_exists($selectedScheduleId, $courseOptions)) {
            $enrollmentIds = $_POST['enrollment_id'];
            $midterms = $_POST['midterm'];
            $finals = $_POST['finals'];

            foreach ($enrollmentIds as $i => $enrollmentId) {
                $midterm = is_numeric($midterms[$i]) ? (float) $midterms[$i] : 0;
                $final = is_numeric($finals[$i]) ? (float) $finals[$i] : 0;
                updateGrade($conn, (int) $enrollmentId, $midterm, $final);
            }
            $saveMessage = "Grades saved successfully.";
        } else {
            $saveMessage = "You are not authorized to update grades for that course.";
        }
    }

    $students = $selectedScheduleId ? getScheduleEnrolledStudents($conn, $selectedScheduleId) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Manage Grades</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>
            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_University_Crest_512.png" alt="BHP University crest">
                </div>
                <h1>Teacher Portal</h1>
                <p>Academic management dashboard</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashhboard-teacher.php">Teacher Profile</a>
                <a href="view-courses.php">View Courses</a>
                <a class="active" href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Grades</h2>
                    <p>Enter and update student grades per course.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Select Course</h3>
                    </div>
                    <div class="panel-body">
                        <form method="get" action="manage-grades.php">
                            <label for="grade-course-select">Course</label>
                            <select id="grade-course-select" name="course_select" onchange="this.form.submit()">
                                <option value="">-- Select a Course --</option>
                                <?php foreach ($courseOptions as $scheduleId => $label): ?>
                                <option value="<?php echo $scheduleId; ?>" <?php echo $scheduleId === $selectedScheduleId ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Student Grade List</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="manage-grades.php">
                            <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($selectedScheduleId ?? ''); ?>">
                            <div class="data-grid cols-6">
                                <div class="data-grid-header">
                                    <span>Student No.</span>
                                    <span>Last Name</span>
                                    <span>First Name</span>
                                    <span>Midterm Grade</span>
                                    <span>Final Grade</span>
                                    <span>Remarks</span>
                                </div>
                                <?php if (!$selectedScheduleId): ?>
                                <div class="data-grid-row"><span>Select a course above to view its class list.</span></div>
                                <?php elseif (empty($students)): ?>
                                <div class="data-grid-row"><span>No students are enrolled in this course.</span></div>
                                <?php else: foreach ($students as $s): ?>
                                <div class="data-grid-row">
                                    <span><?php echo htmlspecialchars($s['student_number']); ?></span>
                                    <span><?php echo htmlspecialchars($s['last_name']); ?></span>
                                    <span><?php echo htmlspecialchars($s['first_name']); ?></span>
                                    <span>
                                        <input type="hidden" name="enrollment_id[]" value="<?php echo $s['enrollment_id']; ?>">
                                        <input type="number" name="midterm[]" value="<?php echo htmlspecialchars($s['midterm']); ?>" placeholder="0.00" step="0.01">
                                    </span>
                                    <span>
                                        <input type="number" name="finals[]" value="<?php echo htmlspecialchars($s['finals']); ?>" placeholder="0.00" step="0.01">
                                    </span>
                                    <span><?php echo htmlspecialchars($s['remarks'] ?? 'INC'); ?></span>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                            <?php if ($selectedScheduleId && !empty($students)): ?>
                            <br>
                            <button type="submit">Save All Grades</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
