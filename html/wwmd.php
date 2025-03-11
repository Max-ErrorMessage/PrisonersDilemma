<?php
session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
    header("Location: /signin.php");
    exit();
}

// If 'uname' is set, display the welcome message
$uname = htmlspecialchars($_SESSION['uname']); 
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
            <br>
            <p>Username: <?php echo $uname; ?></p>
        </div>
        <div id="Game">

        </div>

    </body>
</html>
