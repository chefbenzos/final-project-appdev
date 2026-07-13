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

    // Requested documents (transcripts, certificates, etc.) vs admission requirements
    // (birth certificate, Form 138, etc.) both live in the same `documents` table,
    // distinguished here by doc_type.
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
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/manage-requirements.css">
    <title>Manage Requirements</title>
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
                <a href="main-dashboard-registrar.php">Dashboard</a>
                <a href="manage-balance.php">Manage Balance</a>
                <a href="manage-transactions.php">Manage Transactions</a>
                <a class="active" href="manage-requirements.php">Manage Requirements</a>
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
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Requirement</th>
                                    <th>Current Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($admissionRequirements)): ?>
                                <tr><td colspan="6">No admission requirement records found.</td></tr>
                                <?php else: foreach ($admissionRequirements as $doc): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doc['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($doc['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($doc['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($doc['doc_type']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($doc['status'])); ?></td>
                                    <td>
                                        <form method="post" action="manage-requirements.php" style="display:flex; gap:6px;">
                                            <input type="hidden" name="document_id" value="<?php echo $doc['document_id']; ?>">
                                            <select name="new_status">
                                                <option value="pending" <?php echo $doc['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="submitted" <?php echo $doc['status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                                <option value="approved" <?php echo $doc['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="rejected" <?php echo $doc['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Document Requests</h3>
                    </div>
                    <div class="panel-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Document Type</th>
                                    <th>Date Requested</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documentRequests)): ?>
                                <tr><td colspan="5">No document requests found.</td></tr>
                                <?php else: foreach ($documentRequests as $doc): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doc['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($doc['doc_type']); ?></td>
                                    <td><?php echo htmlspecialchars($doc['submitted_at']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($doc['status'])); ?></td>
                                    <td>
                                        <form method="post" action="manage-requirements.php" style="display:flex; gap:6px;">
                                            <input type="hidden" name="document_id" value="<?php echo $doc['document_id']; ?>">
                                            <select name="new_status">
                                                <option value="pending" <?php echo $doc['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $doc['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="ready" <?php echo $doc['status'] === 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                                                <option value="released" <?php echo $doc['status'] === 'released' ? 'selected' : ''; ?>>Released</option>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                    </td>
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
