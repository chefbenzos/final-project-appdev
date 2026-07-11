<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/view-schedule-teacher.css">
    <title>View Schedule</title>
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
                <a href="main-dashhboard-teacher.php">Teacher Profile</a>
                <a href="view-courses.php">View Courses</a>
                <a href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a class="active" href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="landing-page.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>View Schedule</h2>
                    <p>Review your teaching schedule for the semester.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Weekly Schedule</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
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
