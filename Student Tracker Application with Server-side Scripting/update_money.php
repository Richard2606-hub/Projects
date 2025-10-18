<?php
require "database.php";

$options = [
    1 => ["Allowance", "Income"],
    2 => ["Scholarship", "Income"],
    3 => ["Part-time Job", "Income"],
    4 => ["Gift", "Income"],
    5 => ["Food", "Expense"],
    6 => ["Bills", "Expense"],
    7 => ["Transport", "Expense"],
    8 => ["Entertainment", "Expense"],
    9 => ["Books & Supplies", "Expense"],
    10 => ["Health & Personal Care", "Expense"],
];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "
        SELECT 
            transaction.amount,
            transaction.category_id AS t_category_id,
            category.category_id AS c_category_id,
            category.name,
            category.type,
            transaction.description
        FROM transaction
        INNER JOIN category ON transaction.category_id = category.category_id
        WHERE transaction.transaction_id = '$id'
    ";
    $result = mysqli_query($con, $query) or die(mysqli_error($con));

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $selectedCategory = (int)$row['t_category_id'];
    }
}

if (isset($_POST['logout'])) {
    header("Location: logout.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = trim($_POST['amount']);
    $type = trim($_POST['type']);
    $category = intval($_POST['category']);
    $date = date('Y-m-d H:i:s');
    $description = trim($_POST['description']);

    if ($type !== "Income" && $type !== "Expense") {
        die("Invalid Type.");
    }
    if ($description === "") {
        die("Description cannot be empty.");
    }

    $query = mysqli_prepare($con, "
        UPDATE transaction 
        SET amount = ?, category_id = ?, datetime = ?, description = ?
        WHERE transaction_id = ?
    ");
    mysqli_stmt_bind_param($query, 'dissi', $amount, $category, $date, $description, $id);
    
    if (mysqli_stmt_execute($query)) {
        $status = 1;
    } 
    else {
        $status = 0;
    }

    header("Location: money_summary.php?status=$status");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Transaction Form</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow-x: hidden;
            }

            body {
                display: flex;
                align-items: center;
                padding-bottom: 30px;
                flex-direction: column;
                justify-content: space-between;
                font-family: "Audiowide", sans-serif;
                background-image: linear-gradient(to right, #7F7FD5, #86A8E7, #91EAE4);
            }

            a {
                text-decoration: none;
            }

            #header {
                width: 100%;
                height: 5vh;
                padding: 15px;
                display: flex;
                position: relative;
                align-items: center;
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

            #content {
                width: 500px;
                height: 650px;
                display: flex;
                margin-top: 50px;
                border-radius: 20px;
                align-items: center;
                flex-direction: column;
                background-color: white;
                border: 3px solid black;
                padding: 25px 20px 25px 20px;
                justify-content: space-between;
            }

            #form {
                width: 85%;
                display: flex;
                align-items: center;
                flex-direction: column;
                padding: 5px 10px 5px 10px;
                justify-content: space-between;
            }

            .field {
                width: 100%;
                display: flex;
                margin: 12.5px;
                flex-direction: column;
            }

            .labelText {
                color: #f15f79;
                margin-bottom: 2.5px;
            }

            .credentials {
                width: 100%;
                border: none;
                outline: none;
                cursor: pointer;
                font-size: 16px;
                box-sizing: border-box;
                background-color: transparent;
                border-bottom: 3px solid black;
                padding: 7.5px 15px 7.5px 15px;
            }

            .credentials::placeholder {
                font-size: 14px;
            }

            .credentials:focus {
                border-bottom: 3px solid #512da8;
            }

            .typeContainer {
                width: 100%;
                display: flex;
                align-items: center;
            }

            .transType {
                cursor: pointer;
                background-color: #eee;
            }

            .transType:hover {
                background-color: #ccc;
            }

            .select, .textarea {
                padding: 7.5px;
                border-radius: 5px;
                font-family: "Audiowide", sans-serif;
            }

            .textarea {
                resize: none;
                height: 100px;
                overflow-y: scroll;
            }

            .btnContainer {
                display: flex;
                margin-top: 20px;
                align-items: center;
                justify-content: space-between;
            }

            .btn {
                color: white;
                width: 150px;
                border: none;
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                margin: 0 7.5px 0 7.5px;
                padding: 10px 15px 10px 15px;
                font-family: "Audiowide", sans-serif;
            }

            .btn:hover {
                filter: brightness(120%);
            }

            .btn:nth-child(1) {
                background-color: #f15f79;
            }

            .btn:nth-child(2) {
                background-color: #673ab7;
            }

            .btn:nth-child(1):hover {
                filter: drop-shadow(0 0 5px #f15f79);
            }

            .btn:nth-child(2):hover {
                filter: drop-shadow(0 0 5px #673ab7);
            }
        </style>

    </head>
    <body>
        <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px; margin-left: 15px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div id="links">
                <a href="index.php">Homepage</a>
                <a href="">Exercise</a>
                <a href="">Dairy</a>
                <a href="">Habit</a>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="logout" value="Logout" class="logout" style="margin-right: 15px;" onclick="return confirm('Are you sure you want to logout?')">
                </form>
            </div>
        </div>

        <div id="content">
            <span style="font-size: 36px; color: #673ab7;">Update A Transaction</span>
            <form action="" method="POST" id="form">
                <div class="field">
                    <span class="labelText">Amount (RM):</span>
                    <input type="number" name="amount" min="0" step="0.01" class="credentials" value="<?php echo $row['amount']?>" required>
                </div>
                <div class="field">
                    <span class="labelText">Type:</span>
                    <div class="typeContainer" style="margin-bottom: 5px;">
                        <input type="radio" name="type" value="Income" class="transType" <?php if ($row['type'] == "Income") echo "checked"?> required><span>Income</span>
                    </div>
                    <div class="typeContainer">
                        <input type="radio" name="type" value="Expense" class="transType" <?php if ($row['type'] == "Expense") echo "checked"?> required><span>Expense</span>
                    </div>
                </div>
                <div class="field">
                    <span class="labelText">Category:</span>
                    <select name="category" class="select" required>
                        <?php foreach ($options as $id => [$label, $class]): ?>
                            <option class="<?php echo htmlspecialchars($class); ?>" value="<?php echo $id; ?>"
                                <?php if ($selectedCategory == $id) echo "selected"; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <span class="labelText">Description:</span>
                    <textarea name="description" class="textarea" required><?php echo $row['description']?></textarea>
                </div>
                <div style="text-align: center;" class="btnContainer">
                    <button type="submit" class="btn">UPDATE</button>
                    <button type="button" class="btn"><a href="money_summary.php" style="color: white;">CANCEL</a></button>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let count = 0;
                const radios = document.querySelectorAll(".transType");

                function updateOptions(value, count) {
                    const hideClass = value === "Income" ? ".Expense" : ".Income";
                    const showClass = value === "Income" ? ".Income" : ".Expense";

                    document.querySelectorAll(hideClass).forEach(option => {
                        option.style.display = "none";
                    });

                    document.querySelectorAll(showClass).forEach(option => {
                        option.style.display = "";

                        if (count > 0) {
                            let select = document.querySelector(".select");

                            if (showClass == ".Income" && option.value == "1") {
                                select.value = option.value;
                            }

                            if (showClass == ".Expense" && option.value == "5") {
                                select.value = option.value;
                            }   
                        } 
                    });
                }

                radios.forEach(radio => {
                    radio.addEventListener('click', () => {
                        updateOptions(radio.value, count);
                    });
                });

                const selected = document.querySelector(".transType:checked");
                if (selected) {
                    updateOptions(selected.value, count);
                    count++;
                }
            });
        </script>
    </body>
</html>