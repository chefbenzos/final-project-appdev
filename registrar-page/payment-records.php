<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a registrar
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'registrar') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a registrar.");
        exit();
    }

    $searchQuery = trim($_GET['search_student_id'] ?? '');
    $searchResults = [];
    $searchAttempted = ($searchQuery !== '');

    if ($searchAttempted) {
        $searchResults = getPaymentsByStudentNumber($conn, $searchQuery);
    }

    // A registrar-wide view of every payment across every student
    $payments = getAllPayments($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <title>Payment Records</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
                <h1>Registrar Portal</h1>
                <p>Student records management</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-registrar.php">Registrar Profile</a>
                <a href="enroll-student.php">Enroll Student</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a class="active" href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Payment Records</h2>
                    <p>Search a student's payment history, or browse the full list below.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Search by Student ID</h3>
                    </div>
                    <div class="panel-body">
                        <form method="get" action="payment-records.php" class="inline-form">
                            <input type="text" name="search_student_id" placeholder="e.g. 202600001" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit">Search</button>
                            <?php if ($searchQuery !== ''): ?>
                            <a href="payment-records.php">Clear</a>
                            <?php endif; ?>
                        </form>

                        <?php if ($searchAttempted): ?>
                            <?php if (empty($searchResults)): ?>
                            <div class="notice">No payment records found for student number "<?php echo htmlspecialchars($searchQuery); ?>".</div>
                            <?php else: ?>
                            <div class="search-result-card">
                                <h4>Payment History for <?php echo htmlspecialchars($searchResults[0]['first_name'] . ' ' . $searchResults[0]['last_name']); ?> (<?php echo htmlspecialchars($searchQuery); ?>)</h4>
                                <div class="data-grid cols-3">
                                    <div class="data-grid-header">
                                        <span>Date</span>
                                        <span>Payment Type</span>
                                        <span>Amount</span>
                                    </div>
                                    <?php foreach ($searchResults as $p): ?>
                                    <div class="data-grid-row">
                                        <span><?php echo htmlspecialchars($p['payment_date']); ?></span>
                                        <span><?php echo htmlspecialchars($p['payment_type']); ?></span>
                                        <span>PHP <?php echo number_format($p['amount_paid'], 2); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>All Payment Records</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Date</span>
                                <span>Student No.</span>
                                <span>Payment Type</span>
                                <span>Amount</span>
                            </div>
                            <?php if (empty($payments)): ?>
                            <div class="data-grid-row"><span>No payment records found.</span></div>
                            <?php else: foreach ($payments as $p): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($p['payment_date']); ?></span>
                                <span><?php echo htmlspecialchars($p['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($p['payment_type']); ?></span>
                                <span>PHP <?php echo number_format($p['amount_paid'], 2); ?></span>
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
