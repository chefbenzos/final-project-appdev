<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/grade-report.css">
    <title>Grade Report</title>
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
                <a href="payment-records.php">Payment Records</a>
                <a href="admission-documents.php">Admission Documents</a>
                <a href="course-offerings.php">Course Offerings</a>
                <a class="active" href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="/final-project-appdev/index.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Grade Report</h2>
                    <p>Registered schedule and grades.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Grades</h3>
                    </div>
                    <div class="panel-body">
                        <form class="grade-filter-form" method="get" action="grade-report.php">
                            <label for="term-school-year">Term - School Year:</label>
                            <div class="grade-filter-controls">
                                <select id="term-school-year" name="term_school_year">
                                    <option>--</option>
                                    <option value="1-20252026">1 - 20252026</option>
                                </select>
                                <button type="submit">View Grades</button>
                            </div>
                        </form>
                        <div class="notice">Grade viewing is locked while the account balance is below the required threshold.</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Units</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
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
