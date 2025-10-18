<?php
session_start();
require('database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure ID is numeric
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid entry ID."); // Stop execution with an error
}

$user_id = $_SESSION['user_id'];

// Check database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Prepare statement
$stmt = $con->prepare("DELETE FROM daily_journal WHERE entry_id = ? AND user_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

// Bind parameters
$stmt->bind_param("ii", $id, $user_id);

// Execute statement
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: view_daily_journal.php");
        exit();
    } else {
        echo "No entry found to delete or you don't have permission.";
    }
} else {
    echo "Error deleting entry: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$con->close();
?>
