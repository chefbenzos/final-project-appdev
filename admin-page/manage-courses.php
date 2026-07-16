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

    $errors = [];
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $subjectCode = trim($_POST['subject_code'] ?? '');
        $subjectName = trim($_POST['subject_name'] ?? '');
        $units = trim($_POST['units'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $program = trim($_POST['program'] ?? '');

        if ($subjectCode === '' || $subjectName === '' || $units === '' || $program === '') {
            $errors[] = "Subject code, subject name, units, and program are all required.";
        }

        if (empty($errors)) {
            $result = addSubject($conn, [
                'subject_code' => $subjectCode,
                'subject_name' => $subjectName,
                'units'        => (int) $units,
                'department'   => $department,
                'program'      => $program,
            ]);

            if ($result['success']) {
                $success = "Course \"$subjectCode - $subjectName\" was added successfully.";
            } else {
                $errors[] = "Error adding course: " . $result['error'];
            }
        }
    }

    $programs = getAllPrograms($conn);
    $allSubjects = getAllSubjects($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/main-dashboard-admin.css">
    <script src="<?=ROOT?>/javascript/main-dashboard-student.js" defer></script>
    <title>Manage Courses</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <h1>Admin Portal</h1>
                <p>System administration dashboard</p>
            </div>

            <span class="nav-section-title">Navigation Taskbar</span>
            <nav class="taskbar">
                <a href="main-dashboard-admin.php">Admin Profile</a>
                <a href="add-users.php">Add Teacher / Student</a>
                <a class="active" href="manage-courses.php">Manage Courses</a>
                <a href="assign-courses.php">Assign Courses to Teacher</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Manage Courses</h2>
                    <p>Add new courses and review the full course catalog.</p>
                </header>

                <?php if (!empty($errors)): ?>
                <div class="notice">
                    <?php foreach ($errors as $err): ?>
                        <?php echo htmlspecialchars($err); ?><br>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="notice">
                    <strong><?php echo htmlspecialchars($success); ?></strong>
                </div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Add Course</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="manage-courses.php">
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="subject_code">Subject Code</label>
                                    <input type="text" id="subject_code" name="subject_code" placeholder="e.g. CC106" required>
                                </div>

                                <div class="form-field">
                                    <label for="subject_name">Subject Name</label>
                                    <input type="text" id="subject_name" name="subject_name" placeholder="e.g. Mobile Application Development" required>
                                </div>

                                <div class="form-field">
                                    <label for="units">Units</label>
                                    <input type="number" id="units" name="units" min="1" max="6" value="3" required>
                                </div>

                                <div class="form-field">
                                    <label for="department">Department</label>
                                    <input type="text" id="department" name="department" placeholder="e.g. College of Computer Studies">
                                </div>

                                <div class="form-field">
                                    <label for="program">Program</label>
                                    <input type="text" id="program" name="program" list="existing-programs" placeholder="e.g. BSCS" required>
                                    <datalist id="existing-programs">
                                        <?php foreach ($programs as $p): ?>
                                        <option value="<?php echo htmlspecialchars($p['program']); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                            </div>

                            <button type="submit">Add Course</button>
                        </form>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Course Catalog</h3>
                    </div>
                    <div class="panel-body">
                        <div class="data-grid cols-5">
                            <div class="data-grid-header">
                                <span>Program</span>
                                <span>Subject Code</span>
                                <span>Subject Name</span>
                                <span>Units</span>
                                <span>Department</span>
                            </div>
                            <?php if (empty($allSubjects)): ?>
                            <div class="data-grid-row"><span>No courses on record.</span></div>
                            <?php else: foreach ($allSubjects as $subj): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($subj['program']); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_code']); ?></span>
                                <span><?php echo htmlspecialchars($subj['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($subj['units']); ?></span>
                                <span><?php echo htmlspecialchars($subj['department']); ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
