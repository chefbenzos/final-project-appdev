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

    $bills = viewBills($conn, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Payment Records</title>
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
                <a class="active" href="payment-records.php">Payment Records</a>
                <a href="admission-documents.php">Admission Documents</a>
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
                    <h2>Payment Records</h2>
                    <p>Records of payment throughout the school year.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Payment History</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Date</span>
                                <span>Reference Number</span>
                                <span>Payment Type</span>
                                <span>Amount</span>
                            </div>
                            <?php if (empty($bills)): ?>
                            <div class="data-grid-row">
                                <span>No payment records found.</span>
                            </div>
                            <?php else: foreach ($bills as $bill): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($bill['payment_date']); ?></span>
                                <span>PAY-<?php echo str_pad($bill['payment_id'], 6, '0', STR_PAD_LEFT); ?></span>
                                <span><?php echo htmlspecialchars($bill['payment_type']); ?></span>
                                <span>PHP <?php echo number_format($bill['amount_paid'], 2); ?></span>
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
