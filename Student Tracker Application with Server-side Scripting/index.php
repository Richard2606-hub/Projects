<?php
include "authentication.php";

if (isset($_POST['logout'])) {
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Homepage</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
            }

            body {
                font-family: "Audiowide", sans-serif;
            }

            #wave {
                top: 0;
                left: 0;
                right: 0;
                z-index: -1;
                width: 100%;
                height: 700px;
                position: fixed;
            }

            a {
                text-decoration: none;
                font-family: "Audiowide", sans-serif;
            }

            #header {
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
                top: 50%;
                left: 50%;
                display: flex;
                position: fixed;
                align-items: center;
                justify-content: space-between;
                transform: translate(-50%, -50%);
            }

            #welcome {
                width: 650px;
                margin-right: 100px;
            }

            #content h1,  #content h2 {
                color: #512da8;
            }

            #content h1 {
                font-size: 52px;
            }

            #content h2 {
                font-size: 36px;
            }

            #modules {
                display: flex;
                width: inherit;
                margin-top: 75px;
                align-items: center;
                justify-content: space-between;
            }

            #modules > button {
                width: 150px;
                border: none;
                height: 150px;
                display: flex;
                cursor: pointer;
                border-radius: 10px;
                align-items: center;
                flex-direction: column;
                justify-content: center;;
            }

            #modules img {
                width: 80px;
                height: 80px;
                margin-bottom: 15px;
            }

            #exercise, #dairy, #money, #habit {
                position: relative;
            }

            #exercise {
                background-color: #f15f79;
            }

            #dairy {
                background-color: #009FFF;
            }

            #money {
                background-color: #6be585;
            }

            #habit {
                background-color: #f5af19;
            }

            #modules > button:hover {
                filter: brightness(125%);
            }

            #modules > button::before {
                inset: 0;
                z-index: -1;
                content: "";
                position: absolute;
                border-radius: 10px;
            }

            #exercise::before {
                background-color: #f15f79;
            }

            #dairy::before {
                background-color: #009FFF;
            }

            #money::before {
                background-color: #6be585;
            }

            #habit::before {
                background-color: #f5af19;
            }

            #modules > button:hover::before {
                filter: blur(5px);
            }

            #modules span {
                color: white;
                font-family: "Audiowide", sans-serif;
            }
        </style>

    </head>
    <body>
        <svg id="wave" preserveAspectRatio="none" style="transform:rotate(180deg); transition: 0.3s" viewBox="0 0 1440 490" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="sw-gradient-0" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#7F7FD5" />
                    <stop offset="33%" stop-color="#86A8E7" />
                    <stop offset="66%" stop-color="#91EAE4" />
                    <stop offset="100%" stop-color="#91EAE4" />
                </linearGradient>
            </defs>
            <path style="transform:translate(0, 0px); opacity:1" fill="url(#sw-gradient-0)" d="M0,441L60,408.3C120,376,240,310,360,245C480,180,600,114,720,98C840,82,960,114,1080,106.2C1200,98,1320,49,1440,57.2C1560,65,1680,131,1800,179.7C1920,229,2040,261,2160,236.8C2280,212,2400,131,2520,98C2640,65,2760,82,2880,106.2C3000,131,3120,163,3240,179.7C3360,196,3480,196,3600,204.2C3720,212,3840,229,3960,196C4080,163,4200,82,4320,40.8C4440,0,4560,0,4680,24.5C4800,49,4920,98,5040,106.2C5160,114,5280,82,5400,89.8C5520,98,5640,147,5760,212.3C5880,278,6000,359,6120,375.7C6240,392,6360,343,6480,310.3C6600,278,6720,261,6840,212.3C6960,163,7080,82,7200,40.8C7320,0,7440,0,7560,0C7680,0,7800,0,7920,65.3C8040,131,8160,261,8280,285.8C8400,310,8520,229,8580,187.8L8640,147L8640,490L8580,490C8520,490,8400,490,8280,490C8160,490,8040,490,7920,490C7800,490,7680,490,7560,490C7440,490,7320,490,7200,490C7080,490,6960,490,6840,490C6720,490,6600,490,6480,490C6360,490,6240,490,6120,490C6000,490,5880,490,5760,490C5640,490,5520,490,5400,490C5280,490,5160,490,5040,490C4920,490,4800,490,4680,490C4560,490,4440,490,4320,490C4200,490,4080,490,3960,490C3840,490,3720,490,3600,490C3480,490,3360,490,3240,490C3120,490,3000,490,2880,490C2760,490,2640,490,2520,490C2400,490,2280,490,2160,490C2040,490,1920,490,1800,490C1680,490,1560,490,1440,490C1320,490,1200,490,1080,490C960,490,840,490,720,490C600,490,480,490,360,490C240,490,120,490,60,490L0,490Z"></path>
            <defs>
                <linearGradient id="sw-gradient-1" x1="0" x2="0" y1="1" y2="0">
                    <stop offset="0%" stop-color="#7F7FD5" />
                    <stop offset="33%" stop-color="#86A8E7" />
                    <stop offset="66%" stop-color="#91EAE4" />
                    <stop offset="100%" stop-color="#91EAE4" />
                </linearGradient>
            </defs>
            <path style="transform:translate(0, 50px); opacity:0.9" fill="url(#sw-gradient-1)" d="M0,245L60,204.2C120,163,240,82,360,73.5C480,65,600,131,720,147C840,163,960,131,1080,98C1200,65,1320,33,1440,16.3C1560,0,1680,0,1800,16.3C1920,33,2040,65,2160,130.7C2280,196,2400,294,2520,277.7C2640,261,2760,131,2880,98C3000,65,3120,131,3240,196C3360,261,3480,327,3600,302.2C3720,278,3840,163,3960,106.2C4080,49,4200,49,4320,114.3C4440,180,4560,310,4680,310.3C4800,310,4920,180,5040,122.5C5160,65,5280,82,5400,81.7C5520,82,5640,65,5760,65.3C5880,65,6000,82,6120,130.7C6240,180,6360,261,6480,285.8C6600,310,6720,278,6840,236.8C6960,196,7080,147,7200,163.3C7320,180,7440,261,7560,294C7680,327,7800,310,7920,285.8C8040,261,8160,229,8280,253.2C8400,278,8520,359,8580,400.2L8640,441L8640,490L8580,490C8520,490,8400,490,8280,490C8160,490,8040,490,7920,490C7800,490,7680,490,7560,490C7440,490,7320,490,7200,490C7080,490,6960,490,6840,490C6720,490,6600,490,6480,490C6360,490,6240,490,6120,490C6000,490,5880,490,5760,490C5640,490,5520,490,5400,490C5280,490,5160,490,5040,490C4920,490,4800,490,4680,490C4560,490,4440,490,4320,490C4200,490,4080,490,3960,490C3840,490,3720,490,3600,490C3480,490,3360,490,3240,490C3120,490,3000,490,2880,490C2760,490,2640,490,2520,490C2400,490,2280,490,2160,490C2040,490,1920,490,1800,490C1680,490,1560,490,1440,490C1320,490,1200,490,1080,490C960,490,840,490,720,490C600,490,480,490,360,490C240,490,120,490,60,490L0,490Z"></path>
        </svg>

        <div id="header">
            <div>
                <span style="color:floralwhite; font-size: 30px;">Focus</span><span style="color:darkgreen; font-size: 30px;">Track</span>
            </div>

            <div>
                <form action="" method="POST">
                    <input type="submit" name="logout" value="Logout" class="logout" onclick="return confirm('Are you sure you want to logout?')">
                </form>
            </div>
        </div>

        <div id="content">
            <div id="welcome">
                <h1>Welcome,<br><span style="color: #f15f79"><?php echo $_SESSION['username']; ?>!</span></h1>
                <h2>Start organizing and managing your routines today!</h2>
                <div id="modules">
                    <button type="button" id="exercise">
                        <img src="running.png">
                        <span>Exercise Tracker</span>
                    </button>

                    <button type="button" id="dairy">
                        <img src="dairy.png">
                        <span>Dairy Tracker</span>
                    </button>

                    <button type="button" id="money">
                        <img src="money-bag.png">
                        <span>Money Tracker</span>
                    </button>

                    <button type="button" id="habit">
                        <img src="lifestyle.png">
                        <span>Habit Tracker</span>
                    </button>
                </div>
            </div>
            <div id="image">
                <img width="525px" height="525px" src="schedule.svg">
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.getElementById("exercise").onclick = () => {
                    window.location.href = "view_exercise.php";
                };

                document.getElementById("dairy").onclick = () => {
                    window.location.href = "view_daily_journal.php";
                };

                document.getElementById("money").onclick = () => {
                    window.location.href = "money_summary.php";
                };

                document.getElementById("habit").onclick = () => {
                    window.location.href = "habit_today.php";
                };
            });
        </script>
    </body>
</html>