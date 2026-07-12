<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/main-dashboard-registrar.css">
    <title>Registrar Profile</title>
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
                <a class="active" href="main-dashboard-registrar.php">Registrar Profile</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="/final-project-appdev/index.php">Log out</a></div>
            <div class="registrar-shell">
                <header class="topbar">
                    <div>
                        <h2>Registrar Profile</h2>
                        <p>Registrar information and records service status.</p>
                    </div>
                    <div class="registrar-badge">
                        <span>Registrar</span>
                        <strong>A.Y. 2025–2026</strong>
                    </div>
                </header>

                <section class="panel" id="registrar-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Registrar Sample</h3>
                            <p>Employee ID: REG-2024-001</p>
                        </div>
                        <span class="status">Status: Active</span>
                    </div>
                    <div class="panel-body">
                        <div class="profile-grid">
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Employee ID</td><td>REG-2024-001</td></tr>
                                        <tr><td>First Name</td><td>--</td></tr>
                                        <tr><td>Middle Name</td><td>--</td></tr>
                                        <tr><td>Last Name</td><td>--</td></tr>
                                        <tr><td>Institute Email</td><td>--</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Office</td><td>University Registrar</td></tr>
                                        <tr><td>Position</td><td>Registrar</td></tr>
                                        <tr><td>Employment Status</td><td>Active</td></tr>
                                        <tr><td>Academic Year</td><td>2025–2026</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel" id="pending-requests">
                    <div class="panel-header">
                        <div>
                            <h3>Pending Document Requests</h3>
                            <p>Student requests awaiting processing.</p>
                        </div>
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
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
