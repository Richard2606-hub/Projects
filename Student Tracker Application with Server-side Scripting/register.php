<?php
require "database.php";

$status = "";
$error = "";

if (isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($con, $username);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con, $password);
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($con, $email);

    // Check for duplicate username
    $checkUsername = "SELECT * FROM users WHERE username = '$username'";
    $resultUsername = mysqli_query($con, $checkUsername);

    // Check for duplicate email
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $resultEmail = mysqli_query($con, $checkEmail);

    if (mysqli_num_rows($resultUsername) > 0) {
        $error = "Username already exists. Please choose another.";
    } elseif (mysqli_num_rows($resultEmail) > 0) {
        $error = "Email already registered. Please use a different email.";
    } else {
        $query = "INSERT INTO users (username, password, email)
                  VALUES ('$username', '" . md5($password) . "', '$email')";
        mysqli_query($con, $query) or die(mysqli_error($con));
        $status = "Registration Successful!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>

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

            #register {
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

            #register:hover {
                filter: brightness(120%) drop-shadow(0 0 5px #f15f79);
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
                background-color: lawngreen;
            }
        </style>

    </head>
    <body>
        <?php if (!empty($error)) : ?>
            <div class="alert" style="background-color: crimson;">
                <span style="font-size: 14px;"><?php echo $error; ?></span>
            </div>
        <?php elseif (!empty($status)) : ?>
            <div class="alert">
                <span style="font-size: 14px;"><?php echo $status; ?></span>
            </div>
            <meta http-equiv="refresh" content="2;url=<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php endif; ?>

        <?php
        if (!empty($status)) {
            ?>
            <div class="alert">
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

            <div>
                <form action="" method="POST">
                    <input type="submit" name="login" value="Login" class="login">
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
                <span style="font-size: 36px">Sign Up</span>
                <form action="" method="POST" id="form">
                    <p>
                        <input type="text" name="username" class="credentials" placeholder="Username" required>
                    </p>
                    <p>
                        <input type="password" name="password" class="credentials" placeholder="Password" required>
                    </p>
                    <p>
                        <input type="email" name="email" class="credentials" placeholder="Email" required>
                    </p>
                    <p style="text-align: center;">
                        <button id="register">REGISTER</button>
                    </p>
                </form>
                <span style="font-size: 16px">Already have an account? <a href="login.php">Login</a></span>
            </div>
        </div>
    </body>
</html>