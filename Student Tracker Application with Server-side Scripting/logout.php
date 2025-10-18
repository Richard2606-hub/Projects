<?php
require "database.php";

session_start();

$uid = $_SESSION['user_id'];
$query = "UPDATE users SET remember_token = NULL WHERE user_id = '$uid'";
mysqli_query($con, $query) or die(mysqli_error($con));

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

$cookie_name = "remember_token";
setcookie($cookie_name, "", time() - 3600, "/");

header("Location: login.php");
exit();
?>