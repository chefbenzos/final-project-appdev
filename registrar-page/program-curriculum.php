<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';
    require ROOT_PATH . '/database/db-functions.php';

    // Check if the user is logged in and is a registrar
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'registrar') {
        header("Location: " . ROOT . "/index.php?msg=Access denied. Please log in as a registrar.");
        exit();
    }

    $curricula = getAllCurricula($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Program Curriculum</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>
            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_University_Crest_512.png" alt="BHP University crest">
                </div>
                <h1>Registrar Portal</h1>
                <p>Student records management</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-registrar.php">Registrar Profile</a>
                <a href="enroll-student.php">Enroll Student</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a class="active" href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Program Curriculum</h2>
                    <p>Subjects offered across every program.</p>
                </header>

                <section class="panel">
                    <div class="panel-header">
                        <h3>All Curricula</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Program</span>
                                <span>Subject Code</span>
                                <span>Subject Title</span>
                                <span>Units</span>
                            </div>
                            <?php if (empty($curricula)): ?>
                            <div class="data-grid-row"><span>No curriculum found.</span></div>
                            <?php else: foreach ($curricula as $subj): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($subj['program']); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($subj['units']); ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
