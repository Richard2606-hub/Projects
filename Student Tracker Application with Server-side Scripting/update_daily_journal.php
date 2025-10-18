<?php
session_start();
require('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid journal entry ID.");
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch entry
$sql = "SELECT * FROM daily_journal WHERE entry_id='$id' AND user_id='$user_id'";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("❌ Database Error: " . mysqli_error($con));
}

$entry = mysqli_fetch_assoc($result);

if (!$entry) {
    die("❌ Entry not found or not yours.");
}

$status = "";

if (isset($_POST['update'])) {
    $entry_date = trim($_POST['entry_date']);
    $mood = trim($_POST['mood']);
    $entry_text = trim($_POST['entry_text']);

    // Basic validation
    if (empty($entry_date) || empty($mood) || empty($entry_text)) {
        $status = "❌ Error: All fields are required.";
    } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $entry_date)) {
        $status = "❌ Error: Invalid date format.";
    } elseif (!in_array($mood, ["Happy", "Sad", "Neutral", "Excited", "Stressed"])) {
        $status = "❌ Error: Invalid mood value.";
    } else {
        $update = "UPDATE daily_journal 
                   SET entry_date='$entry_date', mood='$mood', entry_text='$entry_text'
                   WHERE entry_id='$id' AND user_id='$user_id'";

        if (mysqli_query($con, $update)) {
            header("Location: view_daily_journal.php");
            exit();
        } else {
            $status = "❌ Database Error: Unable to update entry.";
            error_log("MySQL Error: " . mysqli_error($con));
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Journal</title>
    <link rel="stylesheet" href="update_daily_journal.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>
<body>
<h2>Edit Journal Entry</h2>
<form method="post">
    <p>Date: 
      <input type="date" name="entry_date" value="<?php echo htmlspecialchars($entry['entry_date']); ?>" required
             oninvalid="this.setCustomValidity('⚠️ Please select a valid date.')" 
             oninput="this.setCustomValidity('')">
    </p>
    <p>Mood:
        <select name="mood" required
             oninvalid="this.setCustomValidity('⚠️ Please select your mood.')" 
             oninput="this.setCustomValidity('')">
            <option value="Happy" <?php if($entry['mood']=="Happy") echo "selected"; ?>>Happy</option>
            <option value="Sad" <?php if($entry['mood']=="Sad") echo "selected"; ?>>Sad</option>
            <option value="Neutral" <?php if($entry['mood']=="Neutral") echo "selected"; ?>>Neutral</option>
            <option value="Excited" <?php if($entry['mood']=="Excited") echo "selected"; ?>>Excited</option>
            <option value="Stressed" <?php if($entry['mood']=="Stressed") echo "selected"; ?>>Stressed</option>
        </select>
    </p>
    <p>Journal Entry:<br>
        <textarea name="entry_text" placeholder = "Enter your daily entry here. You can have any content that you would like to write for." rows="5" cols="50" required
                  oninvalid="this.setCustomValidity('⚠️ Please write your journal entry before saving.')" 
                  oninput="this.setCustomValidity('')"><?php echo htmlspecialchars($entry['entry_text']); ?></textarea>
    </p>
    <p><input type="submit" name="update" value="Update"></p>
    <p style="color:red;"><?php echo $status; ?></p>
</form>
<p><a href="view_daily_journal.php">Back to Journal</a></p>
</body>
</html>
