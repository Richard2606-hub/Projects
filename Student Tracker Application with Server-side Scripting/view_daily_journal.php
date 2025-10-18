<?php
session_start();
require('database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM daily_journal WHERE user_id='$user_id' ORDER BY entry_date DESC";
$result = mysqli_query($con, $sql);

// Error handling for SQL execution
if (!$result) {
    die("Error executing query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Journal</title>
    <link rel="stylesheet" href="view_daily_journal.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
</head>
<body>

    <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>
    </div>

    <h2>My Journal Entries</h2>
    <p><a href="add_daily_journal.php">➕ Add New Entry</a>| <a href="index.php">🏠 Homepage</a></p>
    <table border = "1" cellpadding = "5">
        <tr><th>Date</th><th>Mood</th><th>Entry</th><th>Actions</th></tr>
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['entry_date']); ?></td>
                <td><?php echo htmlspecialchars($row['mood']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['entry_text'])); ?></td>
                <td>
                    <a href="update_daily_journal.php?id=<?php echo $row['entry_id']; ?>">✏️ Edit</a> | 
                    <a href="delete_daily_journal.php?id=<?php echo $row['entry_id']; ?>" onclick="return confirm('Delete this entry?');">🗑️ Delete</a>
                </td>
            </tr>
            <?php } 
        } else { ?>
            <tr><td colspan="4">No journal entries found.</td></tr>
        <?php } ?>
    </table>
</body>
</html>
