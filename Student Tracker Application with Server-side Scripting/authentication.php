<?php
session_start();

$timeout = 60 * 30; //30 minutes

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['last_request_time']) || (time() - $_SESSION['last_request_time']) > $timeout) {
    session_unset();
    session_destroy();

    header("Location: login.php?timeout=1");
    exit();
}

$_SESSION['last_request_time'] = time();
?>