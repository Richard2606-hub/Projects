<?php
include("authentication.php");
require('database.php');

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

if (isset($_GET['id'])) {
    $history_id = intval($_GET['id']);
    
    $check_result = mysqli_query($con, "SELECT date FROM habit_history WHERE id='$history_id' AND user_id='$user_id'");
    $check_row = mysqli_fetch_assoc($check_result);
    
    if (!$check_row) {
        header("Location: habit_history.php?msg=Record+not+found");
        exit();
    }
    
    if ($check_row['date'] === $today) {
        header("Location: habit_history.php?msg=Cannot+delete+today's+record.+Use+Today's+Habits+page");
        exit();
    }
    
    $delete = mysqli_query($con, "DELETE FROM habit_history WHERE id='$history_id' AND user_id='$user_id'");

    if ($delete) {
        header("Location: habit_history.php?msg=History+deleted");
        exit;
    } else {
        header("Location: habit_history.php?msg=Error+deleting+history");
        exit;
    }
} else {
    header("Location: habit_history.php?msg=Invalid+request");
    exit;
}
?>