<?php
require "database.php";
include "authentication.php";

$color = "";
$status = "";
$balance = 0;
$isEmpty = true;

$filter = $_GET['filter'] ?? 'all';
$sortBy = $_GET['sortBy'] ?? "DESC";
$sortCol = $_GET['sortCol'] ?? "datetime";

if (isset($_POST['logout'])) {
    header("Location: logout.php");
    exit();
}

if (isset($_GET['status'])) {
    $status = ($_GET['status'] == 0) ? "Update Failed!" : "Update Successful!";
    $color = ($_GET['status'] == 0) ? "red" : "lawngreen";
}

if (isset($_SESSION['user_id'])) {
    $user_id = mysqli_real_escape_string($con, $_SESSION['user_id']);

    $income_query = "
        SELECT SUM(amount) AS total 
        FROM transaction 
        INNER JOIN category ON transaction.category_id = category.category_id
        WHERE transaction.user_id = '$user_id' AND category.type = 'Income'
    ";

    $expense_query = "
        SELECT SUM(amount) AS total 
        FROM transaction 
        INNER JOIN category ON transaction.category_id = category.category_id
        WHERE transaction.user_id = '$user_id' AND category.type = 'Expense'
    ";

    $income_result = mysqli_query($con, $income_query) or die("Failed to retrieve income: " . mysqli_error($con));
    $expense_result = mysqli_query($con, $expense_query) or die("Failed to retrieve expense: " . mysqli_error($con));

    $income_row = mysqli_fetch_assoc($income_result);
    $expense_row = mysqli_fetch_assoc($expense_result);

    $income = $income_row['total'] ?? 0; 
    $expense = $expense_row['total'] ?? 0;

    $isEmpty = (empty($income_row['total']) && empty($expense_row['total']));

    $balance = $income - $expense;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Transaction Records</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
            }

            body {
                display: flex;
                overflow-x: hidden;
                align-items: center;
                padding-bottom: 30px;
                flex-direction: column;
                font-family: "Audiowide", sans-serif;
                background-image: linear-gradient(to right, #7F7FD5, #86A8E7, #91EAE4);
            }

            a {
                text-decoration: none;
            }

            #header {
                width: 100%;
                padding: 15px;
                display: flex;
                position: relative;
                align-items: center;
                box-sizing: border-box;
                height: calc(5vh + 30px);
                justify-content: space-between;
            }

            #header div {
                margin: 5px;
                padding: 5px;
                font-size: 24px;
            }

            #links {
                top: 40%;
                left: 50%;
                display: flex;
                position: absolute;
                align-items: center;
                justify-content: space-between;
                transform: translate(-50%, -45%);
            }

            #links a {
                margin: 0 30px 0 30px;
            }

            #links a:hover {
                filter: brightness(150%);
            }

            .logout {
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                border: medium solid black;
                background-color: transparent;
                padding: 7.5px 15px 7.5px 15px;
                font-family: "Audiowide", sans-serif;
            }

            .logout:hover {
                color: white;
                border: none;
                background-color: navy;
                padding: 10px 17.5px 10px 17.5px;
            }

            #content, #transactions, #searchfilters {
                width: 1000px;
                display: flex;
                padding: 20px;
                border-radius: 20px;
                align-items: center;
                flex-direction: column;
                background-color: white;
                border: 3px solid black;
            }

            #content {
                margin-top: 50px;
                justify-content: space-between;
            }

            #searchfilters {
                margin-top: 20px;
                justify-content: center;
            }

            #searchfilters > form {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #searchfilters select {
                cursor: pointer;
                font-size: 14px;
                border-radius: 15px;
                padding: 5px 10px 5px 10px;
                font-family: "Audiowide", sans-serif;
            }

            #transactions {
                margin-top: 20px;
                justify-content: center;
            }

            #balance, #options, .record, #searchfilters > form {
                width: inherit;
            }

            #balance {
                text-align: left;
                margin-bottom: 10px;
            }

            #options, #views, #viewfilter, #buttons {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #viewfilter {
                padding: 5px;
                margin-left: 10px;
                border-radius: 15px;
                border: 1px solid black;
            }

            #viewfilter button {
                border: none;
                cursor: pointer;
                font-size: 14px;
                padding: 7.5px 10px;
                background: transparent;
                font-family: "Audiowide", sans-serif;
            }

            .selected {
                border-radius: 10px;
                background-color: #91EAE4 !important;
            }

            #buttons a {
                color: white;
            }

            .btns {
                width: 175px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                border-radius: 15px;
                padding: 10px 15px 10px 15px;
                font-family: "Audiowide", sans-serif;
            }

            .btns:hover {
                filter: brightness(120%);
            }

            .btns:nth-child(1) {
                background-color: #f15f79;
            }

            .btns:nth-child(2) {
                background-color: #009FFF;
            }

            .btns:nth-child(1):hover {
                filter: drop-shadow(0 0 5px #f15f79);
            }

            .btns:nth-child(2):hover {
                filter: drop-shadow(0 0 5px #009FFF);
            }

            .record {
                display: flex;
                border-radius: 15px;
                align-items: center;
                border: 2px solid black;
                background: linear-gradient(to right, #74ebd5, #ACB6E5);
            }

            .record > div {
                height: 100%;
                padding: 10px;
                display: flex;
                box-sizing: border-box;
            }

            .record > div:nth-child(1) {
                width: 7.5%;
                align-items: center;
                justify-content: center;
                border-right: 2px solid black;
            }

            .record > div:nth-child(2) {
                width: 50%;
                flex-direction: column;
                justify-content: space-between;
            }

            .record > div:nth-child(3) {
                width: 25%;
                align-items: center;
                flex-direction: column;
                justify-content: center;
            }

            .record > div:nth-child(4) {
                width: 17.5%;
                align-items: center;
                justify-content: center;
            }

            .eNd {
                width: 50%;
                display: flex;
                align-items: center;
                flex-direction: column;
                justify-content: center;
            }

            .secondOnwards {
                margin-top: 15px;
            }

            .alert {
                top: 35px;
                left: 50%;
                z-index: 2;
                color: white;
                width: 250px;
                padding: 10px;
                position: fixed;
                text-align: center;
                border-radius: 5px;
                transform: translateX(-50%);
            }
        </style>

    </head>
    <body>
        <?php
        if (!empty($status)) {
            ?>
            <div class="alert" style="background-color: <?php echo $color; ?>;">
                <span style="font-size: 14px;"><?php echo $status; ?></span>
            </div>
            <meta http-equiv="refresh" content="2;url=<?php echo $_SERVER['PHP_SELF']; ?>">
            <?php
        }
        ?>

        <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div id="links">
                <a href="index.php">Homepage</a>
                <a href="view_exercise.php">Exercise</a>
                <a href="view_daily_journal.php">Dairy</a>
                <a href="habit_today.php">Habit</a>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="logout" value="Logout" class="logout" onclick="return confirm('Are you sure you want to logout?')">
                </form>
            </div>
        </div>

        <div id="content">
            <div id="balance">
                Your Balance: 
                <h2 style="color: blue;">RM <?php echo $balance; ?></h2>
            </div>
            <div id="options">
                <div id="views">
                    <span>See:</span>
                    <form id="viewfilter" method="GET" action="">
                        <input type="hidden" name="sortBy" value="<?php echo $sortBy; ?>">
                        <input type="hidden" name="sortCol" value="<?php echo $sortCol; ?>">

                        <button type="submit" name="filter" class="<?php echo ($filter == 'all') ? 'selected' : ''?>" value="all" style="margin-right: 7.5px;">All</button>
                        <button type="submit" name="filter" class="<?php echo ($filter == 'income') ? 'selected' : ''?>" value="income" style="margin-right: 7.5px;">Only Income</button>
                        <button type="submit" name="filter" class="<?php echo ($filter == 'expense') ? 'selected' : ''?>" value="expense">Only Expense</button>
                    </form>
                </div>
                <div id="buttons">
                    <button type="button" class="btns" style="margin-right: 7.5px;"><a href="transaction_form.php">Add Transaction</a></button>
                    <button type="button" class="btns"><a href="">Generate Report</a></button>
                </div>
            </div>
        </div>

        <div id="searchfilters">
            <form method="GET" action="">
                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                <div>
                    Sort By: 
                    <select name="sortCol">
                        <option value="datetime" <?php echo ($sortCol == "datetime") ? "selected" : ""?>>Date of Payment</option>
                        <option value="amount" <?php echo ($sortCol != "datetime") ? "selected" : ""?>>Amount</option>
                    </select>
                    <select name="sortBy">
                        <option value="DESC" <?php echo ($sortBy == "DESC") ? "selected" : ""?>>Descending</option>
                        <option value="ASC" <?php echo ($sortBy != "DESC") ? "selected" : ""?>>Ascending</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btns" style="color: white;">Apply Filters</button>
                </div>
            </form>
        </div>

        <div id="transactions">
            <?php
            if (!$isEmpty) {
                $user_id = mysqli_real_escape_string($con, $_SESSION['user_id']);

                $type = ($filter != "all") ? ucwords($filter) : "";

                $query = "SELECT * FROM transaction 
                INNER JOIN category ON transaction.category_id = category.category_id
                WHERE user_id = '$user_id'";

                if (!empty($type)) {
                    $query .= " AND type = '$type'";
                }
                $query .= " ORDER BY $sortCol $sortBy";

                $result = mysqli_query($con, $query);
                $currencySymbol = "RM";
                $count = 1;

                while ($row = mysqli_fetch_assoc($result)) {
                    $class = ($count == 1) ? "" : "secondOnwards";
                    ?>
                    <div class="record <?php echo $class; ?>">
                        <div><?php echo $count++; ?></div>
                        <div>
                            <span>Amount: RM <?php echo $row['amount']; ?></span>
                            <span style="margin-top: 5px;">Category: <?php echo $row['name']; ?></span>
                            <span style="margin-top: 5px;">Type: <?php echo $row['type']; ?></span>
                            <span style="margin-top: 5px;">Details: <?php echo $row['description']; ?></span>
                        </div>
                        <div>
                            Paid On:
                            <span style="margin-top: 5px;"><?php echo $row['datetime']; ?></span>
                        </div>
                        <div>
                            <div class="eNd">
                                <a href="update_money.php?id=<?php echo $row['transaction_id']; ?>"><i class="fa-solid fa-pen"></i></a>
                                <span>Edit</span>
                            </div>
                            <div class="eNd">
                                <a href="delete_money.php?id=<?php echo $row['transaction_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')"><i class="fa-solid fa-trash-can"></i></a>
                                <span>Delete</span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            else {
                ?>
                <span>You have no transactions right now.</span>
                <?php
            }
            ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const buttons = document.querySelectorAll('#viewfilter button');

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        buttons.forEach(btn => btn.classList.remove('selected'));
                        button.classList.add('selected');
                    });
                });
            });
        </script>
    </body>
</html>