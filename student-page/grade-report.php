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

    $studentId = $_SESSION['user_id'];

    // Grade viewing is locked while there's an outstanding balance, matching the notice already in the page.
    $balance = getStudentBalance($conn, $studentId);
    $gradesLocked = $balance > 0;

    $selectedTermYear = $_GET['term_school_year'] ?? '';
    $semester = null;
    $schoolYear = null;
    if ($selectedTermYear !== '') {
        // Format from the dropdown is "{semester_number}-{school_year}", e.g. "1-20252026"
        [$semNum, $yearPart] = array_pad(explode('-', $selectedTermYear, 2), 2, null);
        $semester = $semNum . 'st Semester';
        $schoolYear = $yearPart ? (int) substr($yearPart, 0, 4) : null;
    }

    $grades = $gradesLocked ? [] : viewGrades($conn, $studentId, $semester, $schoolYear);

    // Build the term dropdown from whatever semesters/years actually appear in the student's grades
    $allGrades = viewGrades($conn, $studentId);
    $termOptions = [];
    foreach ($allGrades as $g) {
        $semNum = (strpos($g['semester'], '1') !== false) ? '1' : '2';
        $key = $semNum . '-' . $g['school_year'] . ($g['school_year'] + 1);
        $termOptions[$key] = $g['semester'] . ' - ' . $g['school_year'] . '/' . ($g['school_year'] + 1);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/dashboard.css">
    <script src="<?=ROOT?>/javascript/dashboard-sidebar.js" defer></script>
    <title>Grade Report</title>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true">&#9776;</button>

            <div class="portal-brand">
                <div class="sidebar-logo">
                    <img src="<?=ROOT?>/image/BHP_Icon.png" alt="Logo">
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
                <a class="active" href="grade-report.php">Grade Report</a>
                <a href="program-curriculum.php">Program Curriculum</a>
                <a href="schedule-assessment.php">Schedule and Assessment</a>
                <a href="request-documents.php">Request Documents</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Grade Report</h2>
                    <p>Registered schedule and grades.</p>
                </header>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Grades</h3>
                    </div>
                    <div class="panel-body">
                        <form class="grade-filter-form" method="get" action="grade-report.php">
                            <label for="term-school-year">Term - School Year:</label>
                            <div class="grade-filter-controls">
                                <select id="term-school-year" name="term_school_year">
                                    <option value="">-- All Terms --</option>
                                    <?php foreach ($termOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $value === $selectedTermYear ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">View Grades</button>
                            </div>
                        </form>
                        <?php if ($gradesLocked): ?>
                        <div class="notice">Grade viewing is locked while the account balance is below the required threshold.</div>
                        <?php endif; ?>
                        <div class="data-grid cols-4">
                            <div class="data-grid-header">
                                <span>Subject</span>
                                <span>Units</span>
                                <span>Grade</span>
                                <span>Remarks</span>
                            </div>
                            <?php if ($gradesLocked): ?>
                            <!-- Grades intentionally not rendered while locked -->
                            <?php elseif (empty($grades)): ?>
                            <div class="data-grid-row">
                                <span>No grades found for the selected term.</span>
                            </div>
                            <?php else: foreach ($grades as $g): ?>
                            <div class="data-grid-row">
                                <span><?php echo htmlspecialchars($g['subject_code'] . ' - ' . $g['subject_name']); ?></span>
                                <span><?php echo htmlspecialchars($g['units']); ?></span>
                                <span><?php echo htmlspecialchars($g['final_grade']); ?></span>
                                <span><?php echo htmlspecialchars($g['remarks']); ?></span>
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
