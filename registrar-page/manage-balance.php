<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/manage-balance.css">
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
            <div class="logout-row"><a href="/final-project-appdev/index.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Balance</h2>
                    <p>View and update the remaining balance of each student.</p>
                </header>

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
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>PHP 0.00</td>
                                    <td>PHP 0.00</td>
                                    <td>PHP 0.00</td>
                                    <td>
                                        <input type="number" name="adjustment" placeholder="Amount" step="0.01">
                                        <button type="button">Update</button>
                                    </td>
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
