<?php
    session_start();
    require_once __DIR__ . '/config.php';
    session_destroy();

    header("Location: " . ROOT . "/index.php?msg=You have successfully logged out.");
    exit();
?>
