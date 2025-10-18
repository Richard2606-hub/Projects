<?php
require('database.php');
$history_id = $_REQUEST['history_id'];
$query = "SELECT * FROM exercises_history where history_id='" . $history_id . "'";
$result = mysqli_query($con, $query) or die(mysqli_error($con));
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - Edit Exercise History</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
        <div class="form-container">
            <h1 class="page-title">Edit Exercise History</h1>
            <?php
            $status = "";
            if (isset($_POST['submit']) && $_POST['submit'] == "Update") {
                $preDuration = $_REQUEST['duration'];
                $duration = gmdate("H:i:s", $preDuration * 60);
                $exerciseName = $_REQUEST['exerciseName'];
                $caloriesBurn = $_REQUEST['caloriesBurn'];
                $update = "UPDATE exercises_history set exerciseName='" . $exerciseName . "', duration='" . $duration . "', caloriesBurn='" . $caloriesBurn . "' where history_id='" . $history_id . "'";
                mysqli_query($con, $update) or die(mysqli_error($con));
                $status = "Exercise History Updated Successfully.   <a href='view_history.php'>View</a>";
                echo "<div class=\"status-message\">" . $status ."</div>";
            } else if (isset($_POST['submit']) && $_POST['submit'] == "Back") {
                header("Location: view_history.php");
                exit();
            } else {
                $durationParts = explode(':', $row['duration']);
                $durationInMinutes = ($durationParts[0] * 60) + $durationParts[1];
                ?>
                <form name="form" method="post" action="" class="exercise-form">
                    <input name="history_id" type="hidden" value="<?php echo $row['history_id']; ?>" />
                    <p>
                        <input type="text" name="exerciseName" placeholder="Edit Exercise Name" required value="<?php echo $row['exerciseName']; ?>" />
                    </p>
                    <p>
                        <input type="number" name="duration" placeholder="Edit Duration (in minutes)" required value="<?php echo $durationInMinutes; ?>" />
                        <span>Minutes</span>
                    </p>
                    <p>
                        <input type="number" name="caloriesBurn" placeholder="Edit Calorie Burn" required value="<?php echo $row['caloriesBurn']; ?>" />
                    </p>
                    <p>
                    <p><input name="submit" type="submit" value="Update" /> <input name="submit" type="submit" value="Back" /></p>
                    </p>
                </form>
            <?php } ?>
        </div>
    </div>
</body>

</html>