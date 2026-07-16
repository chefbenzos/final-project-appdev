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

    $registrarId = $_SESSION['user_id'];
    $pendingRequests = getPendingDocumentRequests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Registrar Profile</title>
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
                <a class="active" href="main-dashboard-registrar.php">Registrar Profile</a>
                <a href="enroll-student.php">Enroll Student</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="profile-shell">
                <header class="topbar">
                    <div>
                        <h2>Registrar Profile</h2>
                        <p>Registrar information and records service status.</p>
                    </div>
                </header>

                <section class="panel" id="registrar-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Registrar <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h3>
                        </div>
                        <span class="status">Status: Active</span>
                    </div>
                    <div class="panel-body">
                        <div class="info-grid">
                            <div class="info-row"><span class="info-label">Employee ID</span><span>REG-<?php echo str_pad($registrarId, 4, '0', STR_PAD_LEFT); ?></span></div>
                            <div class="info-row"><span class="info-label">First Name</span><span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Last Name</span><span><?php echo htmlspecialchars($_SESSION['last_name']); ?></span></div>
                            <div class="info-row"><span class="info-label">Institute Email</span><span><?php echo htmlspecialchars($_SESSION['email']); ?></span></div>
                            <div class="info-row"><span class="info-label">Office</span><span>University Registrar</span></div>
                            <div class="info-row"><span class="info-label">Position</span><span>Registrar</span></div>
                            <div class="info-row"><span class="info-label">Employment Status</span><span>Active</span></div>
                            <div class="info-row"><span class="info-label">Academic Year</span><span><?php echo date('Y') . '–' . (date('Y') + 1); ?></span></div>
                        </div>
                    </div>
                </section>

                <section class="panel" id="pending-requests">
                    <div class="panel-header">
                        <div>
                            <h3>Pending Document Requests</h3>
                            <p>Student requests awaiting processing.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Student No.</span>
                                <span>Document Type</span>
                                <span>Date Requested</span>
                                <span>Status</span>
                            </div>
                            <?php if (empty($pendingRequests)): ?>
                            <div class="data-grid-row"><span>No pending requests.</span></div>
                            <?php else: foreach ($pendingRequests as $req): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($req['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($req['doc_type']); ?></span>
                                <span><?php echo htmlspecialchars($req['submitted_at']); ?></span>
                                <span><?php echo htmlspecialchars(ucfirst($req['status'])); ?></span>
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
