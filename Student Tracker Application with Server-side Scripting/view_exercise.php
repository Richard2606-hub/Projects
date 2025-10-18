<?php
include("authentication.php");
require('database.php');
$user_id = $_SESSION["user_id"];
$query1 = "SELECT * FROM exercises_history WHERE user_id =" . $user_id;
$result1 = mysqli_query($con, $query1);
$history = [];
while ($row = mysqli_fetch_assoc($result1)) {
    $history[] = $row['exercise_id'];
}
if(!empty($history)) {
    $historyChecker = "<a href=\"view_history.php\">View Exercise History</a>";
}else{
    $historyChecker = "";
}

$query2 = "SELECT * FROM exercises WHERE user_id =" . $user_id;
$result2 = mysqli_query($con, $query2);
$exercise = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $exercise[] = $row['exercise_id'];
}
if(!empty($exercise)) {
    $exerciseChecker = "<a href=\"view_daily.php\">Exercise Today</a>";
}else{
    $exerciseChecker = "";
}

$search = $_GET['search'] ?? '';
$min_duration = $_GET['min_duration'] ?? '';
$max_duration = $_GET['max_duration'] ?? '';
$caloriesBurn = $_GET['caloriesBurn'] ?? '';

$sel_query = "SELECT * FROM exercises WHERE user_id='$user_id' AND 1=1";

if (!empty($search)) {
    $sel_query .= " AND exerciseName LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
}
if (!empty($caloriesBurn) && is_numeric($caloriesBurn)) {
    $sel_query .= " AND caloriesBurn >= '$caloriesBurn'";
}

if (!empty($min_duration) && is_numeric($min_duration) && !empty($max_duration) && is_numeric($max_duration)) {
    $min_time = gmdate("H:i:s", $min_duration * 60);
    $max_time = gmdate("H:i:s", $max_duration * 60);
    $sel_query .= " AND duration >= '$min_time' AND duration <= '$max_time'";
} else if (!empty($max_duration) && is_numeric($max_duration) && empty($min_duration)) {
    $max_time = gmdate("H:i:s", $max_duration * 60);
    $sel_query .= " AND duration <= '$max_time'";
} else if (!empty($min_duration) && is_numeric($min_duration) && empty($max_duration)) {
    $min_time = gmdate("H:i:s", $min_duration * 60);
    $sel_query .= " AND duration >= '$min_time'";
}

$sel_query .= " ORDER BY exercise_id DESC";
$result = mysqli_query($con, $sel_query);
$noExe = false;
if(mysqli_num_rows($result) <= 0) {
    $noExe = true;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - View Daily Exercise list</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
        <div>
            <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
        </div>
        <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
        <br>
        <div class="search-filter">
            <form method="GET" action="view_exercise.php">
                <div>
                    <label>Exercise Name:</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search exercise name">
                </div>
                <div>
                    <label>Min Duration (minutes):</label>
                    <input type="number" name="min_duration" value="<?php echo htmlspecialchars($min_duration); ?>" step="1" min="0" placeholder="e.g. 30">
                </div>
                <div>
                    <label>Max Duration (minutes):</label>
                    <input type="number" name="max_duration" value="<?php echo htmlspecialchars($max_duration); ?>" step="1" min="0" placeholder="e.g. 60">
                </div>
                <div>
                    <label>Min Calorie Burn:</label>
                    <input type="number" name="caloriesBurn" value="<?php echo htmlspecialchars($caloriesBurn); ?>" step="1" min="0" placeholder="e.g. 200">
                </div>
                <div>
                    <button type="submit">Search & Filter</button>
                </div>
                <div>
                    <button type="button" onclick="window.location.href='view_exercise.php'">Reset</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h1 class="page-title">Daily Exercise List</h1>
                <strong>Today's Date: <?php echo date("Y-m-d"); ?></strong>
            
            <table>
                <thead>
                    <tr>
                        <th><strong>No.</strong></th>
                        <th><strong>Exercise Name</strong></th>
                        <th><strong>Duration</strong></th>
                        <th><strong>Calorie Burn</strong></th>
                        <th><strong>Actions</strong></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $count = 1;
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?php echo htmlspecialchars($row["exerciseName"]); ?></td>
                                <td><span class="duration-display"><?php echo $row["duration"]; ?></span></td>
                                <td><?php echo $row["caloriesBurn"]; ?> cal</td>
                                <td>
                                    <a href="edit_exercise.php?exercise_id=<?php echo $row["exercise_id"]; ?>">Edit</a>
                                    <a href="delete_exercise.php?exercise_id=<?php echo $row["exercise_id"]; ?>"
                                        onclick="return confirm('Are you sure you want to delete this Exercise?')">Delete</a>
                                </td>
                            </tr>
                            <?php 
                            $count++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                <?php if ($noExe): ?>
                                    No exercises found. <a href="add_exercise.php">Add your first exercise</a>!
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <div class="nav-links">
                <a href="index.php">Homepage</a>
                <?php echo $exerciseChecker; ?>
                <a href="add_exercise.php">Insert New Exercise</a>
                <?php echo $historyChecker; ?>
            </div>
        </div>
    </div>
</body>

</html>