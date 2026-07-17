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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>View Schedule</title>
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
                <a href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a class="active" href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>View Schedule</h2>
                    <p>Review your teaching schedule for the semester.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Weekly Schedule</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-6">
                            <div class="data-grid-header">
                                <span>Course Code</span>
                                <span>Course Name</span>
                                <span>Section</span>
                                <span>Day</span>
                                <span>Time</span>
                                <span>Room</span>
                            </div>
                            <?php if (empty($teacherSchedule)): ?>
                            <div class="data-grid-row"><span>No schedule found.</span></div>
                            <?php else: foreach ($teacherSchedule as $row): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($row['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($row['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($row['section']); ?></span>
                                <span><?php echo htmlspecialchars($row['day']); ?></span>
                                <span><?php echo htmlspecialchars(substr($row['time_start'], 0, 5) . ' - ' . substr($row['time_end'], 0, 5)); ?></span>
                                <span><?php echo htmlspecialchars($row['room']); ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
