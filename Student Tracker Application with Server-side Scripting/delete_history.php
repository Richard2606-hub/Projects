<?php
require('database.php');
$history_id = $_GET['history_id'];
$query = "DELETE FROM exercises_history WHERE history_id=$history_id";
$result = mysqli_query($con, $query) or die(mysqli_error($con));
header("Location: view_history.php");
exit();
?>