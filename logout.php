<?php
    session_start();
    session_destroy();

    header("Location: index.php?msg=You have successfully logged out.");
    exit();
?>