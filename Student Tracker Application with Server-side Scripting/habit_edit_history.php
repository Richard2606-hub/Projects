<?php
include("authentication.php");
require('database.php');

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
if (isset($_GET['id'])) {
    $history_id = intval($_GET['id']);
    $result = mysqli_query($con, "SELECT * FROM habit_history WHERE id='$history_id' AND user_id='$user_id'");
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        header("Location: habit_history.php?msg=Record+not+found");
        exit();
    }

    if ($row['date'] === $today) {
        header("Location: habit_history.php?msg=Cannot+edit+today's+record.+Use+Today's+Habits+page");
        exit();
    }
} else {
    header("Location: habit_history.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $history_id = intval($_POST['id']);

    $check_result = mysqli_query($con, "SELECT date FROM habit_history WHERE id='$history_id' AND user_id='$user_id'");
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row && $check_row['date'] === $today) {
        header("Location: habit_history.php?msg=Cannot+edit+today's+record");
        exit();
    }

    $habit_name = mysqli_real_escape_string($con, $_POST['habit_name']);
    $habit_remark = mysqli_real_escape_string($con, $_POST['habit_remark']);
    $is_done = intval($_POST['is_done']);

    mysqli_query($con, "UPDATE habit_history 
                        SET habit_name='$habit_name', habit_remark='$habit_remark', is_done='$is_done' 
                        WHERE id='$history_id' AND user_id='$user_id'");

    header("Location: habit_history.php?msg=History+updated");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Habit History</title>
    <link rel="stylesheet" href="habit-tracker-styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>

<body>
    <div>
        <span style="color:floralwhite; font-size: 30px;">Focus</span><span
            style="color:darkgreen; font-size: 30px;">Track</span>
    </div>
    <div class="container">
        <div class="card form">
            <h1>Edit Habit History</h1>
            <p style="color: #666; text-align: left; margin-bottom:20px;">
                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['date'])); ?><br>
                <strong>Habit Name: </strong><?php echo htmlspecialchars($row['habit_name']); ?><br>
                <strong>Remark: </strong><?php echo htmlspecialchars($row['habit_remark']); ?><br>
                <strong>Status:</strong><?php echo $row['is_done'] ? 'Done' : 'Not Done'; ?><br>
            </p>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                <label>Habit Name:</label>
                <input type="text" name="habit_name" value="<?php echo htmlspecialchars($row['habit_name']); ?>"
                    required autofocus>

                <label>Remark:</label>
                <input type="text" name="habit_remark" value="<?php echo htmlspecialchars($row['habit_remark']); ?>"
                    placeholder="Add a note about this habit">

                <label>Status:</label>
                <select name="is_done">
                    <option value="0" <?php if ($row['is_done'] == 0)
                        echo "selected"; ?>>Not Done</option>
                    <option value="1" <?php if ($row['is_done'] == 1)
                        echo "selected"; ?>>Done</option>
                </select>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <input type="submit" value="Update History" class="btn" style="flex: 1;">
                    <button type="button" onclick="goBack()" class="btn secondary" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            if (document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
                window.history.back();
            } else {
                window.location.href = 'habit_history.php';
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                goBack();
            }
        });
    </script>
</body>

</html>