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

    $requestDocTypes = ['Certificate of Registration', 'Transcript of Records', 'Certificate of Enrollment', 'Good Moral Certificate'];

    $saveMessage = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_id'], $_POST['new_status'])) {
        $result = updateDocumentStatus($conn, (int) $_POST['document_id'], $_POST['new_status'], 'Updated by registrar.');
        $saveMessage = $result['success'] ? "Status updated." : "Error: " . $result['error'];
    }

    $allDocuments = getAllDocuments($conn);
    $admissionRequirements = [];
    $documentRequests = [];

    foreach ($allDocuments as $doc) {
        if (in_array($doc['doc_type'], $requestDocTypes)) {
            $documentRequests[] = $doc;
        } else {
            $admissionRequirements[] = $doc;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Manage Requirements</title>
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
                <a class="active" href="manage-requirements.php">Manage Requirements</a>
                <a href="payment-records.php">Payment Records</a>
                <a href="program-curriculum.php">Program Curriculum</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Requirements</h2>
                    <p>Review and update student admission requirements and document requests.</p>
                </header>

                <?php if ($saveMessage): ?>
                <div class="notice"><?php echo htmlspecialchars($saveMessage); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Admission Requirements</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-6">
                            <div class="data-grid-header">
                                <span>Student No.</span>
                                <span>Last Name</span>
                                <span>First Name</span>
                                <span>Requirement</span>
                                <span>Current Status</span>
                                <span>Action</span>
                            </div>
                            <?php if (empty($admissionRequirements)): ?>
                            <div class="data-grid-row"><span>No admission requirement records found.</span></div>
                            <?php else: foreach ($admissionRequirements as $doc): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($doc['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($doc['last_name']); ?></span>
                                <span><?php echo htmlspecialchars($doc['first_name']); ?></span>
                                <span><?php echo htmlspecialchars($doc['doc_type']); ?></span>
                                <span><?php echo htmlspecialchars(ucfirst($doc['status'])); ?></span>
                                <span>
                                    <form method="post" action="manage-requirements.php" class="inline-form">
                                        <input type="hidden" name="document_id" value="<?php echo $doc['document_id']; ?>">
                                        <select name="new_status">
                                            <option value="pending" <?php echo $doc['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="submitted" <?php echo $doc['status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                            <option value="approved" <?php echo $doc['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $doc['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Requests</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-5">
                            <div class="data-grid-header">
                                <span>Student No.</span>
                                <span>Document Type</span>
                                <span>Date Requested</span>
                                <span>Status</span>
                                <span>Action</span>
                            </div>
                            <?php if (empty($documentRequests)): ?>
                            <div class="data-grid-row"><span>No document requests found.</span></div>
                            <?php else: foreach ($documentRequests as $doc): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($doc['student_number']); ?></span>
                                <span><?php echo htmlspecialchars($doc['doc_type']); ?></span>
                                <span><?php echo htmlspecialchars($doc['submitted_at']); ?></span>
                                <span><?php echo htmlspecialchars(ucfirst($doc['status'])); ?></span>
                                <span>
                                    <form method="post" action="manage-requirements.php" class="inline-form">
                                        <input type="hidden" name="document_id" value="<?php echo $doc['document_id']; ?>">
                                        <select name="new_status">
                                            <option value="pending" <?php echo $doc['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $doc['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="ready" <?php echo $doc['status'] === 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                                            <option value="released" <?php echo $doc['status'] === 'released' ? 'selected' : ''; ?>>Released</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </span>
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
