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
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/main-dashboard-registrar.css">
    <title>Registrar Profile</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" aria-label="Main navigation">
            <div class="portal-brand">
                <h1>Registrar Portal</h1>
                <p>Student records management</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a class="active" href="main-dashboard-registrar.php">Registrar Profile</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a href="manage-requirements.php">Manage Requirements</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="registrar-shell">
                <header class="topbar">
                    <div>
                        <h2>Registrar Profile</h2>
                        <p>Registrar information and records service status.</p>
                    </div>
                    <div class="registrar-badge">
                        <span>Registrar</span>
                        <strong>A.Y. <?php echo date('Y') . '–' . (date('Y') + 1); ?></strong>
                    </div>
                </header>

                <section class="panel" id="registrar-profile">
                    <div class="panel-header">
                        <div>
                            <h3>Welcome, Registrar <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h3>
                            <p>Employee ID: REG-<?php echo str_pad($registrarId, 4, '0', STR_PAD_LEFT); ?></p>
                        </div>
                        <span class="status">Status: Active</span>
                    </div>
                    <div class="panel-body">
                        <div class="profile-grid">
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Employee ID</td><td>REG-<?php echo str_pad($registrarId, 4, '0', STR_PAD_LEFT); ?></td></tr>
                                        <tr><td>First Name</td><td><?php echo htmlspecialchars($_SESSION['first_name']); ?></td></tr>
                                        <tr><td>Last Name</td><td><?php echo htmlspecialchars($_SESSION['last_name']); ?></td></tr>
                                        <tr><td>Institute Email</td><td><?php echo htmlspecialchars($_SESSION['email']); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table>
                                    <tbody>
                                        <tr><td>Office</td><td>University Registrar</td></tr>
                                        <tr><td>Position</td><td>Registrar</td></tr>
                                        <tr><td>Employment Status</td><td>Active</td></tr>
                                        <tr><td>Academic Year</td><td><?php echo date('Y') . '–' . (date('Y') + 1); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
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
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Document Type</th>
                                    <th>Date Requested</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pendingRequests)): ?>
                                <tr><td colspan="4">No pending requests.</td></tr>
                                <?php else: foreach ($pendingRequests as $req): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($req['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($req['doc_type']); ?></td>
                                    <td><?php echo htmlspecialchars($req['submitted_at']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($req['status'])); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
