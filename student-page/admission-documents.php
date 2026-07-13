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

    // Admission requirements vs later document requests are distinguished by doc_type
    // (kept consistent with registrar-page/manage-requirements.php).
    $requestDocTypes = ['Certificate of Registration', 'Transcript of Records', 'Certificate of Enrollment', 'Good Moral Certificate'];

    $allDocuments = viewDocuments($conn, $_SESSION['user_id']);
    $admissionDocuments = array_filter($allDocuments, function ($doc) use ($requestDocTypes) {
        return !in_array($doc['doc_type'], $requestDocTypes);
    });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/main-dashboard-student.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Admission Documents</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <h1>Student Portal</h1>
                <p>Academic services dashboard</p>
            </div>
            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-student.php">User Profile</a>
                <a href="academic-holds.php">Academic Holds</a>
                <a href="account-balance-summary.php">Account Balance Summary</a>
                <a href="payment-records.php">Payment Records</a>
                <a class="active" href="admission-documents.php">Admission Documents</a>
                <a href="course-offerings.php">Course Offerings</a>
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
                    <h2>Admission Documents</h2>
                    <p>Submitted, missing, and pending admission files.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Status</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-3">
                            <div class="data-grid-header">
                                <span>Document</span>
                                <span>Status</span>
                                <span>Remarks</span>
                            </div>
                            <?php if (empty($admissionDocuments)): ?>
                            <div class="data-grid-row">
                                <span>No admission documents on file yet.</span>
                            </div>
                            <?php else: foreach ($admissionDocuments as $doc): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($doc['doc_type']); ?></span>
                                <span><?php echo htmlspecialchars(ucfirst($doc['status'])); ?></span>
                                <span><?php echo htmlspecialchars($doc['remarks'] ?? '--'); ?></span>
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
