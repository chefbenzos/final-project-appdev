<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a student
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a student.");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Student Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <div class="logo-container">
                    <img src="<?=ROOT?>/images/logo.png" alt="Logo" class="logo">
                </div>
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
                <a href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
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
                            <h3>Welcome, <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h3>
                        </div>
                        <span class="status">Status: <?php echo $_SESSION['registration_status']; ?></span>
                    </div>

                    <div class="panel-body">
                        <div class="info-grid">
                            <div>
                                <div class="info-row"><span class="info-label">Student Number</span><span><?php echo $_SESSION['student_number']; ?></span></div>
                                <div class="info-row"><span class="info-label">First Name</span><span><?php echo $_SESSION['first_name']; ?></span></div>
                                <div class="info-row"><span class="info-label">Middle Initial</span><span><?php echo $_SESSION['middle_initial']; ?></span></div>
                                <div class="info-row"><span class="info-label">Last Name</span><span><?php echo $_SESSION['last_name']; ?></span></div>
                                <div class="info-row"><span class="info-label">Personal Email Address</span><span><?php echo $_SESSION['personal_email']; ?></span></div>
                                <div class="info-row"><span class="info-label">School Email Address</span><span><?php echo $_SESSION['email']; ?></span></div>
                                <div class="info-row"><span class="info-label">Citizenship</span><span><?php echo $_SESSION['citizenship']; ?></span></div>
                                <div class="info-row"><span class="info-label">Program</span><span><?php echo $_SESSION['curriculum_code']; ?></span></div>
                            </div>
                            <div>
                                <div class="info-row"><span class="info-label">Year Level</span><span><?php echo $_SESSION['year_level']; ?></span></div>
                                <div class="info-row"><span class="info-label">Registration Status</span><span><?php echo $_SESSION['registration_status']; ?></span></div>
                                <div class="info-row"><span class="info-label">Student Type</span><span><?php echo $_SESSION['student_type']; ?></span></div>
                                <div class="info-row"><span class="info-label">Scholarship</span><span><?php echo $_SESSION['scholarship']; ?></span></div>
                                <div class="info-row"><span class="info-label">Grades Viewing</span><span><?php echo !empty($_SESSION['grades']) ? 'Available' : 'Not Available'; ?></span></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
