<?php
require "database.php";
include "authentication.php";

$status = "";
$color = "";

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $status = "Submission Successful!";
    $color = "green";
}

if (isset($_POST['logout'])) {
    header("Location: logout.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];

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

    $query = mysqli_prepare($con, "INSERT INTO transaction (user_id, amount, category_id, datetime, description) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($query, 'idiss', $user_id, $amount, $category, $date, $description);
    
    if (mysqli_stmt_execute($query)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } 
    else {
        $status = "Submission Failed!";
        $color = "red";
    }
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
            }

            body {
                display: flex;
                overflow-x: hidden;
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
                overflow-y: auto;
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
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div id="links">
                <a href="index.php">Homepage</a>
                <a href="">Exercise</a>
                <a href="">Dairy</a>
                <a href="">Habit</a>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="logout" value="Logout" class="logout" onclick="return confirm('Are you sure you want to logout?')">
                </form>
            </div>
        </div>

        <div id="content">
            <span style="font-size: 36px; color: #673ab7;">Submit A Transaction</span>
            <form action="" method="POST" id="form">
                <div class="field">
                    <span class="labelText">Amount (RM):</span>
                    <input type="number" name="amount" min="0" step="0.01" class="credentials" required>
                </div>
                <div class="field">
                    <span class="labelText">Type:</span>
                    <div class="typeContainer" style="margin-bottom: 5px;">
                        <input type="radio" name="type" value="Income" class="transType" checked required><span>Income</span>
                    </div>
                    <div class="typeContainer">
                        <input type="radio" name="type" value="Expense" class="transType" required><span>Expense</span>
                    </div>
                </div>
                <div class="field">
                    <span class="labelText">Category:</span>
                    <select name="category" class="select" required>
                        <option class="Income" value="1">Allowance</option>
                        <option class="Income" value="2">Scholarship</option>
                        <option class="Income" value="3">Part-time Job</option>
                        <option class="Income" value="4">Gift</option>
                        <option class="Expense" value="5">Food</option>
                        <option class="Expense" value="6">Bills</option>
                        <option class="Expense" value="7">Transport</option>
                        <option class="Expense" value="8">Entertainment</option>
                        <option class="Expense" value="9">Books & Supplies</option>
                        <option class="Expense" value="10">Health & Personal Care</option>
                    </select>
                </div>
                <div class="field">
                    <span class="labelText">Description:</span>
                    <textarea name="description" class="textarea" required></textarea>
                </div>
                <div style="text-align: center;" class="btnContainer">
                    <button type="submit" class="btn">SUBMIT</button>
                    <button type="reset" class="btn">RESET</button>
                </div>
            </form>
            <?php if (!empty($status)): ?>
                <div style="font-size: 16px; text-align: center; margin: 20px 0 10px 0; color: <?php echo $color; ?>;"><?php echo $status; ?></div>
            <?php endif; ?>
            <span style="font-size: 16px; margin-top: 10px;">Want to track your income and expenses? <a href="money_summary.php">View</a></span>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const radios = document.querySelectorAll(".transType");

                function updateOptions(value) {
                    const hideClass = value === "Income" ? ".Expense" : ".Income";
                    const showClass = value === "Income" ? ".Income" : ".Expense";

                    document.querySelectorAll(hideClass).forEach(option => {
                        option.style.display = "none";
                    });

                    document.querySelectorAll(showClass).forEach(option => {
                        option.style.display = "";

                        let select = document.querySelector(".select");

                        if (showClass == ".Income" && option.value == "1") {
                            select.value = option.value;
                        }

                        if (showClass == ".Expense" && option.value == "5") {
                            select.value = option.value;
                        }   
                    });
                }

                radios.forEach(radio => {
                    radio.addEventListener('click', () => {
                        updateOptions(radio.value);
                    });
                });

                const selected = document.querySelector(".transType:checked");
                if (selected) {
                    updateOptions(selected.value);
                }
            });
        </script>
    </body>
</html>