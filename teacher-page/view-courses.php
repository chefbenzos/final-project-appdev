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

    // Collapse the 5-day-a-week rows down to one row per subject/section
    $courses = [];
    foreach ($teacherSchedule as $row) {
        $key = $row['subject_code'] . '|' . $row['section'];
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
    foreach ($courses as &$course) {
        $course['enrolled'] = getEnrollmentCountForSchedule($conn, $course['schedule_id']);
    }
    unset($course);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
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
                        <div class="data-grid cols-6">
                            <div class="data-grid-header">
                                <span>Course Code</span>
                                <span>Course Name</span>
                                <span>Units</span>
                                <span>Section</span>
                                <span>Enrolled Students</span>
                                <span>Status</span>
                            </div>
                            <?php if (empty($courses)): ?>
                            <div class="data-grid-row">
                                <span>No courses currently assigned.</span>
                            </div>
                            <?php else: foreach ($courses as $course): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($course['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($course['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($course['units']); ?></span>
                                <span><?php echo htmlspecialchars($course['section']); ?></span>
                                <span><?php echo (int) $course['enrolled']; ?></span>
                                <span>Active</span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
</body>
</html>
