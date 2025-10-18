<?php
include("authentication.php");
require('database.php');

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$records_per_page = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $records_per_page;

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';

$allowed_columns = ['date', 'habit_name', 'habit_remark', 'is_done'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'date';
}

$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$where_conditions = ["user_id='$user_id'"];

if ($filter_status == 'done') {
    $where_conditions[] = "is_done = 1";
} elseif ($filter_status == 'not_done') {
    $where_conditions[] = "is_done = 0";
}

if (!empty($search_name)) {
    $search_name_escaped = mysqli_real_escape_string($con, $search_name);
    $where_conditions[] = "habit_name LIKE '%$search_name_escaped%'";
}

if (!empty($date_from)) {
    $where_conditions[] = "date >= '$date_from'";
}

if (!empty($date_to)) {
    $where_conditions[] = "date <= '$date_to'";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

$count_query = "SELECT COUNT(*) as total FROM habit_history $where_clause";
$count_result = mysqli_query($con, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

$order_clause = "ORDER BY $sort_column " . ($sort_order === 'desc' ? 'DESC' : 'ASC');

$query = "SELECT * FROM habit_history $where_clause $order_clause LIMIT $offset, $records_per_page";
$result = mysqli_query($con, $query);


function getSortUrl($column, $current_sort, $current_order)
{
    $params = $_GET;
    $params['sort'] = $column;

    if ($current_sort === $column) {
        $params['order'] = ($current_order === 'desc') ? 'asc' : 'desc';
    } else {
        $params['order'] = ($column === 'date') ? 'desc' : 'asc';
    }

    return 'habit_history.php?' . http_build_query($params);
}

function getPaginationUrl($page_num)
{
    $params = $_GET;
    $params['page'] = $page_num;
    return 'habit_history.php?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Habit History</title>
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
            <h1>Habit History <a style="float: right; font-size:20px; padding:10px; color: white;text-decoration: none;border-radius: 15px;transition: all 0.3s ease;display: inline-block;background: #7C3AED" href="habit_today.php">View Today's Habits</a></h1>
            <hr><br>
            <?php if ($total_records > 0) { ?>

                <form method="GET" action="habit_history.php">
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; align-items: end;">
                        <div>
                            <label>Search by Name:</label>
                            <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>"
                                placeholder="Enter habit name">
                        </div>

                        <div>
                            <label>Status:</label>
                            <select name="filter_status">
                                <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All Status
                                </option>
                                <option value="done" <?php echo $filter_status == 'done' ? 'selected' : ''; ?>>Done
                                </option>
                                <option value="not_done" <?php echo $filter_status == 'not_done' ? 'selected' : ''; ?>>Not
                                    Done</option>
                            </select>
                        </div>

                        <div>
                            <label>From Date:</label>
                            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                        </div>

                        <div>
                            <label>To Date:</label>
                            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                        </div>

                        <div>
                            <button type="submit" class="btn primary">Apply Filter</button>
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
                        <col style="width: 20%;">
                        <col style="width: 25%;">
                        <col style="width: 20%;">
                        <col style="width: 15%;">
                        <col style="width: 20%;">
                    </colgroup>
                    <tr>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('date', $sort_column, $sort_order); ?>'">
                            Date
                            <?php if ($sort_column === 'date'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('habit_name', $sort_column, $sort_order); ?>'">
                            Habit Name
                            <?php if ($sort_column === 'habit_name'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('habit_remark', $sort_column, $sort_order); ?>'">
                            Remark
                            <?php if ($sort_column === 'habit_remark'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th style="cursor: pointer; user-select: none;"
                            onclick="window.location.href='<?php echo getSortUrl('is_done', $sort_column, $sort_order); ?>'">
                            Status
                            <?php if ($sort_column === 'is_done'): ?>
                                <span
                                    style="font-size: 12px; margin-left: 5px;"><?php echo $sort_order === 'asc' ? '▲' : '▼'; ?></span>
                            <?php endif; ?>
                        </th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) {
                        $is_today = ($row['date'] === $today);
                        ?>
                        <tr <?php if ($is_today)
                            echo 'style="background-color: #FFF7ED; border-left: 4px solid #F59E0B;"'; ?>>
                            <td>
                                <?php echo date('M j, Y', strtotime($row['date'])); ?>
                                <?php if ($is_today)
                                    echo '<br><span style="color: #F59E0B; font-size: 11px; font-weight: bold;">TODAY</span>'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['habit_name']); ?></td>
                            <td style="max-width: 200px; word-wrap: break-word;">
                                <?php echo htmlspecialchars($row['habit_remark'] ?: 'No remark'); ?>
                            </td>
                            <td>
                                <span
                                    style="padding: 6px 12px; border-radius: 15px; font-weight: 600; 
                                 <?php echo $row['is_done'] ? 'background: rgba(16, 185, 129, 0.1); color: #10B981;' : 'background: rgba(239, 68, 68, 0.1); color: #EF4444;'; ?>">
                                    <?php echo $row['is_done'] ? 'Done' : 'Not Done'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($is_today) { ?>
                                    <a href="habit_today.php" style="color: #F59E0B; font-weight: bold;">Today's Habits</a>
                                <?php } else { ?>
                                    <a href="habit_edit_history.php?id=<?php echo $row['id']; ?>" class="link edit">Edit</a>
                                    <a href="habit_delete_history.php?id=<?php echo $row['id']; ?>" class="link delete"
                                        onclick="return confirm('Delete this history record?');">Delete</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <div
                    style="background: #FEF3C7; border: 1px solid #F59E0B; color: #92400E; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    <strong>Note:</strong> Today's habit records cannot be edited or deleted.
                </div>

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

                <p>
                    Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $records_per_page, $total_records); ?>
                    of
                    <?php echo $total_records; ?> record(s)
                    <?php if (!empty($search_name) || $filter_status != 'all' || !empty($date_from) || !empty($date_to)) { ?>
                        | <a href="habit_history.php" style="color: #EF4444;">Clear all filters</a>
                    <?php } ?>
                </p><br>

            <?php } else { ?>
                <?php if (!empty($search_name) || $filter_status != 'all' || !empty($date_from) || !empty($date_to)) { ?>
                    <div class='message error'>No history records found matching your filters. <a href='habit_history.php'>View
                            all
                            history</a></div>
                <?php } else { ?>
                    <div class='message success'>No habit history found. Start tracking your habits!</div>
                <?php } ?>
            <?php } ?>
        </div>
        <p><a href="index.php">Back to Dashboard</a></p>

    </div>

    <script>
        function clearFilters() {
            window.location.href = 'habit_history.php';
        }
    </script>
</body>

</html>