<?php
session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    header("Location: /signin.php");
    exit();
}
$uname = htmlspecialchars($_SESSION['uname']);

// Initialize decisions array in session if not already set

if (!isset($_SESSION['trusts'])) {
    $_SESSION['trusts'] = 0;
}

if (!isset($_SESSION['betrays'])) {
    $_SESSION['betrays'] = 0;
}

if (!isset($_SESSION['user_decisions'])) {
    $_SESSION['user_decisions'] = [];
}

if (!isset($_SESSION['merlin_decisions'])) {
    $_SESSION['merlin_decisions'] = [];
}

if (isset($_POST['decide_true'])) {
    $_SESSION['user_decisions'][] = true;
    $_SESSION['trusts']++;
}

if (isset($_POST['decide_false'])) {
    $_SESSION['user_decisions'][] = false;
    $_SESSION['betrays']++;
}

if (isset($_POST['reset'])) {
    $_SESSION['user_decisions'] = [];
    $_SESSION['betrays'] = 0;
    $_SESSION['trusts'] = 0;
}

function test($merlin_decisions, $user_decisions) {
    $u = $binaryString = implode('', array_map(function($value) {
        return $value ? '1' : '0';
    }, $_SESSION['user_decisions']));
    $m = $binaryString = implode('', array_map(function($value) {
        return $value ? '1' : '0';
    }, $_SESSION['merlin_decisions']));

    $command = 'python3 /var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/wwmd.py ' . escapeshellarg($u) . ' ' . escapeshellarg($o);
    $decision = exec($command);

    $_SESSION['merlin_decisions'][] = $decision == '1';
}

?>
<html>
    <head>
        <link rel="stylesheet" href="main.css">
        <link rel="stylesheet" href="profile.css">
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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
            <form method="post">
                <button class="go" type="submit" name="decide_true">Trust</button>
            </form>
            <form method="post">
                <button class="go" type="submit" name="decide_false">Betray</button>
            </form>
            <h1>Merlin's most recent decision: <?php
                if (empty($_SESSION['merlin_decisions'])) {
                    echo "Merlin hasn't made any decisions yet...";
                } else {
                    echo end($_SESSION['merlin_decisions']);
                }
             ?></h1>
            <form method="post">
                <button class="go" type="submit" name="reset">Reset</button>
            </form>
        </div>
    </body>
</html>
