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
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/main-dashboard-teacher.css">
    <title>Teacher Dashboard</title>
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
            <div class="teacher-shell">
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
                        <div class="profile-grid">
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Faculty ID</td><td>TCH-<?php echo str_pad($teacherId, 4, '0', STR_PAD_LEFT); ?></td></tr>
                                        <tr><td>First Name</td><td><?php echo htmlspecialchars($_SESSION['first_name']); ?></td></tr>
                                        <tr><td>Last Name</td><td><?php echo htmlspecialchars($_SESSION['last_name']); ?></td></tr>
                                        <tr><td>Department</td><td><?php echo htmlspecialchars($_SESSION['department'] ?? '--'); ?></td></tr>
                                        <tr><td>Institute Email</td><td><?php echo htmlspecialchars($_SESSION['email']); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Employment Status</td><td>Active</td></tr>
                                        <tr><td>Courses Handled</td><td><?php echo $courseCount; ?></td></tr>
                                        <tr><td>Academic Year</td><td><?php echo htmlspecialchars($schoolYear); ?></td></tr>
                                        <tr><td>Semester</td><td><?php echo htmlspecialchars($semester); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
</body>
</html>
