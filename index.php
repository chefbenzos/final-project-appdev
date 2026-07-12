<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/final-project-appdev/stylesheets/landing-page.css">
    <script src="/final-project-appdev/javascript/password-verify.js"></script>

    <title>Student Portal</title>
</head>
<body>
    <?php
        session_start();
        
        if(isset($_SESSION['user_id'])) {
            header("Location: /final-project-appdev/php-scripts/dashboard.php");
            exit();
        }
    ?>

    <div class="center-items">
        <div class="login-header">
            <form method="post" action="/final-project-appdev/php-scripts/login-process.php">
                <div class="logo-holder">
                    <img src="images\PHP.png" alt="Logo here ideally">
                </div>

                <div class="center-items">
                    <h1>Student Portal</h1>
                    <sub>by Piosang Home Page</sub>
                </div>
                    
                <div class="input-fields">
                    <label><span class="required">*</span> Email</label>
                    <input type="text" name="email" placeholder="email" required>
                </div>

                <div class="input-fields">
                    <label><span class="required">*</span> Password</label>
                    <input type="password" name="password" placeholder="password" required>
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
