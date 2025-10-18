<?php
session_start();
require('database.php');

$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new']) && $_POST['new'] == 1) {
    if (!isset($_SESSION['user_id'])) {
        $status = "❌ Error: User not logged in.";
    } else {
        $user_id = $_SESSION['user_id'];
        $entry_date = trim($_POST['entry_date']);
        $mood = trim($_POST['mood']);
        $entry_text = trim($_POST['entry_text']);

        // Validate required fields
        if (empty($entry_date) || empty($mood) || empty($entry_text)) {
            $status = "❌ Error: All fields are required.";
        } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $entry_date)) { 
            $status = "❌ Error: Invalid date format.";
        } elseif (!in_array($mood, ["Happy", "Sad", "Neutral", "Excited", "Stressed"])) {
            $status = "❌ Error: Invalid mood value.";
        } else {
            // Escape inputs
            $entry_date = mysqli_real_escape_string($con, $entry_date);
            $mood = mysqli_real_escape_string($con, $mood);
            $entry_text = mysqli_real_escape_string($con, $entry_text);

            $sql = "INSERT INTO daily_journal (user_id, entry_text, entry_date, mood)
                    VALUES ('$user_id', '$entry_text', '$entry_date', '$mood')";

            if (mysqli_query($con, $sql)) {
                $status = "✅ Journal entry added successfully. <a href='view_daily_journal.php'>View Entries</a>";
            } else {
                $status = "❌ Database Error: Unable to save entry.";
                error_log("MySQL Error: " . mysqli_error($con));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Journal</title>
    <link rel="stylesheet" href="add_daily_journal.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>
<body>
<h2>Add New Journal Entry</h2>
<form method="post">
    <input type="hidden" name="new" value="1">
    
<p>Date: 
<input type="date" name="entry_date" required 
       oninvalid="this.setCustomValidity('⚠️ Please select a valid date.')" 
       oninput="this.setCustomValidity('')">
</p>

<p>Mood: 
<select name="mood" required 
        oninvalid="this.setCustomValidity('⚠️ Please select your mood.')" 
        oninput="this.setCustomValidity('')">
    <option value="">-- Select Mood --</option>
    <option value="Happy">Happy</option>
    <option value="Sad">Sad</option>
    <option value="Stressed">Stressed</option>
    <option value="Excited">Excited</option>
    <option value="Neutral">Neutral</option>
</select>
</p>

<p>Journal Entry: 
<textarea name="entry_text" placeholder="Enter your daily entry here." rows="5" cols="40" required
          oninvalid="this.setCustomValidity('⚠️ Please write your journal entry before saving.')" 
          oninput="this.setCustomValidity('')"></textarea>
</p>
<p><input type="submit" value="Save"></p>
<p style="color:red;"><?php echo $status; ?></p>
</form>
<p><a href="view_daily_journal.php">Back to Journal</a></p>
</body>
</html>
