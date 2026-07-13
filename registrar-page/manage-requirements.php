<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/manage-requirements.css">
    <title>Manage Requirements</title>
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
                <a href="manage-transactions.php">Manage Transactions</a>
                <a class="active" href="manage-requirements.php">Manage Requirements</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="/final-project-appdev/index.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Requirements</h2>
                    <p>Review and update student admission requirements and document requests.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Admission Requirements</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Requirement</th>
                                    <th>Current Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <select name="req_status">
                                            <option value="pending">Pending</option>
                                            <option value="submitted">Submitted</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                        <button type="button">Update</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Requests</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Document Type</th>
                                    <th>Purpose</th>
                                    <th>Date Requested</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <select name="doc_status">
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="ready">Ready for Pickup</option>
                                            <option value="released">Released</option>
                                        </select>
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
