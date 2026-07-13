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

    // Handle a balance adjustment (recorded as a payment with no new assessment,
    // just an amount paid, so it reduces the student's outstanding balance).
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['adjustment'])) {
        $studentId = (int) $_POST['student_id'];
        $adjustment = (float) $_POST['adjustment'];

        if ($adjustment > 0) {
            $result = addPayment($conn, [
                'student_id'   => $studentId,
                'total_fees'   => 0,
                'amount_paid'  => $adjustment,
                'payment_type' => 'Balance Adjustment',
                'payment_date' => date('Y-m-d'),
                'semester'     => '1st Semester',
                'school_year'  => (int) date('Y'),
            ]);
            $saveMessage = $result['success'] ? "Payment recorded." : "Error: " . $result['error'];
        }
    }

    $balances = getAllStudentBalances($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/manage-balance.css">
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
                <a href="main-dashboard-registrar.php">Dashboard</a>
                <a class="active" href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Balance</h2>
                    <p>View and update the remaining balance of each student.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Student Balance Records</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Total Assessment</th>
                                    <th>Amount Paid</th>
                                    <th>Remaining Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($balances)): ?>
                                <tr><td colspan="7">No students found.</td></tr>
                                <?php else: foreach ($balances as $b): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($b['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($b['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($b['first_name']); ?></td>
                                    <td>PHP <?php echo number_format($b['total_assessment'], 2); ?></td>
                                    <td>PHP <?php echo number_format($b['total_paid'], 2); ?></td>
                                    <td>PHP <?php echo number_format($b['remaining_balance'], 2); ?></td>
                                    <td>
                                        <form method="post" action="manage-balance.php" style="display:flex; gap:6px;">
                                            <input type="hidden" name="student_id" value="<?php echo $b['student_id']; ?>">
                                            <input type="number" name="adjustment" placeholder="Amount" step="0.01" min="0.01" required>
                                            <button type="submit">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
