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
    $searchResult = null;
    $searchNotFound = false;

    if ($searchQuery !== '') {
        $searchResult = getStudentBalanceByNumber($conn, $searchQuery);
        $searchNotFound = ($searchResult === null);
    }

    $balances = getAllStudentBalances($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <title>Manage Balance</title>
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
                <a class="active" href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Balance</h2>
                    <p>Search a student's balance, or browse the full list below.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Search by Student ID</h3>
                    </div>
                    <div class="panel-body">
                        <form method="get" action="manage-balance.php" class="inline-form">
                            <input type="text" name="search_student_id" placeholder="e.g. 202600001" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit">Search</button>
                            <?php if ($searchQuery !== ''): ?>
                            <a href="manage-balance.php">Clear</a>
                            <?php endif; ?>
                        </form>

                        <?php if ($searchNotFound): ?>
                        <div class="notice">No student found with student number "<?php echo htmlspecialchars($searchQuery); ?>".</div>
                        <?php elseif ($searchResult): ?>
                        <div class="search-result-card">
                            <h4>Balance for <?php echo htmlspecialchars($searchResult['first_name'] . ' ' . $searchResult['last_name']); ?> (<?php echo htmlspecialchars($searchResult['student_number']); ?>)</h4>
                            <div class="info-grid">
                                <div class="info-row"><span class="info-label">Total Assessment</span><span>PHP <?php echo number_format($searchResult['total_assessment'], 2); ?></span></div>
                                <div class="info-row"><span class="info-label">Amount Paid</span><span>PHP <?php echo number_format($searchResult['total_paid'], 2); ?></span></div>
                                <div class="info-row"><span class="info-label">Remaining Balance</span><span>PHP <?php echo number_format($searchResult['remaining_balance'], 2); ?></span></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Student Balance Records</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-6">
                            <div class="data-grid-header">
                                <span>Student No.</span>
                                <span>Last Name</span>
                                <span>First Name</span>
                                <span>Total Assessment</span>
                                <span>Amount Paid</span>
                                <span>Remaining Balance</span>
                            </div>
                            <?php if (empty($balances)): ?>
                            <div class="data-grid-row"><span>No students found.</span></div>
                            <?php else: foreach ($balances as $b): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($b['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($b['last_name']); ?></span>
                                <span><?php echo htmlspecialchars($b['first_name']); ?></span>
                                <span>PHP <?php echo number_format($b['total_assessment'], 2); ?></span>
                                <span>PHP <?php echo number_format($b['total_paid'], 2); ?></span>
                                <span>PHP <?php echo number_format($b['remaining_balance'], 2); ?></span>
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
