<?php
include("authentication.php");
require('database.php');
$exercise_id = $_GET['exercise_id'];
$query1 = "SELECT *
FROM `exercises`
WHERE exercise_id='$exercise_id'
;";
$result = mysqli_query($con, $query1) or die(mysqli_error($con));
$rows = mysqli_num_rows($result);
if ($rows == 1) {
    $exercise = mysqli_fetch_assoc($result);
    $user_id = $_SESSION["user_id"];
    $exerciseName = $exercise["exerciseName"];
    $duration = $exercise["duration"];
    $caloriesBurn = $exercise["caloriesBurn"];
    $dateRegister = date("Y-m-d");

    $query3 = "INSERT into exercises_history 
    (`exercise_id`, `user_id`, `exerciseName`, `duration`, `caloriesBurn`, `dateRegister`) VALUES 
    ('$exercise_id', '$user_id', '$exerciseName', '$duration', '$caloriesBurn', '$dateRegister')";
    if (mysqli_query($con, $query3)) {
        echo "Insert successful. Rows inserted: " . mysqli_affected_rows($con);
        header("Location: view_daily.php");
        exit();
    } else {
        echo "Insert failed: Query 3 - " . mysqli_error($con);
    }
}
?>