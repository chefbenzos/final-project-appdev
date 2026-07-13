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

    // Collapse the 5-day-a-week rows down to one row per subject/section,
    // since a subject/section combination repeats once per weekday.
    $courses = [];
    foreach ($teacherSchedule as $row) {
        $key = $row['schedule_id'] === null ? null : $row['subject_code'] . '|' . $row['section'];
        if (!isset($courses[$key])) {
            $courses[$key] = [
                'subject_code' => $row['subject_code'],
                'subject_name' => $row['subject_name'],
                'units'        => $row['units'],
                'section'      => $row['section'],
                'schedule_id'  => $row['schedule_id'],
                'enrolled'     => 0,
            ];
        }
    }
    foreach ($courses as $key => &$course) {
        $course['enrolled'] = getEnrollmentCountForSchedule($conn, $course['schedule_id']);
    }
    unset($course);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/view-courses.css">
    <title>View Courses</title>
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
                <a class="active" href="view-courses.php">View Courses</a>
                <a href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>View Courses</h2>
                    <p>Review your assigned courses for the semester.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Available Courses</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Units</th>
                                    <th>Section</th>
                                    <th>Enrolled Students</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($courses)): ?>
                                <tr>
                                    <td colspan="6">No courses currently assigned.</td>
                                </tr>
                                <?php else: foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($course['units']); ?></td>
                                    <td><?php echo htmlspecialchars($course['section']); ?></td>
                                    <td><?php echo (int) $course['enrolled']; ?></td>
                                    <td>Active</td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>
</body>
</html>
