<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/stylesheets/manage-grades.css">
    <title>Manage Grades</title>
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
                <a class="active" href="manage-grades.php">Manage Grades</a>
                <a href="manage-attendance.php">Manage Attendance</a>
                <a href="view-schedule-teacher.php">View Schedule</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="landing-page.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Grades</h2>
                    <p>Enter and update student grades per course.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Select Course</h3>
                    </div>
                    <div class="panel-body">
                        <label for="grade-course-select">Course</label>
                        <select id="grade-course-select" name="course_select">
                            <option value="">-- Select a Course --</option>
                        </select>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Student Grade List</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Midterm Grade</th>
                                    <th>Final Grade</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td><input type="number" name="midterm" placeholder="0.00" step="0.01"></td>
                                    <td><input type="number" name="final" placeholder="0.00" step="0.01"></td>
                                    <td>--</td>
                                    <td><button type="button">Save</button></td>
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
