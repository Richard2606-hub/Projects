<?php
$max_attempts = 3;

$current_attempt = 0;
$connected = false;

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'stu_track_db';

while ($current_attempt < $max_attempts && !$connected) {
    $current_attempt++;

    $con = mysqli_connect('localhost', 'root', '', 'stu_track_db');

    if (!$con) {
        sleep(1);
    }
    else {
        $connected = true;
    }
}

if (!$connected) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Check connection
if (mysqli_connect_errno()) {
    // Log error to database (error_logs table)
    $error_code = mysqli_connect_errno();
    $error_message = mysqli_connect_error();
    $error_time = date("Y-m-d H:i:s");

    // Try inserting the error into error_logs if possible
    if ($con) {
        $log_query = "INSERT INTO error_logs (error_code, error_message, error_time) 
                      VALUES ('$error_code', '" . mysqli_real_escape_string($con, $error_message) . "', '$error_time')";
        mysqli_query($con, $log_query);
    }

    // Show user-friendly message
    die("⚠️ Sorry, we are experiencing technical difficulties connecting to the database. Please try again later.");
}
?>