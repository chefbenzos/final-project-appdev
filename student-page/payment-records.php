<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/payment-records.css">
    <title>Payment Records</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
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
            <div class="logout-row"><a href="/final-project-appdev/index.php">Log out</a></div>
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
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference Number</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>PHP 0.00</td>
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
