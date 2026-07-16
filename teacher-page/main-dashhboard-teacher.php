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

    // How many distinct subject/section combinations this teacher currently handles
    $coursesHandled = [];
    foreach ($teacherSchedule as $row) {
        $coursesHandled[$row['subject_code'] . '|' . $row['section']] = true;
    }
    $courseCount = count($coursesHandled);

    $semester = $teacherSchedule[0]['semester'] ?? '--';
    $schoolYear = $teacherSchedule[0]['school_year'] ?? '--';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <title>Teacher Profile</title>
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
                <a class="active" href="main-dashhboard-teacher.php">Teacher Profile</a>
                <a href="view-courses.php">View Courses</a>
                <a href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="profile-shell">
                <header class="topbar">
                    <div>
                        <h2>Teacher Profile</h2>
                        <p>Faculty information and academic service status.</p>
                    </div>
                </header>

                <section class="panel" id="teacher-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Prof. <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h3>
                        </div>
                        <span class="status">Status: Faculty</span>
                    </div>

                    <div class="panel-body">
                        <div class="info-grid">
                            <div class="info-row"><span class="info-label">Faculty ID</span><span>TCH-<?php echo str_pad($teacherId, 4, '0', STR_PAD_LEFT); ?></span></div>
                            <div class="info-row"><span class="info-label">First Name</span><span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Last Name</span><span><?php echo htmlspecialchars($_SESSION['last_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Department</span><span><?php echo htmlspecialchars($_SESSION['department'] ?? '--'); ?></span></div>
                            <div class="info-row"><span class="info-label">Institute Email</span><span><?php echo htmlspecialchars($_SESSION['email']); ?></span></div>
                            <div class="info-row"><span class="info-label">Employment Status</span><span>Active</span></div>
                            <div class="info-row"><span class="info-label">Courses Handled</span><span><?php echo $courseCount; ?></span></div>
                            <div class="info-row"><span class="info-label">Academic Year</span><span><?php echo htmlspecialchars($schoolYear); ?></span></div>
                            <div class="info-row"><span class="info-label">Semester</span><span><?php echo htmlspecialchars($semester); ?></span></div>
                        </div>
                    </div>
                </section>

            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
