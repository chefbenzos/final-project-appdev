<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/manage-transactions.css">
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
                <a href="main-dashboard-registrar.php">Dashboard</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a class="active" href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="landing-page.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Transactions</h2>
                    <p>Log and review all student payment transactions.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Add Transaction</h3>
                    </div>
                    <div class="panel-body">
                        <form>
                            <label for="txn-student-no">Student Number</label>
                            <input type="text" id="txn-student-no" name="student_no" placeholder="e.g. 2024-12345">

                            <label for="txn-amount">Amount Paid (PHP)</label>
                            <input type="number" id="txn-amount" name="amount" placeholder="0.00" step="0.01">

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

                            <button type="button">Record Transaction</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Transaction History</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student No.</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>PHP 0.00</td>
                                    <td>--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
