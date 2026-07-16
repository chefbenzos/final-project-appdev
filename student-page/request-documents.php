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
    $saveMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_type'])) {
        $docType = trim($_POST['document_type']);
        $purpose = trim($_POST['request_purpose'] ?? '');

        $result = addDocument($conn, [
            'student_id' => $studentId,
            'doc_type'   => $docType,
            'status'     => 'pending',
            'remarks'    => $purpose !== '' ? "Purpose: $purpose" : 'Awaiting review.',
        ]);
        $saveMessage = $result['success'] ? "Request submitted successfully." : "Error: " . $result['error'];
    }

    // Show this student's past requests for the document types requestable on this page
    $requestDocTypes = ['Certificate of Registration', 'Transcript of Records', 'Certificate of Enrollment', 'Good Moral Certificate'];
    $allDocuments = viewDocuments($conn, $studentId);
    $myRequests = array_filter($allDocuments, function ($doc) use ($requestDocTypes) {
        return in_array($doc['doc_type'], $requestDocTypes);
    });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Request Documents</title>
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
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a class="active" href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Request Documents</h2>
                    <p>Dedicated registrar request area for student documents.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Request Form</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="request-documents.php">
                            <label for="document-type">Document Type</label>
                            <select id="document-type" name="document_type">
                                <option>Certificate of Registration</option>
                                <option>Transcript of Records</option>
                                <option>Certificate of Enrollment</option>
                                <option>Good Moral Certificate</option>
                            </select>

                            <label for="request-purpose">Purpose</label>
                            <textarea id="request-purpose" name="request_purpose" placeholder="Enter request purpose"></textarea>

                            <button type="submit">Submit Request</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Your Requests</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-3">
                            <div class="data-grid-header">
                                <span>Document Type</span>
                                <span>Date Requested</span>
                                <span>Status</span>
                            </div>
                            <?php if (empty($myRequests)): ?>
                            <div class="data-grid-row">
                                <span>No document requests submitted yet.</span>
                            </div>
                            <?php else: foreach ($myRequests as $req): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($req['doc_type']); ?></span>
                                <span><?php echo htmlspecialchars($req['submitted_at']); ?></span>
                                <span><?php echo htmlspecialchars(ucfirst($req['status'])); ?></span>
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
