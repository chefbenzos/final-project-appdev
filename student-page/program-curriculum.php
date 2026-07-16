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

    $curriculum = getProgramCurriculum($conn, $_SESSION['curriculum_code']);

    // The current schema doesn't track a per-subject year level (only a per-student one),
    // so every subject in the program is shown under the student's own current year level.
    $yearLevel = $_SESSION['year_level'] ?? '1st Year';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Program Curriculum</title>
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
                <a href="course-offerings.php">Course Offerings</a>
                <a href="grade-report.php">Grade Report</a>
                <a class="active" href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Program Curriculum</h2>
                    <p>Subjects available for the student's program.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Curriculum Subjects</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Year Level</span>
                                <span>Subject Code</span>
                                <span>Subject Title</span>
                                <span>Units</span>
                            </div>
                            <?php if (empty($curriculum)): ?>
                            <div class="data-grid-row">
                                <span>No curriculum found for your program.</span>
                            </div>
                            <?php else: foreach ($curriculum as $subj): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($yearLevel); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($subj['units']); ?></span>
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
