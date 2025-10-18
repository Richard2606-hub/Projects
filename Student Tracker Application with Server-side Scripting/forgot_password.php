<?php
require "database.php";

session_start();

$status = "";
$color = "";

if (isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['email'])) {
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($con, $email);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $query = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";

        $status = (mysqli_query($con, $query)) ? "Click <a href='reset_password.php?token=$token'>here</a> to reset your password." : "Database transaction failed.";
        $color = (mysqli_query($con, $query)) ? "green" : "red";
    }
    else {
        $status = "Email not found.";
        $color = "red";
    }
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
            <span style="font-size: 32px; margin-bottom: 30px;">Email Verification</span>
            <form action="" method="POST" id="form">
                <div>
                    <input type="email" name="email" class="credentials" placeholder="Email" size="30" required>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button id="verify">VERIFY</button>
                </div>
                <?php if (!empty($status)): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <span style="font-size: 14px; color: <?php echo $color; ?>"><?php echo $status; ?></span>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </body>
</html>