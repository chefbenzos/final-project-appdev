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

    $teachers = getAllTeachers($conn);
    $subjects = getAllSubjects($conn);

    $errors = [];
    $success = null;

    // Context fields carried across both the "find slots" and "assign" submissions.
    $teacherId  = trim($_POST['teacher_id'] ?? '');
    $subjectId  = trim($_POST['subject_id'] ?? '');
    $section    = trim($_POST['section'] ?? '');
    $semester   = trim($_POST['semester'] ?? '1st Semester');
    $schoolYear = trim($_POST['school_year'] ?? date('Y'));

    $availableSlots = [];
    $hasContext = ($teacherId !== '' && $subjectId !== '' && $section !== '');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasContext) {

        // Assigning the chosen slot
        if (($_POST['action'] ?? '') === 'assign') {
            $chosen = $_POST['time_slot'] ?? '';
            $parts = explode('|', $chosen);

            if (count($parts) !== 4) {
                $errors[] = "Please select a time slot before assigning the course.";
            } else {
                [$day, $timeStart, $timeEnd, $room] = $parts;

                // Re-check availability at the moment of submission to avoid a race condition
                // with another admin assigning the same slot in the meantime.
                $stillFree = isRoomAvailable($conn, $room, $day, $timeStart, $timeEnd)
                    && isTeacherAvailable($conn, (int) $teacherId, $day, $timeStart, $timeEnd)
                    && isSectionAvailable($conn, $section, $day, $timeStart, $timeEnd);

                if (!$stillFree) {
                    $errors[] = "That time slot was just taken. Please pick another available slot.";
                } else {
                    $result = addSchedule($conn, [
                        'subject_id'  => (int) $subjectId,
                        'teacher_id'  => (int) $teacherId,
                        'section'     => $section,
                        'day'         => $day,
                        'time_start'  => $timeStart,
                        'time_end'    => $timeEnd,
                        'room'        => $room,
                        'semester'    => $semester,
                        'school_year' => (int) $schoolYear,
                    ]);

                    if ($result['success']) {
                        $success = "Assigned $day, " . substr($timeStart, 0, 5) . "–" . substr($timeEnd, 0, 5) . " in $room.";
                    } else {
                        $errors[] = "Error assigning course: " . $result['error'];
                    }
                }
            }
        }

        // Recompute available slots for this teacher + section, whether we just found them
        // or just assigned one (so the admin can immediately add another meeting day).
        $availableSlots = getAvailableSlots($conn, (int) $teacherId, $section);
    }

    // Look up display labels for the teacher/subject currently selected, for a friendlier header.
    $selectedTeacherName = '';
    foreach ($teachers as $t) {
        if ((int) $t['teacher_id'] === (int) $teacherId) {
            $selectedTeacherName = $t['first_name'] . ' ' . $t['last_name'];
            break;
        }
    }
    $selectedSubjectLabel = '';
    foreach ($subjects as $s) {
        if ((int) $s['subject_id'] === (int) $subjectId) {
            $selectedSubjectLabel = $s['subject_code'] . ' - ' . $s['subject_name'];
            break;
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
    <title>Assign Courses to Teacher</title>
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
                <a href="main-dashboard-admin.php">Admin Profile</a>
                <a href="add-users.php">Add Teacher / Student</a>
                <a href="manage-courses.php">Manage Courses</a>
                <a class="active" href="assign-courses.php">Assign Courses to Teacher</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="logout-row"><a href="<?=ROOT?>/logout.php">Log out</a></div>
            <div class="page-shell">
                <header class="topbar">
                    <h2>Assign Courses to Teacher</h2>
                    <p>Assign a subject and section to a teacher. Only time slots free of teacher, section, and room conflicts are offered.</p>
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
                    <strong>Course assigned successfully.</strong><br>
                    <?php echo htmlspecialchars($selectedSubjectLabel); ?> — <?php echo htmlspecialchars($section); ?> with <?php echo htmlspecialchars($selectedTeacherName); ?><br>
                    <?php echo htmlspecialchars($success); ?><br>
                    Need another meeting day for the same class? Pick a new slot below and assign again.
                </div>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel-header">
                        <h3>Step 1: Course Details</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="assign-courses.php">
                            <input type="hidden" name="action" value="find_slots">
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="teacher_id">Teacher</label>
                                    <select id="teacher_id" name="teacher_id" required>
                                        <option value="">-- Select a Teacher --</option>
                                        <?php foreach ($teachers as $t): ?>
                                        <option value="<?php echo (int) $t['teacher_id']; ?>" <?php echo ((int) $teacherId === (int) $t['teacher_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name'] . ' (' . $t['department'] . ')'); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="subject_id">Subject</label>
                                    <select id="subject_id" name="subject_id" required>
                                        <option value="">-- Select a Subject --</option>
                                        <?php foreach ($subjects as $s): ?>
                                        <option value="<?php echo (int) $s['subject_id']; ?>" <?php echo ((int) $subjectId === (int) $s['subject_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($s['subject_code'] . ' - ' . $s['subject_name'] . ' (' . $s['program'] . ')'); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="section">Section</label>
                                    <input type="text" id="section" name="section" placeholder="e.g. BSCS-1A" value="<?php echo htmlspecialchars($section); ?>" required>
                                </div>

                                <div class="form-field">
                                    <label for="semester">Semester</label>
                                    <select id="semester" name="semester">
                                        <option <?php echo $semester === '1st Semester' ? 'selected' : ''; ?>>1st Semester</option>
                                        <option <?php echo $semester === '2nd Semester' ? 'selected' : ''; ?>>2nd Semester</option>
                                        <option <?php echo $semester === 'Summer' ? 'selected' : ''; ?>>Summer</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="school_year">School Year</label>
                                    <input type="number" id="school_year" name="school_year" value="<?php echo htmlspecialchars($schoolYear); ?>" required>
                                </div>
                            </div>

                            <button type="submit">Find Available Slots</button>
                        </form>
                    </div>
                </section>

                <?php if ($hasContext): ?>
                <section class="panel">
                    <div class="panel-header">
                        <h3>Step 2: Choose an Open Time Slot</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($availableSlots)): ?>
                        <div class="notice">No open slots for this teacher and section combination. Try a different section, teacher, or add more rooms.</div>
                        <?php else: ?>
                        <form method="post" action="assign-courses.php">
                            <input type="hidden" name="action" value="assign">
                            <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacherId); ?>">
                            <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subjectId); ?>">
                            <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
                            <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                            <input type="hidden" name="school_year" value="<?php echo htmlspecialchars($schoolYear); ?>">

                            <div class="form-field">
                                <label for="time_slot">Available Time Slot</label>
                                <select id="time_slot" name="time_slot" required>
                                    <option value="">-- Select an Available Slot --</option>
                                    <?php foreach ($availableSlots as $slot): ?>
                                    <?php
                                        $value = $slot['day'] . '|' . $slot['time_start'] . '|' . $slot['time_end'] . '|' . $slot['room'];
                                        $label = $slot['day'] . ' • ' . substr($slot['time_start'], 0, 5) . '–' . substr($slot['time_end'], 0, 5) . ' • ' . $slot['room'];
                                    ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit">Assign Course</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>
            <?php require ROOT_PATH . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
