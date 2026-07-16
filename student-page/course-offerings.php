<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a student
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a student.");
        exit();
    }

    $offerings = getCourseOfferings($conn, $_SESSION['curriculum_code']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Course Offerings</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_University_Crest_512.png" alt="BHP University crest">
                </div>
                <h1>Student Portal</h1>
                <p>Academic services dashboard</p>
            </div>
            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-student.php">User Profile</a>
                <a href="academic-holds.php">Academic Holds</a>
                <a href="account-balance-summary.php">Account Balance Summary</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="admission-documents.php">Admission Documents</a>
                <a class="active" href="course-offerings.php">Course Offerings</a>
                <a href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Course Offerings</h2>
                    <p>Available schedules for the student's specific course.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Available Schedules</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Course Code</span>
                                <span>Subject</span>
                                <span>Schedule</span>
                                <span>Room</span>
                            </div>
                            <?php if (empty($offerings)): ?>
                            <div class="data-grid-row">
                                <span>No course offerings found for your program.</span>
                            </div>
                            <?php else: foreach ($offerings as $row): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($row['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($row['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($row['day'] . ' ' . substr($row['time_start'], 0, 5) . '-' . substr($row['time_end'], 0, 5)); ?></span>
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
