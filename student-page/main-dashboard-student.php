<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/main-dashboard-student.css">
    <title>Student Dashboard</title>
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
                <a class="active" href="main-dashboard-student.php">User Profile</a>
                <a href="academic-holds.php">Academic Holds</a>
                <a href="account-balance-summary.php">Account Balance Summary</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="admission-documents.php">Admission Documents</a>
                <a href="course-offerings.php">Course Offerings</a>
                <a href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="/final-project-appdev/landing-page/landing-page.php">Log out</a></div>
            <div class="profile-shell">
                <header class="topbar">
                    <div>
                        <h2>User Profile</h2>
                        <p>Student information and academic service status.</p>
                    </div>
                </header>

                <section class="panel" id="user-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Cleveland jr.</h3>
                            <p>Student ID: 2026-123-45</p>
                        </div>
                        <span class="status">Status: (either enrolled or nah)</span>
                    </div>

                    <div class="panel-body">
                        <div class="profile-grid">
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Student Number</td><td>2026xxxxx</td></tr>
                                        <tr><td>First Name</td><td>Du</td></tr>
                                        <tr><td>Middle Name</td><td>Bist</td></tr>
                                        <tr><td>Last Name</td><td>Gut Genug</td></tr>
                                        <tr><td>Personal Email Address</td><td>dubistgutgenug@gmail.com</td></tr>
                                        <tr><td>School Email Address</td><td>kcbacolod@piosang.home.page</td></tr>
                                        <tr><td>Citizenship</td><td>slightly FILIPINO</td></tr>
                                        <tr><td>Program</td><td>BSCSSE</td></tr>
                                        <tr><td>Curriculum Code</td><td>BSCSSE2024</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                            <tr><td>Year Level</td><td>2</td></tr>
                                            <tr><td>Registration Status</td><td>Maybe</td></tr>
                                            <tr><td>Student Type   </td><td>Cooked</td></tr>
                                            <tr><td>Scholarship</td><td>Harvard Scholarship</td></tr>
                                            <tr><td>Grades Viewing</td><td>Dont do this to yourself</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
