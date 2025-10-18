<?php
include("authentication.php");
require("database.php");

$user_id = $_SESSION["user_id"];

$records_per_page = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $records_per_page;

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'specific_date';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$allowed_columns = ['habit_name', 'is_daily', 'specific_date', 'created_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'habit_name';
}

$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 'all';
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$specific_date = isset($_GET['specific_date']) ? $_GET['specific_date'] : '';

$where_conditions = ["user_id = $user_id"];

$where_conditions[] = "(is_daily = 1 OR (is_daily = 0 AND specific_date >= CURDATE()))";

if ($filter_type == 'daily') {
    $where_conditions[] = "is_daily = 1";
} elseif ($filter_type == 'specific') {
    $where_conditions[] = "is_daily = 0";

    if (!empty($specific_date)) {
        $where_conditions[] = "specific_date = '$specific_date'";
    }
}

if (!empty($search_name)) {
    $search_name_escaped = mysqli_real_escape_string($con, $search_name);
    $where_conditions[] = "habit_name LIKE '%$search_name_escaped%'";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

$count_query = "SELECT COUNT(*) as total FROM habit $where_clause";
$count_result = mysqli_query($con, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

$order_clause = "ORDER BY ";
if ($sort_column === 'is_daily') {
    $order_clause .= "is_daily " . ($sort_order === 'desc' ? 'DESC' : 'ASC') . ", habit_name ASC";
} else {
    $order_clause .= "$sort_column " . ($sort_order === 'desc' ? 'DESC' : 'ASC');
}

$habit_result = mysqli_query($con, "SELECT * FROM habit $where_clause $order_clause LIMIT $offset, $records_per_page");

function getSortUrl($column, $current_sort, $current_order)
{
    $params = $_GET;
    $params['sort'] = $column;

    if ($current_sort === $column) {
        $params['order'] = ($current_order === 'asc') ? 'desc' : 'asc';
    } else {
        $params['order'] = 'asc';
    }

    return 'habit_list.php?' . http_build_query($params);
}

function getPaginationUrl($page_num)
{
    $params = $_GET;
    $params['page'] = $page_num;
    return 'habit_list.php?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Habit List</title>
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
            <h1>Habit List <a style="float: right; font-size:20px; padding:10px; color: white;text-decoration: none;border-radius: 15px;transition: all 0.3s ease;display: inline-block;background: #7C3AED" href="habit_today.php">View Today's Habits</a></h1>

            <hr><br>
            <?php if ($total_records > 0) { ?>

                <form method="GET" action="habit_list.php">
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                        <div>
                            <label>Search by Name:</label>
                            <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>"
                                placeholder="Enter habit name">
                        </div>

                        <div>
                            <label>Filter by Type:</label>
                            <select name="filter_type" id="filter_type" onchange="toggleDateFilters()">
                                <option value="all" <?php echo $filter_type == 'all' ? 'selected' : ''; ?>>All Habits</option>
                                <option value="daily" <?php echo $filter_type == 'daily' ? 'selected' : ''; ?>>Daily Habits
                                </option>
                                <option value="specific" <?php echo $filter_type == 'specific' ? 'selected' : ''; ?>>Specific
                                    Date</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="btn primary">Apply Filter</button>
                        </div>
                    </div>

                    <div id="date-filters"
                        style="<?php echo ($filter_type == 'specific') ? '' : 'display: none;'; ?> margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                        <div style="max-width: 200px;">
                            <label>Select Date:</label>
                            <input type="date" name="specific_date" value="<?php echo htmlspecialchars($specific_date); ?>">
                            <div style="font-size: 12px; color: #666; margin-top: 5px; font-style: italic;">
                                Show habits scheduled for this exact date
                            </div>
                        </div>
                    </div>

                    <?php
                    if (isset($_GET['sort']))
                        echo '<input type="hidden" name="sort" value="' . htmlspecialchars($_GET['sort']) . '">';
                    if (isset($_GET['order']))
                        echo '<input type="hidden" name="order" value="' . htmlspecialchars($_GET['order']) . '">';
                    ?>
                </form><br>
                <hr><br>

                <table>
                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 20%;">
                        <col style="width: 10%;">
                        <col style="width: 15%;">
                        <col style="width: 15%;">
                        <col style="width: 20%;">
                    </colgroup>
                    <tr>
                        <th>No.</th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('habit_name', $sort_column, $sort_order); ?>'">
                            Habit Name
                            <?php if ($sort_column === 'habit_name'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('is_daily', $sort_column, $sort_order); ?>'">
                            Type
                            <?php if ($sort_column === 'is_daily'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('specific_date', $sort_column, $sort_order); ?>'">
                            Date/Schedule
                            <?php if ($sort_column === 'specific_date'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('created_at', $sort_column, $sort_order); ?>'">
                            Created
                            <?php if ($sort_column === 'created_at'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $no = $offset + 1;
                    while ($habit = mysqli_fetch_assoc($habit_result)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($habit['habit_name']) . "</td>";

                        if ($habit['is_daily']) {
                            echo "<td><span style='background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600;'>Daily</span></td>";
                            echo "<td>Every day</td>";
                        } else {
                            echo "<td><span style='background: rgba(59, 130, 246, 0.1); color: #3B82F6; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600;'>Specific</span></td>";
                            echo "<td>" . ($habit['specific_date'] ? date('M j, Y', strtotime($habit['specific_date'])) : "Not Set") . "</td>";
                        }

                        echo "<td>" . ($habit['created_at'] ? date('M j, Y', strtotime($habit['created_at'])) : 'N/A') . "</td>";
                        echo "<td><a href='habit_edit.php?id={$habit['habit_id']}' class='link edit'>Edit</a><a href='habit_delete.php?id={$habit['habit_id']}' class='link delete' onclick='return confirm(\"Are you sure you want to delete this habit?\");'>Delete</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin: 20px 0;">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo getPaginationUrl(1); ?>"
                                style="padding: 8px 12px; border-radius: 5px; text-decoration: none; border: 1px solid #ddd; background: white; color: #7C3AED;">&laquo;
                                First</a>
                            <a href="<?php echo getPaginationUrl($page - 1); ?>"
                                style="padding: 8px 12px; border-radius: 5px; text-decoration: none; border: 1px solid #ddd; background: white; color: #7C3AED;">&lt;
                                Prev</a>
                        <?php else: ?>
                            <span
                                style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; color: #999; cursor: not-allowed;">&laquo;
                                First</span>
                            <span
                                style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; color: #999; cursor: not-allowed;">&lt;
                                Prev</span>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);

                        for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span
                                    style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; background: #7C3AED; color: white;"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo getPaginationUrl($i); ?>"
                                    style="padding: 8px 12px; border-radius: 5px; text-decoration: none; border: 1px solid #ddd; background: white; color: #7C3AED;"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="<?php echo getPaginationUrl($page + 1); ?>"
                                style="padding: 8px 12px; border-radius: 5px; text-decoration: none; border: 1px solid #ddd; background: white; color: #7C3AED;">Next
                                &gt;</a>
                            <a href="<?php echo getPaginationUrl($total_pages); ?>"
                                style="padding: 8px 12px; border-radius: 5px; text-decoration: none; border: 1px solid #ddd; background: white; color: #7C3AED;">Last
                                &raquo;</a>
                        <?php else: ?>
                            <span
                                style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; color: #999; cursor: not-allowed;">Next
                                &gt;</span>
                            <span
                                style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; color: #999; cursor: not-allowed;">Last
                                &raquo;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <p style="margin: 15px; color: #666; text-align: center;">
                    Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $records_per_page, $total_records); ?> of
                    <?php echo $total_records; ?> habit(s)
                    <?php if (!empty($search_name) || $filter_type != 'all' || !empty($specific_date)) { ?>
                        | <a href="habit_list.php" style="color: #EF4444;">Clear all filters</a>
                    <?php } ?>
                </p>
                <?php
            } else {
                if (!empty($search_name) || $filter_type != 'all' || !empty($specific_date)) {
                    echo "<div class='message error'>No habits found matching your filters. <br><a href='habit_list.php'>View all habits</a> or <a href='habit_add.php'>add a new habit</a>.</div>";
                } else {
                    echo "<div class='message success'>No habits added yet. <a href='habit_add.php'>Add your first habit!</a></div>";
                }
            }
            ?>
            <p>
                <a style="border:2px solid #7C3AED" href="habit_add.php">Add New Habit</a>
            </p>

        </div>
        <p><a href="index.php">Back to Dashboard</a></p>

    </div>

    <script>
        function clearFilters() {
            window.location.href = 'habit_list.php';
        }

        function toggleDateFilters() {
            const filterType = document.getElementById('filter_type').value;
            const dateFilters = document.getElementById('date-filters');

            if (filterType === 'specific') {
                dateFilters.style.display = 'block';
            } else {
                dateFilters.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleDateFilters();
        });
    </script>
</body>

</html>