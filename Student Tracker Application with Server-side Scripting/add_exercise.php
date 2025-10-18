<?php
include("authentication.php");
require('database.php');
$status = "";
if (isset($_POST['new']) && $_POST['new'] == 1) {
    $user_id = $_SESSION['user_id'];
    $exerciseName = $_REQUEST['exerciseName'];
    $preDuration = $_REQUEST['duration'];
    $duration = gmdate("H:i:s", $preDuration * 60);
    $caloriesBurn = $_REQUEST['caloriesBurn'];
    $dateRegister = date("Y-m-d");
    $incQuery = "INSERT into exercises
    (`user_id`, `exerciseName`,`duration`,`caloriesBurn`,`dateRegister`) VALUES 
    ('$user_id','$exerciseName','$duration','$caloriesBurn','$dateRegister')";
    mysqli_query($con, $incQuery)
        or die(mysqli_error($con));
    $status = "New Exercise Inserted Successfully.      <a href='view_exercise.php'>View</a>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - Insert New Exercise</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <div>
        <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
    </div>
    <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
<br>
    <div class="form-container">
        <h1 class="page-title">Insert New Exercise</h1>
        <form name="form" method="post" action="" class="exercise-form">
            <input type="hidden" name="new" value="1" />
            <label>Exercise Name:</label>
            <p><input type="text" name="exerciseName" placeholder="Enter Exercise Name" required /></p>
            <label>Duration:</label>
            <p><input type="number" name="duration" step="1" min="0" placeholder="Enter Duration (in minutes)"
                    required /> Minutes</p>
            <label>Calorie Burn:</label>
            <p><input type="number" name="caloriesBurn" placeholder="Enter Calorie Burn" required /> </p>
            <p><input name="submit" type="submit" value="Submit" /> <input name="submit" type="submit" value="Back"
                    onclick="window.location.href='view_exercise.php'" /></p>
        </form>

        <?php if ($status): ?>
            <div class="status-message"><?php echo $status; ?></div>
        <?php endif; ?>
    </div>
    </div>
</body>

</html>