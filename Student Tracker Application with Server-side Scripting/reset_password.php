<?php
require "database.php";

session_start();

$status = "";

if (isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_REQUEST['token']);

    $query = "SELECT * FROM password_resets WHERE token = '$token' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result->num_rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['password'])) {
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $hashed_password = md5($password);

            mysqli_query($con, "UPDATE users SET password = '$hashed_password' WHERE email = '$email'");
            mysqli_query($con, "DELETE FROM password_resets WHERE email = '$email'");

            header("Location: login.php?reset_success=1");
            exit();
        }
    }
    else {
        $status = "Invalid or expired token.";
    }
}
else {
    $status = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
            }

            body {
                font-family: "Audiowide", sans-serif;
                background-image: linear-gradient(to right, #7F7FD5, #86A8E7, #91EAE4);
            }

            a {
                text-decoration: none;
            }

            #header {
                margin: 0;
                height: 5vh;
                padding: 15px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                
            }

            #header div {
                margin: 5px;
                padding: 5px;
                font-size: 24px;
            }

            .login {
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                border: medium solid black;
                background-color: transparent;
                padding: 7.5px 15px 7.5px 15px;
                font-family: "Audiowide", sans-serif;
            }

            .login:hover {
                color: white;
                border: none;
                background-color: navy;
                padding: 10px 17.5px 10px 17.5px;
            }

            #content {
                top: 50%;
                left: 50%;
                width: 400px;
                height: auto;
                padding: 20px;
                display: flex;
                position: fixed;
                align-items: center;
                border-radius: 20px;
                flex-direction: column;
                justify-content: center;
                border: 2px solid white;
                transform: translate(-50%, -50%);
                box-shadow: 0 0 10px white, 0 0 20px white;
                background-color: rgba(255, 255, 255, 0.5);
            }

            .credentials {
                border: none;
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                border: medium solid black;
                background-color: transparent;
                padding: 7.5px 15px 7.5px 15px;
            }

            .credentials::placeholder {
                font-size: 14px;
            }

            #verify {
                color: white;
                width: 200px;
                border: none;
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                background-color: #f15f79;
                padding: 10px 15px 10px 15px;
                font-family: "Audiowide", sans-serif;
            }

            #verify:hover {
                filter: brightness(120%) drop-shadow(0 0 5px #f15f79);
            }
        </style>

    </head>
    <body>
        <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="login" value="Login" class="login">
                </form>
            </div>
        </div>

        <div id="content">
            <span style="font-size: 32px; margin-bottom: 30px;">Reset Password</span>
            <form action="" method="POST" id="form">
                <div>
                    <input type="password" name="password" class="credentials" placeholder="Enter new passowrd" size="30" required>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button id="verify">CONFIRM</button>
                </div>
                <?php if (!empty($status)): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <span style="font-size: 14px; color: red"><?php echo $status; ?></span>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </body>
</html>