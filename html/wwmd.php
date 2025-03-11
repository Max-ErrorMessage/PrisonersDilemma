<?php
session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    header("Location: /signin.php");
    exit();
}

$uname = htmlspecialchars($_SESSION['uname']);

// Initialize decisions array in session if not already set
if (!isset($_SESSION['user_decisions'])) {
    $_SESSION['user_decisions'] = [];
}

if (isset($_POST['decide_true'])) {
    $_SESSION['user_decisions'][] = true;
}
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css">
        <link rel="stylesheet" href="profile.css">
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>

        <title>Twokie - WWMD?</title>
    </head>
    <body>
        <div id="NavBar">
            <img src="images/twokielogo.png" id="navbarLogo">
            <a href="index.php" class="nav-link">Home</a>
            <a href="leaderboards.php" class="nav-link">Leaderboards</a>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
        </div>
        <div id="Main">
            <br><br>
            <h1>What Would Merlin Do?</h1>
        </div>
        <div id="Game">
            <h1>Trusts: <?php
                echo $array_count_values($_SESSION['user_decisions']);[true] ?? 0;
            ?></h1>
            <h1>Betrayals: <?php
                echo count($_SESSION['user_decisions']);
            ?></h1>
            <form method="post">
                <button type="submit" name="decide_true">Trust</button>
            </form>
        </div>
    </body>
</html>
