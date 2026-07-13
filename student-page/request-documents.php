<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';

    // Check if the user is logged in and is a student
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a student.");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/main-dashboard-student.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Request Documents</title>
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
                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Request Form</h3>
                    </div>
                    <div class="panel-body">
                        <form>
                            <label for="document-type">Document Type</label>
                            <select id="document-type" name="document_type">
                                <option>Certificate of Registration</option>
                                <option>Transcript of Records</option>
                                <option>Certificate of Enrollment</option>
                                <option>Good Moral Certificate</option>
                            </select>

                            <label for="request-purpose">Purpose</label>
                            <textarea id="request-purpose" name="request_purpose" placeholder="Enter request purpose"></textarea>

                            <button type="button">Submit Request</button>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
