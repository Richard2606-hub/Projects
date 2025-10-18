<?php
include("authentication.php");
require('database.php');

if (isset($_GET['id'])) { 
    $habit_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $delete = mysqli_query($con, "DELETE FROM habit WHERE habit_id='$habit_id' AND user_id='$user_id'");

    if ($delete) {
        header("Location: habit_list.php?");
        exit;
    } else {
        echo "Error deleting habit.";
    }
} else {
    echo "Invalid request.";
}
?>
