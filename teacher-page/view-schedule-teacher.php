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
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/view-schedule-teacher.css">
    <title>View Schedule</title>
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
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($teacherSchedule)): ?>
                                <tr><td colspan="6">No schedule found.</td></tr>
                                <?php else: foreach ($teacherSchedule as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                                    <td><?php echo htmlspecialchars($row['day']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['time_start'], 0, 5) . ' - ' . substr($row['time_end'], 0, 5)); ?></td>
                                    <td><?php echo htmlspecialchars($row['room']); ?></td>
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
