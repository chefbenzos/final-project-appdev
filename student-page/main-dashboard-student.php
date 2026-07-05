<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            width: 280px;
            height: 100vh;
            overflow-y: auto;
            padding: 24px 18px;
        }

        .portal-brand {
            padding: 0 8px 22px;
            border-bottom: 1px solid;
        }

        .portal-brand h1 {
            margin: 0;
        }

        .portal-brand p {
            margin: 8px 0 0;
        }

        .nav-section-title {
            display: block;
            margin: 24px 8px 10px;
        }

        .taskbar {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .taskbar a {
            display: block;
            padding: 11px 12px;
        }

        .dashboard-main {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        .logout-row {
            text-align: right;
            margin-bottom: 5px;
        }

        .profile-shell {
            width: 100%;
            max-width: 100vw;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .topbar h2 {
            margin: 0;
        }

        .topbar p {
            margin: 6px 0 0;
        }

        .student-badge {
            min-width: 190px;
            padding: 12px 14px;
            text-align: right;
        }

        .student-badge span {
            display: block;
        }

        .student-badge strong {
            display: block;
            margin-top: 4px;
        }

        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
        }

        .panel-header h3 {
            margin: 0;
        }

        .panel-header p {
            margin: 5px 0 0;
        }

        .panel-body {
            padding: 20px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .profile-item {
            padding: 14px;
            min-height: 74px;
        }

        .profile-item label {
            display: block;
        }

        .profile-item span {
            display: block;
            margin-top: 8px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 88px;
            padding: 5px 9px;
            white-space: nowrap;
        }  
    </style>
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
            <div class="logout-row"><a href="landing-page.php">Log out</a></div>
            <div class="profile-shell">
                <header class="topbar">
                    <div>
                        <h2>User Profile</h2>
                        <p>Student information and academic service status.</p>
                    </div>

                    <div class="student-badge">
                        <span>Student Number</span>
                        <strong>2026-00000</strong>
                    </div>
                </header>

                <section class="panel" id="user-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Main Profile</h3>
                            <p>Primary student record details.</p>
                        </div>
                        <span class="pill success">Enrolled</span>
                    </div>

                    <div class="panel-body">
                        <div class="profile-grid">
                            <div class="profile-item">
                                <label>Student Number</label>
                                <span>199411108</span>
                            </div>
                            <div class="profile-item">
                                <label>Last Name</label>
                                <span>Home Page</span>
                            </div>
                            <div class="profile-item">
                                <label>First Name</label>
                                <span>Piosang</span>
                            </div>
                            <div class="profile-item">
                                <label>Middle Initial</label>
                                <span>E.</span>
                            </div>
                            <div class="profile-item">
                                <label>Personal Email</label>
                                <span>student@email.com</span>
                            </div>
                            <div class="profile-item">
                                <label>Institute Email</label>
                                <span>student@piosang.home.page</span>
                            </div>
                            <div class="profile-item">
                                <label>Citizenship</label>
                                <span>Filipino</span>
                            </div>
                            <div class="profile-item">
                                <label>Curriculum Code</label>
                                <span>BSCS-2026</span>
                            </div>
                            <div class="profile-item">
                                <label>Enrollment Status</label>
                                <span>Enrolled</span>
                            </div>
                            <div class="profile-item">
                                <label>Year Level</label>
                                <span>3rd Year</span>
                            </div>
                            <div class="profile-item">
                                <label>Registration Status</label>
                                <span>Registered</span>
                            </div>
                            <div class="profile-item">
                                <label>Student Type</label>
                                <span>Regular</span>
                            </div>
                            <div class="profile-item">
                                <label>Scholarship</label>
                                <span>None</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
