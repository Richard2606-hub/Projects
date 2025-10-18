<?php
require('database.php');
$exercise_id = $_GET['exercise_id'];
$query = "DELETE FROM exercises WHERE exercise_id=$exercise_id";
$result = mysqli_query($con, $query) or die(mysqli_error($con));
header("Location: view_exercise.php");
exit();
?>