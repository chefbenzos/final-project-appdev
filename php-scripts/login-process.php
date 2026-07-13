<?php
    session_start();
    require_once __DIR__ . '/../config.php';
    require ROOT_PATH .'/database/db.php';

    if (!isset($_POST['login'])) {
        header("Location: " . ROOT . "/index.php?msg=Please login first.");
        exit();
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Query all tables for the user with the given email
    $sql = "SELECT students.institute_email, students.password_hash, 'student' as user_type FROM students WHERE students.institute_email = '$email' 
            UNION ALL
            SELECT registrars.email, registrars.password_hash, 'registrar' as user_type FROM registrars WHERE registrars.email = '$email' 
            UNION ALL
            SELECT teachers.email, teachers.password_hash, 'teacher' as user_type FROM teachers WHERE teachers.email = '$email'";
    
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) != 1) {
        header("Location:" . ROOT . "/index.php?msg=Invalid email or password.");
        exit();
    } 

    $row = mysqli_fetch_assoc($result);

    if (!password_verify($password, $row['password_hash'])) {
        header("Location:" . ROOT . "/index.php?msg=Invalid password.&email=" . urlencode($email));
        exit();
    }

    $user_type = $row['user_type'];
    $access_level = '';

    // Unset variables for reuse
    unset($row);
    unset($result);

    $user_info_sql = "";

    if ($user_type === 'student') {
        $user_info_sql = "SELECT * FROM students WHERE institute_email = '$email'";
    } elseif ($user_type === 'registrar') {
        $user_info_sql = "SELECT * FROM registrars WHERE email = '$email'";
    } elseif ($user_type === 'teacher') {
        $user_info_sql = "SELECT * FROM teachers WHERE email = '$email'";
    }

    $result = mysqli_query($conn, $user_info_sql);
    $row = mysqli_fetch_assoc($result);

    $_SESSION['user_type'] = $user_type;
    $_SESSION['user_id'] = $row["{$user_type}_id"];
    $_SESSION['email'] = $row['institute_email'] ?? $row['email'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];

    if ($user_type === 'student') { //Sets necessary information for specific users.
        $access_level = "Location: " . ROOT . "/student-page/academic-holds.php";

        $_SESSION['middle_initial'] = $row['middle_initial'] ?? '';
        $_SESSION['personal_email'] = $row['personal_email'];
        $_SESSION['curriculum_code'] = $row['curriculum_code'];
        $_SESSION['year_level'] = $row['year_level'];
        $_SESSION['registration_status'] = $row['registration_status'];
        $_SESSION['student_type'] = $row['student_type'];
        $_SESSION['scholarship'] = $row['scholarship'];

    } elseif ($user_type === 'registrar') {
        $access_level = "Location: " . ROOT . "/registrar-page/main-dashboard-registrar.php";

    } elseif ($user_type === 'teacher') {
        $access_level = "Location: " . ROOT . "/teacher-page/main-dashboard-teacher.php";

        $_SESSION['department'] = $row['department'] ?? '';
    }

    header($access_level);
?>