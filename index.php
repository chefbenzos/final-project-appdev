<?php
    require 'config.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=ROOT?>/stylesheets/landing-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="<?=ROOT?>/javascript/password-verify.js" defer></script>

    <title>Student Portal</title>
</head>
<body>
    <?php
        session_start();
        
        if(isset($_SESSION['email'])) {
            if ($_SESSION['user_type'] === 'student') {
                header("Location:".ROOT."/php-scripts/main-dashboard-student.php");
            } elseif ($_SESSION['user_type'] === 'registrar') {
                header("Location: ".ROOT."/php-scripts/main-dashboard-registrar.php");
            } elseif ($_SESSION['user_type'] === 'teacher') {
                header("Location: ".ROOT."/php-scripts/main-dashboard-teacher.php");
            }
        }
    ?>

    <div class="center-items">
        <div class="login-header">
            <form method="post" action="<?=ROOT?>/php-scripts/login-process.php">
                <div class="logo-holder">
                    <img src="<?=ROOT?>/images/PHP.png" alt="Logo here ideally">
                </div>

                <div class="center-items">
                    <h1>Student Portal</h1>
                    <sub>by Piosang Home Page</sub>
                </div>
                    
                <div class="input-fields">
                    <label><span class="required">*</span> Email</label>
                    <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" placeholder="Enter your email ..." required>
                </div>

                <div class="input-fields">
                    <label><span class="required">*</span> Password</label>
                    <input type="password" name="password" id="password" style="width: 88%;" placeholder="Enter password ..." required>
                    <i class="fa-solid fa-eye" id="toggle-password"></i>
                </div>

                <div class="center-items">
                    <input type="submit" name="login" value="Login">
                </div>
            </form>

            <?php
                if(isset($_GET["msg"])){
                    echo "<div id='message'>";
                    echo $_GET["msg"];
                    echo "</div>";
                }
            ?>
        </div>
    </div>
</body>
</html>
