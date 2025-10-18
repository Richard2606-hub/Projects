<?php
include("authentication.php");
require('database.php');

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$sql = "SELECT habit_id, habit_name 
        FROM habit 
        WHERE user_id = '$user_id' 
          AND (is_daily = 1 OR specific_date = '$today')";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $habit_id = $row['habit_id'];
    $habit_name = mysqli_real_escape_string($con, $row['habit_name']);

    $check = mysqli_query($con, "SELECT 1 FROM habit_history 
                                 WHERE user_id='$user_id' 
                                   AND habit_id='$habit_id' 
                                   AND date='$today'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "INSERT INTO habit_history 
            (habit_id, habit_name, user_id, is_done, date) 
            VALUES ('$habit_id', '$habit_name', '$user_id', 0, '$today')");
    }
}

if (isset($_POST["save_remark"])) {
    $habit_id = intval($_POST["habit_id"]);
    $remark = mysqli_real_escape_string($con, $_POST['habit_remark']);

    mysqli_query($con, "UPDATE habit_history 
                        SET habit_remark='$remark' 
                        WHERE user_id='$user_id' 
                          AND habit_id='$habit_id' 
                          AND date='$today'");
}

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $habit_id = intval($_GET['id']);
    $toggle = intval($_GET['toggle']);

    mysqli_query($con, "UPDATE habit_history 
                        SET is_done='$toggle' 
                        WHERE user_id='$user_id' 
                          AND habit_id='$habit_id' 
                          AND date='$today'");

    header("Location: habit_today.php");
    exit;
}

$query = "SELECT h.habit_id, hh.habit_name, hh.is_done, hh.habit_remark
          FROM habit h
          JOIN habit_history hh 
            ON h.habit_id = hh.habit_id 
           AND hh.date = '$today'
          WHERE h.user_id = '$user_id'
            AND (h.is_daily = 1 OR h.specific_date = '$today')";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Today's Habits</title>
    <link rel="stylesheet" href="habit-tracker-styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <div>
        <span style="color:floralwhite; font-size: 30px;">Focus</span><span
            style="color:darkgreen; font-size: 30px;">Track</span>
    </div>
    <div class="container">
        <h1
            style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">
            Habit Tracker</h1>
        <div class="card">

            <h1>Today's Habits</h1>
            <p style="text-align:left;"><?php echo date('F j, Y'); ?></p><br><hr>
            <?php if (mysqli_num_rows($result) > 0) { ?>

                <table>
                    <colgroup>
                        <col style="width: 25%;">
                        <col style="width: 35%;">
                        <col style="width: 15%;">
                        <col style="width: 25%;">
                    </colgroup>
                    <tr>
                        <th>Habit Name</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['habit_name']); ?></td>
                            <td>
                                <form method="post" action="habit_today.php"
                                    style="display: inline-block; margin: 0; padding: 0; background: none; box-shadow: none;">
                                    <input type="hidden" name="habit_id" value="<?php echo $row['habit_id']; ?>">
                                    <input type="text" name="habit_remark"
                                        value="<?php echo htmlspecialchars($row['habit_remark']); ?>" size="25"
                                        style="margin-bottom: 0;">
                                    <input type="submit" name="save_remark" value="Save" class="btn small">
                                </form>
                            </td>
                            <td>
                                <span
                                    style="padding: 6px 12px; border-radius: 15px; font-weight: 600; 
                                     <?php echo $row['is_done'] ? 'background: rgba(16, 185, 129, 0.1); color: #10B981;' : 'background: rgba(239, 68, 68, 0.1); color: #EF4444;'; ?>">
                                    <?php echo $row['is_done'] ? 'Done' : 'Not Done'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['is_done']) { ?>
                                    <a href="habit_today.php?id=<?php echo $row['habit_id']; ?>&toggle=0" class="link action">Mark
                                        as Not Done</a>
                                <?php } else { ?>
                                    <a href="habit_today.php?id=<?php echo $row['habit_id']; ?>&toggle=1" class="link action">Mark
                                        as Done</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <div class=" message success">No habits scheduled for today. <a href="habit_add.php">Add a habit!</a>
                </div>
            <?php } ?>
            <p>
                <a style="border:2px solid #7C3AED" href="habit_list.php">View Habit List</a> |
                <a style="border:2px solid #7C3AED" href="habit_history.php">View Habit History</a>
            </p>
        </div>

        <p><a href="index.php">Back to Dashboard</a></p>

    </div>
</body>

</html>