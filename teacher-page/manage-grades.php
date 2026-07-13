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

    // One dropdown entry per subject/section combination this teacher handles
    $courseOptions = [];
    foreach ($teacherSchedule as $row) {
        $courseOptions[$row['schedule_id']] = $row['subject_code'] . ' - ' . $row['subject_name'] . ' (' . $row['section'] . ')';
    }

    $selectedScheduleId = isset($_GET['course_select']) ? (int) $_GET['course_select'] : null;
    $saveMessage = '';

    // Handle grade save submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'])) {
        $selectedScheduleId = (int) $_POST['schedule_id'];

        // Only allow saving grades for a course this teacher actually teaches (basic authorization check)
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
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/manage-grades.css">
    <title>Manage Grades</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
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
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student No.</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Midterm Grade</th>
                                        <th>Final Grade</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$selectedScheduleId): ?>
                                    <tr><td colspan="6">Select a course above to view its class list.</td></tr>
                                    <?php elseif (empty($students)): ?>
                                    <tr><td colspan="6">No students are enrolled in this course.</td></tr>
                                    <?php else: foreach ($students as $s): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['student_number']); ?></td>
                                        <td><?php echo htmlspecialchars($s['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($s['first_name']); ?></td>
                                        <td>
                                            <input type="hidden" name="enrollment_id[]" value="<?php echo $s['enrollment_id']; ?>">
                                            <input type="number" name="midterm[]" value="<?php echo htmlspecialchars($s['midterm']); ?>" placeholder="0.00" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" name="finals[]" value="<?php echo htmlspecialchars($s['finals']); ?>" placeholder="0.00" step="0.01">
                                        </td>
                                        <td><?php echo htmlspecialchars($s['remarks'] ?? 'INC'); ?></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                            <?php if ($selectedScheduleId && !empty($students)): ?>
                            <br>
                            <button type="submit">Save All Grades</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
