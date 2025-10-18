<?php
require('database.php');
$exercise_id = $_REQUEST['exercise_id'];
$query = "SELECT * FROM exercises where exercise_id='" . $exercise_id . "'";
$result = mysqli_query($con, $query) or die(mysqli_error($con));
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - Edit Exercise</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
        <div class="form-container">
            <h1 class="page-title">Edit Exercise</h1>
            <?php
            $status = "";
            if (isset($_POST['submit']) && $_POST['submit'] == "Update") {
                $exerciseName = $_REQUEST['exerciseName'];
                $preDuration = $_REQUEST['duration'];
                $duration = gmdate("H:i:s", $preDuration * 60);
                $caloriesBurn = $_REQUEST['caloriesBurn'];
                $update = "UPDATE exercises set exerciseName='" . $exerciseName . "', duration='" . $duration . "', caloriesBurn='" . $caloriesBurn . "' where exercise_id='" . $exercise_id . "'";
                mysqli_query($con, $update) or die(mysqli_error($con));
                $status = "Exercise Updated Successfully.   <a href='view_exercise.php'>View</a>";
                echo "<div class=\"status-message\">" . $status ."</div>";
            }else if (isset($_POST['submit']) && $_POST['submit'] == "Back"){
                header("Location: view_exercise.php");
                exit();
            }else{
                $durationParts = explode(':', $row['duration']);
                $durationInMinutes = ($durationParts[0] * 60) + $durationParts[1];
                ?>
                <form name="form" method="post" action="" class="exercise-form">
                    <input name="exercise_id" type="hidden" value="<?php echo $row['exercise_id']; ?>" />
                    <p><input type="text" name="exerciseName" placeholder="Edit Exercise Name" required value="<?php echo $row['exerciseName']; ?>" /></p>
                    <p><input type="number" name="duration" placeholder="Edit Duration (in minutes)" required value="<?php echo $durationInMinutes; ?>" /> Minutes </p>
                    <p><input type="number" name="caloriesBurn" placeholder="Edit Calorie Burn" required value="<?php echo $row['caloriesBurn']; ?>" /></p>
                    <p><input name="submit" type="submit" value="Update" /> <input name="submit" type="submit" value="Back" /></p>
                </form>
                <?php } ?>
        </div>
    </div>
</body>

</html>