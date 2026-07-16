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

    $saveMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_no'], $_POST['amount'])) {
        $studentNumber = trim($_POST['student_no']);
        $amount = (float) $_POST['amount'];
        $reason = trim($_POST['reason'] ?? '');
        $method = trim($_POST['payment_method'] ?? '');
        $studentId = getStudentIdByNumber($conn, $studentNumber);

        if (!$studentId) {
            $saveMessage = "No student found with student number \"$studentNumber\".";
        } elseif ($amount <= 0) {
            $saveMessage = "Amount must be greater than zero.";
        } else {
            $paymentType = $method !== '' ? ucfirst($method) : 'Cash';
            if ($reason !== '') {
                $paymentType .= ' - ' . $reason;
            }
            $paymentType = substr($paymentType, 0, 50);

            $result = addPayment($conn, [
                'student_id'   => $studentId,
                'total_fees'   => 0,
                'amount_paid'  => $amount,
                'payment_type' => $paymentType,
                'payment_date' => date('Y-m-d'),
                'semester'     => '1st Semester',
                'school_year'  => (int) date('Y'),
            ]);
            $saveMessage = $result['success'] ? "Transaction recorded." : "Error: " . $result['error'];
        }
    }

    $transactions = getAllPayments($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <title>Manage Transactions</title>
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
                <a class="active" href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Transactions</h2>
                    <p>Log and review all student payment transactions.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Add Transaction</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="manage-transactions.php">
                            <label for="txn-student-no">Student Number</label>
                            <input type="text" id="txn-student-no" name="student_no" placeholder="e.g. 202600001" required>

                            <label for="txn-amount">Amount Paid (PHP)</label>
                            <input type="number" id="txn-amount" name="amount" placeholder="0.00" step="0.01" min="0.01" required>

                            <label for="txn-reason">Reason / Description</label>
                            <input type="text" id="txn-reason" name="reason" placeholder="e.g. Tuition payment - 1st semester">

                            <label for="txn-method">Payment Method</label>
                            <select id="txn-method" name="payment_method">
                                <option value="">-- Select Method --</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="check">Check</option>
                            </select>

                            <button type="submit">Record Transaction</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Transaction History</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Date</span>
                                <span>Student No.</span>
                                <span>Payment Type</span>
                                <span>Amount</span>
                            </div>
                            <?php if (empty($transactions)): ?>
                            <div class="data-grid-row"><span>No transactions recorded yet.</span></div>
                            <?php else: foreach ($transactions as $t): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($t['payment_date']); ?></span>
                                <span><?php echo htmlspecialchars($t['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($t['payment_type']); ?></span>
                                <span>PHP <?php echo number_format($t['amount_paid'], 2); ?></span>
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
