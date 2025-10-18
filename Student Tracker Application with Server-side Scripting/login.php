<?php
require "database.php";

session_start();

$timeout_message = "";
$reset_status = "";

if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = "Your session has expired. Please login again.";
}

if (isset($_GET['reset_success']) && $_GET['reset_success'] == 1) {
    $reset_status = "Password successfully reset.";
}

$_SESSION['last_request_time'] = time();

if (isset($_POST['register'])) {
    header("Location: register.php");
    exit();
}

if (isset($_COOKIE['remember_token'])) {
    $query = mysqli_prepare($con, "SELECT * FROM users WHERE remember_token = ?");
    mysqli_stmt_bind_param($query, 's', $_COOKIE['remember_token']);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];

        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($con, $username);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con, $password);

    $query = "SELECT * FROM users
    WHERE username = '$username'
    AND password = '" . md5($password) . "'";
    $result = mysqli_query($con, $query) or die(mysqli_error($con));
    $rows = mysqli_num_rows($result);

    if ($rows == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];

        if (isset($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32));
            $expiration_time = time() + (60 * 60 * 24 * 30);
            setcookie("remember_token", $token, $expiration_time, "/", "", true, true);

            $query = mysqli_prepare($con, "UPDATE users SET remember_token = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($query, 'si', $token, $row['user_id']);
            mysqli_stmt_execute($query);
        }

        header("Location: index.php");
        exit();
    }
    else {
        echo "<script>alert('Username or Password is incorrect.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>

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

            .register {
                cursor: pointer;
                font-size: 16px;
                border-radius: 15px;
                border: medium solid black;
                background-color: transparent;
                padding: 7.5px 15px 7.5px 15px;
                font-family: "Audiowide", sans-serif;
            }

            .register:hover {
                color: white;
                border: none;
                background-color: navy;
                padding: 10px 17.5px 10px 17.5px;
            }

            #content {
                top: 50%;
                left: 50%;
                width: 900px;
                height: 500px;
                display: flex;
                position: fixed;
                align-items: center;
                border-radius: 20px;
                transform: translate(-50%, -50%);
            }

            #profile, #description {
                width: 50%;
                display: flex;
                padding: 20px;
                height: inherit;
                align-items: center;
                flex-direction: column;
                justify-content: center;
                padding: 10px 20px 10px 20px;
            }

            #profile {
                color: white;
                align-items: center;
                border-top-left-radius: 20px;
                border-bottom-left-radius: 20px;
                background: linear-gradient(to right, #512da8, #673ab7);
            }

            #description {
                background-color: white;
                border-top-right-radius: 20px;
                border-bottom-right-radius: 20px;
            }

            #appdesc {
                padding: 15px;
                font-size: 14px; 
                color: white; 
                text-align: justify; 
                text-justify: inter-word;
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

            .options, #rememberMe {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #signIn {
                color: white;
                width: 200px;
                border: none;
                cursor: pointer;
                font-size: 16px;
                margin-top: 30px;
                border-radius: 15px;
                background-color: #f15f79;
                padding: 10px 15px 10px 15px;
                font-family: "Audiowide", sans-serif;
            }

            #signIn:hover {
                filter: brightness(120%) drop-shadow(0 0 5px #f15f79);
            }

            .timeout {
                top: 35px;
                left: 50%;
                z-index: 2;
                color: white;
                width: 400px;
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
        if (!empty($timeout_message)) {
            ?>
            <div class="timeout" style="background-color: red;">
                <span style="font-size: 14px;"><?php echo $timeout_message; ?></span>
            </div>
            <meta http-equiv="refresh" content="2;url=<?php echo $_SERVER['PHP_SELF']; ?>">
            <?php
        }

        if (!empty($reset_status)) {
            ?>
            <div class="timeout" style="background-color: green;">
                <span style="font-size: 14px;"><?php echo $reset_status; ?></span>
            </div>
            <meta http-equiv="refresh" content="2;url=<?php echo $_SERVER['PHP_SELF']; ?>">
            <?php
        }
        ?>

        <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="register" value="Register" class="register">
                </form>
            </div>
        </div>

        <div id="content">
            <div id="profile">
                <span style="font-size: 24px;">Hello, Welcome!</span>
                <p id="appdesc">
                    FocusTrack is a platform for users, especially students to efficiently manage and improve their daily routines in one place. 
                    Users will be able to take advantage of four key tools: Exercise Tracker, Diary Journal, Money Tracker, and Habit Tracker.
                    Create an account and try it out today.
                </p>
            </div>

            <div id="description">
                <span style="font-size: 36px">Sign In</span>
                <form action="" method="POST" id="form">
                    <p>
                        <input type="text" name="username" class="credentials" placeholder="Username" size="30" required>
                    </p>
                    <p>
                        <input type="password" name="password" class="credentials" placeholder="Password" size="30" required>
                    </p>
                    <div class="options">
                        <div id="rememberMe">
                            <input type="checkbox" name="remember_me" value="remember"><span style="font-size: 12px">Remember Me</span>
                        </div>
                        <div>
                            <a href="forgot_password.php" style="font-size: 12px">Forgot password?</a>
                        </div>
                    </div>
                    <p style="text-align: center;">
                        <button id="signIn">SIGN IN</button>
                    </p>
                </form>
                <span style="font-size: 16px">Don't have an account? <a href="register.php">Register</a></span>
            </div>
        </div>
    </body>
</html>