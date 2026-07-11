<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/main-dashboard-teacher.css">
    <title>Teacher Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
                <h1>Teacher Portal</h1>
                <p>Academic management dashboard</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a class="active" href="main-dashhboard-teacher.php">Teacher Profile</a>
                <a href="view-courses.php">View Courses</a>
                <a href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="landing-page.php">Log out</a></div>
            <div class="teacher-shell">
                <header class="topbar">
                    <div>
                        <h2>Teacher Profile</h2>
                        <p>Faculty information and academic service status.</p>
                    </div>
                </header>

                <section class="panel" id="teacher-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Prof. Sample</h3>
                            <p>Faculty ID: TCH-2024-001</p>
                        </div>
                        <span class="status">Status: Faculty</span>
                    </div>

                    <div class="panel-body">
                        <div class="profile-grid">
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Faculty ID</td><td>TCH-2024-001</td></tr>
                                        <tr><td>First Name</td><td>--</td></tr>
                                        <tr><td>Last Name</td><td>--</td></tr>
                                        <tr><td>Department</td><td>--</td></tr>
                                        <tr><td>Institute Email</td><td>--</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Employment Status</td><td>--</td></tr>
                                        <tr><td>Courses Handled</td><td>--</td></tr>
                                        <tr><td>Academic Year</td><td>2025–2026</td></tr>
                                        <tr><td>Semester</td><td>--</td></tr>
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
