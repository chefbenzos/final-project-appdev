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

    $studentId = $_SESSION['user_id'];
    $schedule = viewStudentSchedule($conn, $studentId);
    $bills = viewBills($conn, $studentId);

    $totalAssessment = 0;
    foreach ($bills as $bill) {
        $totalAssessment += $bill['total_fees'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Schedule and Assessment</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_Icon.png" alt="Logo">
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
                <a href="course-offerings.php">Course Offerings</a>
                <a href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a class="active" href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Schedule and Assessment</h2>
                    <p>Student schedule and tuition calculation.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Class Schedule</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Subject</span>
                                <span>Day</span>
                                <span>Time</span>
                                <span>Room</span>
                            </div>
                            <?php if (empty($schedule)): ?>
                            <div class="data-grid-row">
                                <span>No enrolled schedule found.</span>
                            </div>
                            <?php else: foreach ($schedule as $row): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($row['subject_code'] . ' - ' . $row['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($row['day']); ?></span>
                                <span><?php echo htmlspecialchars(substr($row['time_start'], 0, 5) . ' - ' . substr($row['time_end'], 0, 5)); ?></span>
                                <span><?php echo htmlspecialchars($row['room']); ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Assessment</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-2">
                            <div class="data-grid-header">
                                <span>Description</span>
                                <span>Amount</span>
                            </div>
                            <div class="data-grid-row">
                                <span>Tuition and fees</span>
                                <span>PHP <?php echo number_format($totalAssessment, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
