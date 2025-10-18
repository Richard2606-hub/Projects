<?php
include("authentication.php");
require("database.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: habit_list.php");
    exit();
}

$habit_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $con->prepare("SELECT * FROM habit WHERE habit_id = ? AND user_id = ?");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: habit_list.php");
    exit();
}
$habit = $result->fetch_assoc();

if (isset($_POST['edit_habit'])) {
    $habit_name = mysqli_real_escape_string($con, $_POST['habit_name']);
    $habit_type = $_POST['habit_type'];
    $specific_date = !empty($_POST['specific_date']) ? $_POST['specific_date'] : NULL;
    $is_daily = ($habit_type === "daily") ? 1 : 0;

    $update = $con->prepare("UPDATE habit SET habit_name = ?, is_daily = ?, specific_date = ? WHERE habit_id = ? AND user_id = ?");
    $update->bind_param("sisii", $habit_name, $is_daily, $specific_date, $habit_id, $user_id);

    if ($update->execute()) {
        header("Location: habit_list.php");
        exit();
    } else {
        echo "<div class='message error'>Error updating habit: " . $con->error . "</div>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Habit</title>
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
            <h1>Edit Habit</h1>
            <p style="color: #666; text-align: left; margin-bottom:20px;">
                <strong>Habit Name: </strong><?php echo htmlspecialchars($habit['habit_name']); ?><br>
                <strong>Habit Type: </strong><?php
                if ($habit['is_daily']) {
                    echo "<td><span
                        style='background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600;'>Daily</span>
                </td>";
                    echo "<td>Every day</td>";
                } else {
                    echo "<td><span
                        style='background: rgba(59, 130, 246, 0.1); color: #3B82F6; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600;'>Specific</span>
                </td>";
                    echo "<td>" . ($habit['specific_date'] ? date('M j, Y', strtotime($habit['specific_date'])) : "Not Set")
                        . "</td>";
                } ?>
            </p>
            <form method="post">
                <label>Habit Name:</label>
                <input type="text" name="habit_name" value="<?php echo htmlspecialchars($habit['habit_name']); ?>"
                    required autofocus>

                <label>Habit Type:</label>
                <select name="habit_type" id="habit_type" onchange="toggleDateField()">
                    <option value="daily" <?php if ($habit['is_daily'])
                        echo 'selected'; ?>>Daily</option>
                    <option value="specific" <?php if (!$habit['is_daily'])
                        echo 'selected'; ?>>Specific Date</option>
                </select>

                <div id="date_field" style="display:<?php echo $habit['is_daily'] ? 'none' : 'block'; ?>;">
                    <label>Specific Date:</label>
                    <input type="date" name="specific_date"
                        value="<?php echo htmlspecialchars($habit['specific_date']); ?>"
                        min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="edit_habit" class="btn" style="flex: 1;">Update Habit</button>
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