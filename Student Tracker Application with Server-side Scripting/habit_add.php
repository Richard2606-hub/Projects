<?php
include("authentication.php");
require("database.php");

if (isset($_POST['add_habit'])) {
    $habit_name = mysqli_real_escape_string($con, $_POST['habit_name']);
    $habit_type = $_POST['habit_type'];
    $specific_date = !empty($_POST['specific_date']) ? $_POST['specific_date'] : NULL;

    $is_daily = ($habit_type === "daily") ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    if ($is_daily) {
        $sql = "INSERT INTO habit (habit_name, is_daily, specific_date, user_id, created_at)
                VALUES ('$habit_name', $is_daily, NULL, $user_id, CURDATE())";
    } else {
        $sql = "INSERT INTO habit (habit_name, is_daily, specific_date, user_id, created_at)
                VALUES ('$habit_name', $is_daily, '$specific_date', $user_id, CURDATE())";
    }

    if (mysqli_query($con, $sql)) {
        header("Location: habit_list.php");
        exit();
    } else {
        echo "<div class='message error'>Error: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add New Habit</title>
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

        <div class="card form">
            <h1>Add New Habit</h1>

            <form method="post" action="habit_add.php">
                <label>Habit Name:</label>
                <input type="text" name="habit_name" required placeholder="Enter habit name" autofocus>

                <label>Habit Type:</label>
                <select name="habit_type" id="habit_type" onchange="toggleDateField()">
                    <option value="daily">Daily</option>
                    <option value="specific">Specific Date</option>
                </select>

                <div id="date_field" style="display:none;">
                    <label>Specific Date:</label>
                    <input type="date" name="specific_date" min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="add_habit" class="btn" style="flex: 1;">Add Habit</button>
                    <button type="button" onclick="goBack()" class="btn secondary" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleDateField() {
            const type = document.getElementById("habit_type").value;
            document.getElementById("date_field").style.display = (type === "specific") ? "block" : "none";
        }

        function goBack() {
            if (document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
                window.history.back();
            } else {
                window.location.href = 'habit_list.php';
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