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

    $courseOptions = [];
    foreach ($teacherSchedule as $row) {
        $courseOptions[$row['schedule_id']] = $row['subject_code'] . ' - ' . $row['subject_name'] . ' (' . $row['section'] . ')';
    }

    $selectedScheduleId = isset($_GET['att_course']) ? (int) $_GET['att_course'] : null;
    $selectedDate = $_GET['att_date'] ?? date('Y-m-d');
    $saveMessage = '';

    // Handle attendance save submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'])) {
        $selectedScheduleId = (int) $_POST['schedule_id'];
        $selectedDate = $_POST['attendance_date'];

        if (array_key_exists($selectedScheduleId, $courseOptions)) {
            $enrollmentIds = $_POST['enrollment_id'];
            $studentIds = $_POST['student_id'];
            $statuses = $_POST['status'];

            foreach ($enrollmentIds as $i => $enrollmentId) {
                markAttendance($conn, [
                    'enrollment_id'   => (int) $enrollmentId,
                    'student_id'      => (int) $studentIds[$i],
                    'schedule_id'     => $selectedScheduleId,
                    'attendance_date' => $selectedDate,
                    'status'          => $statuses[$i],
                ]);
            }
            $saveMessage = "Attendance saved for " . htmlspecialchars($selectedDate) . ".";
        } else {
            $saveMessage = "You are not authorized to record attendance for that course.";
        }
    }

    $students = $selectedScheduleId ? getScheduleEnrolledStudents($conn, $selectedScheduleId) : [];
    $existingAttendance = $selectedScheduleId ? getAttendanceForSchedule($conn, $selectedScheduleId, $selectedDate) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/manage-attendance.css">
    <title>Manage Attendance</title>
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
                <a href="manage-grades.php">Manage Grades</a>
                <a class="active" href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Attendance</h2>
                    <p>Record and track student attendance per class meeting.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Select Course and Date</h3>
                    </div>
                    <div class="panel-body">
                        <form method="get" action="manage-attendance.php">
                            <label for="att-course-select">Course</label>
                            <select id="att-course-select" name="att_course">
                                <option value="">-- Select a Course --</option>
                                <?php foreach ($courseOptions as $scheduleId => $label): ?>
                                <option value="<?php echo $scheduleId; ?>" <?php echo $scheduleId === $selectedScheduleId ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                            <br><br>

                            <label for="att-date">Meeting Date</label>
                            <input type="date" id="att-date" name="att_date" value="<?php echo htmlspecialchars($selectedDate); ?>">

                            <br><br>
                            <button type="submit">Load Class List</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Attendance Sheet</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="manage-attendance.php">
                            <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($selectedScheduleId ?? ''); ?>">
                            <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student No.</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$selectedScheduleId): ?>
                                    <tr><td colspan="4">Select a course and date above to load the class list.</td></tr>
                                    <?php elseif (empty($students)): ?>
                                    <tr><td colspan="4">No students are enrolled in this course.</td></tr>
                                    <?php else: foreach ($students as $s):
                                        $currentStatus = $existingAttendance[$s['enrollment_id']] ?? 'present';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['student_number']); ?></td>
                                        <td><?php echo htmlspecialchars($s['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($s['first_name']); ?></td>
                                        <td>
                                            <input type="hidden" name="enrollment_id[]" value="<?php echo $s['enrollment_id']; ?>">
                                            <input type="hidden" name="student_id[]" value="<?php echo $s['student_id']; ?>">
                                            <select name="status[]">
                                                <option value="present" <?php echo $currentStatus === 'present' ? 'selected' : ''; ?>>Present</option>
                                                <option value="absent" <?php echo $currentStatus === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                                <option value="late" <?php echo $currentStatus === 'late' ? 'selected' : ''; ?>>Late</option>
                                                <option value="excused" <?php echo $currentStatus === 'excused' ? 'selected' : ''; ?>>Excused</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                            <?php if ($selectedScheduleId && !empty($students)): ?>
                            <br>
                            <button type="submit">Save Attendance</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
