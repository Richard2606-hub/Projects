<?php
require('database.php');
include('authentication.php');
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM exercises WHERE user_id =" . $user_id;
$exercise_result = mysqli_query($con, $query);
$exercise = [];
while ($row = mysqli_fetch_assoc($exercise_result)) {
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
$dateRegister = $_GET['dateRegister'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

$count_query = "SELECT COUNT(*) as total FROM exercises_history WHERE user_id = $user_id AND 1=1";

$sel_query = "SELECT * FROM exercises_history WHERE user_id = $user_id AND 1=1";

$filter_conditions = "";

if (!empty($search)) {
    $filter_conditions .= " AND exerciseName LIKE '%$search%'";
}
if (!empty($caloriesBurn) && is_numeric($caloriesBurn)) {
    $filter_conditions .= " AND caloriesBurn >= '$caloriesBurn'";
}
if (!empty($dateRegister)) {
    $filter_conditions .= " AND dateRegister = '$dateRegister'";
}

if (!empty($min_duration) && is_numeric($min_duration) && !empty($max_duration) && is_numeric($max_duration)) {
    $min_time = gmdate("H:i:s", $min_duration * 60);
    $max_time = gmdate("H:i:s", $max_duration * 60);
    $filter_conditions .= " AND duration >= '$min_time' AND duration <= '$max_time'";
} else if (!empty($max_duration) && is_numeric($max_duration) && empty($min_duration)) {
    $max_time = gmdate("H:i:s", $max_duration * 60);
    $filter_conditions .= " AND duration <= '$max_time'";
} else if (!empty($min_duration) && is_numeric($min_duration) && empty($max_duration)) {
    $min_time = gmdate("H:i:s", $min_duration * 60);
    $filter_conditions .= " AND duration >= '$min_time'";
}

$count_query .= $filter_conditions;
$sel_query .= $filter_conditions;

$count_result = mysqli_query($con, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

$sel_query .= " ORDER BY history_id DESC LIMIT $records_per_page OFFSET $offset";
$result = mysqli_query($con, $sel_query);

function buildUrlWithFilters($page) {
    $params = $_GET;
    $params['page'] = $page;
    return 'view_history.php?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FocusTrack - View Exercise History</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <style>
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }
        
        .pagination a:hover {
            background-color: #f5f5f5;
        }
        
        .pagination .current {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination .disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        .pagination .disabled:hover {
            background-color: transparent;
        }
        
        .pagination-info {
            text-align: center;
            margin: 10px 0;
            color: #666;
        }
    </style>
</head>

<body>
    <div>
        <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
    </div>
    <h1 style="text-align: center; font-size: 34px; color: rgba(67, 16, 150, 1); text-shadow: 0 1px 1px rgba(52, 0, 136, 1)">Exercise Tracker</h1>
    <br>
    <div class="search-filter">
        <form method="GET" action="view_history.php">
            <div>
                <label>Exercise Name:</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search exercise name">
            </div>
            <div>
                <label>Min Duration (minutes):</label>
                <input type="number" name="min_duration" value="<?php echo htmlspecialchars($min_duration); ?>" step="1"
                    min="0" placeholder="e.g. 30">
            </div>
            <div>
                <label>Max Duration (minutes):</label>
                <input type="number" name="max_duration" value="<?php echo htmlspecialchars($max_duration); ?>" step="1"
                    min="0" placeholder="e.g. 60">
            </div>
            <div>
                <label>Min Calorie Burn:</label>
                <input type="number" name="caloriesBurn" value="<?php echo htmlspecialchars($caloriesBurn); ?>" step="1"
                    min="0" placeholder="e.g. 200">
            </div>
            <div>
                <label>Date:</label>
                <input type="date" name="dateRegister" value="<?php echo htmlspecialchars($dateRegister); ?>">
            </div>
            <div>
                <button type="submit">Search & Filter</button>
            </div>
            <div>
                <button type="button" onclick="window.location.href='view_history.php'">Reset</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <h1 class="page-title">Exercise History</h1>
        <?php echo "<p>Today's Date: " . date("Y-m-d") . "</p>"; ?>
        
        <div class="pagination-info">
            Showing <?php echo min(($page - 1) * $records_per_page + 1, $total_records); ?> to 
            <?php echo min($page * $records_per_page, $total_records); ?> of <?php echo $total_records; ?> records
        </div>
        
        <table>
            <thead>
                <tr>
                    <th><strong>No.</strong></th>
                    <th><strong>Exercise Name</strong></th>
                    <th><strong>Duration</strong></th>
                    <th><strong>Calorie Burn</strong></th>
                    <th><strong>Date</strong></th>
                    <th><strong>Actions</strong></th>
                </tr>
            </thead>

            <tbody>
                <?php
                $count = ($page - 1) * $records_per_page + 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row["exerciseName"]); ?></td>
                            <td><span class="duration-display"><?php echo $row["duration"]; ?></span></td>
                            <td><?php echo $row["caloriesBurn"]; ?> cal</td>
                            <td><?php echo $row["dateRegister"]; ?></td>
                            <td>
                                <a href="edit_history.php?history_id=<?php echo $row["history_id"]; ?>">Edit</a>
                                <a href="delete_history.php?history_id=<?php echo $row["history_id"]; ?>"
                                    onclick="return confirm('Are you sure you want to delete this Exercise?')">Delete</a>
                            </td>
                        </tr>
                        <?php $count++;
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center;'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?php echo buildUrlWithFilters(1); ?>">&laquo; First</a>
            <?php else: ?>
                <span class="disabled">&laquo; First</span>
            <?php endif; ?>

            <?php if ($page > 1): ?>
                <a href="<?php echo buildUrlWithFilters($page - 1); ?>">&lsaquo; Prev</a>
            <?php else: ?>
                <span class="disabled">&lsaquo; Prev</span>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1) {
                echo '<a href="' . buildUrlWithFilters(1) . '">1</a>';
                if ($start_page > 2) {
                    echo '<span class="disabled">...</span>';
                }
            }
            
            for ($i = $start_page; $i <= $end_page; $i++):
                if ($i == $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="<?php echo buildUrlWithFilters($i); ?>"><?php echo $i; ?></a>
                <?php endif;
            endfor;
            
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="disabled">...</span>';
                }
                echo '<a href="' . buildUrlWithFilters($total_pages) . '">' . $total_pages . '</a>';
            }
            ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?php echo buildUrlWithFilters($page + 1); ?>">Next &rsaquo;</a>
            <?php else: ?>
                <span class="disabled">Next &rsaquo;</span>
            <?php endif; ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?php echo buildUrlWithFilters($total_pages); ?>">Last &raquo;</a>
            <?php else: ?>
                <span class="disabled">Last &raquo;</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <br>
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <?php echo $exerciseChecker; ?>
            <a href="add_exercise.php">Insert New Exercise</a>
            <a href="view_exercise.php">View Exercise List</a>
        </div>
    </div>
</body>

</html>