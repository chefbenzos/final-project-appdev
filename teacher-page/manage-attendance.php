<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/manage-attendance.css">
    <title>Manage Attendance</title>
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
                <a class="active" href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="/landing-page/landing-page.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Attendance</h2>
                    <p>Record and track student attendance per class meeting.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Select Course and Date</h3>
                    </div>
                    <div class="panel-body">
                        <label for="att-course-select">Course</label>
                        <select id="att-course-select" name="att_course">
                            <option value="">-- Select a Course --</option>
                        </select>

                        <br><br>

                        <label for="att-date">Meeting Date</label>
                        <input type="date" id="att-date" name="att_date">
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Attendance Sheet</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <select name="status">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                            <option value="excused">Excused</option>
                                        </select>
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
