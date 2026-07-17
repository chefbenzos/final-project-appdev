<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as an admin.");
        exit();
    }

    $adminId = $_SESSION['user_id'];

    $totalStudents = count(getAllStudents($conn));
    $totalTeachers = count(getAllTeachers($conn));
    $totalSubjects = count(getAllSubjects($conn));

    $scheduleCountResult = $conn->query("SELECT COUNT(*) AS total FROM schedules");
    $totalSchedules = $scheduleCountResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Admin Profile</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_University_Crest_512.png" alt="BHP University crest">
                </div>
                <h1>Admin Portal</h1>
                <p>System administration dashboard</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a class="active" href="main-dashboard-admin.php">Admin Profile</a>
                <a href="add-users.php">Add Teacher / Student</a>
                <a href="manage-courses.php">Manage Courses</a>
                <a href="assign-courses.php">Assign Courses to Teacher</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="profile-shell">
                <header class="topbar">
                    <div>
                        <h2>Admin Profile</h2>
                        <p>Administrator information and system overview.</p>
                    </div>
                </header>

                <section class="panel" id="admin-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h3>
                        </div>
                        <span class="status">Status: Active</span>
                    </div>
                    <div class="panel-body">
                        <div class="info-grid">
                            <div class="info-row"><span class="info-label">Employee ID</span><span>ADM-<?php echo str_pad($adminId, 4, '0', STR_PAD_LEFT); ?></span></div>
                            <div class="info-row"><span class="info-label">First Name</span><span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Last Name</span><span><?php echo htmlspecialchars($_SESSION['last_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Institute Email</span><span><?php echo htmlspecialchars($_SESSION['email']); ?></span></div>
                            <div class="info-row"><span class="info-label">Position</span><span>System Administrator</span></div>
                            <div class="info-row"><span class="info-label">Academic Year</span><span><?php echo date('Y') . '–' . (date('Y') + 1); ?></span></div>
                        </div>
                    </div>
                </section>

                <section class="panel" id="system-overview">
                    <div class="panel-header">
                        <div>
                            <h3>System Overview</h3>
                            <p>Current record counts across the portal.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span>Total Students</span>
                                <strong><?php echo $totalStudents; ?></strong>
                            </div>
                            <div class="summary-item">
                                <span>Total Teachers</span>
                                <strong><?php echo $totalTeachers; ?></strong>
                            </div>
                            <div class="summary-item">
                                <span>Total Courses</span>
                                <strong><?php echo $totalSubjects; ?></strong>
                            </div>
                            <div class="summary-item">
                                <span>Total Schedule Entries</span>
                                <strong><?php echo $totalSchedules; ?></strong>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
