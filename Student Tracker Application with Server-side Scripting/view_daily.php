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

$query2 = "SELECT exercise_id FROM exercises WHERE user_id='$user_id'";
$result2 = mysqli_query($con, $query2);
$exercise_ids1 = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $exercise_ids1[] = $row['exercise_id'];
}

$exercise_ids2 = [];
if (!empty($exercise_ids1)) {
    $query3 = 'SELECT exercise_id FROM exercises_history WHERE user_id=' . $user_id . ' AND exercise_id IN (' . implode(',', $exercise_ids1) . ') AND dateRegister = CURDATE()';
    $result3 = mysqli_query($con, $query3);
    while ($row = mysqli_fetch_assoc($result3)) {
        $exercise_ids2[] = $row['exercise_id'];
    }
}

$exercise_ids = array_diff($exercise_ids1, $exercise_ids2);

if (!empty($exercise_ids)) {
    $sel_query = "SELECT * FROM exercises WHERE user_id='$user_id' AND exercise_id IN (" . implode(',', $exercise_ids) . ")";
} else {
    $sel_query = "SELECT * FROM exercises WHERE 1=0";
}

$sel_query .= " ORDER BY exercise_id DESC";
$result = mysqli_query($con, $sel_query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - View Exercise Today</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <div>
        <span style="color:floralwhite; font-size: 30px;">Focus</span><span
            style="color:darkgreen; font-size: 30px;">Track</span>
    </div>
    <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
    <br>
    <div class="table-container">
        <h1 class="page-title">Exercise Today</h1>
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
                                <a href="add_history.php?exercise_id=<?php echo $row["exercise_id"]; ?>">Accomplished</a>
                            </td>
                        </tr>
                        <?php
                        $count++;
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                            <?php if (empty($exercise_ids1)): ?>
                                No exercises found. <a href="add_exercise.php">Add your first exercise</a>!
                            <?php else: ?>
                                All exercises completed for today! Great job! 🎉
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
            <a href="index.php">Dashboard</a>
            <a href="view_exercise.php">View Exercises List</a>
            <?php echo $historyChecker; ?>
        </div>
    </div>
    </div>
</body>

</html>