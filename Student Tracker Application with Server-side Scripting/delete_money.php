<?php
require "database.php";

$id = $_GET['id'];
mysqli_query($con, "DELETE FROM transaction WHERE transaction_id = '$id'") or die(mysqli_error($con));

header("Location: money_summary.php");
exit();
?>