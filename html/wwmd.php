<?php
session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    header("Location: /signin.php");
    exit();
}
$uname = htmlspecialchars($_SESSION['uname']);

// Initialize decisions array in session if not already set

if (!isset($_SESSION['user_score'])) {
    $_SESSION['user_score'] = 0;
}

if (!isset($_SESSION['merlin_score'])) {
    $_SESSION['merlin_score'] = 0;
}

if (!isset($_SESSION['user_decisions'])) {
    $_SESSION['user_decisions'] = [];
}

if (!isset($_SESSION['merlin_decisions'])) {
    $_SESSION['merlin_decisions'] = [];
}

if (isset($_POST['decide_true'])) {
    wwmd();
    $_SESSION['user_decisions'][] = true;
}

if (isset($_POST['decide_false'])) {
    wwmd();
    $_SESSION['user_decisions'][] = false;
}

if (isset($_POST['reset'])) {
    $_SESSION['user_decisions'] = [];
    $_SESSION['merlin_decisions'] = [];
    $_SESSION['betrays'] = 0;
    $_SESSION['trusts'] = 0;
    $_SESSION['user_score'] = 0;
    $_SESSION['merlin_score'] = 0;
}

function wwmd() {
    $u = $binaryString = implode('', array_map(function($value) {
        return $value ? '1' : '0';
    }, $_SESSION['user_decisions']));
    $m = $binaryString = implode('', array_map(function($value) {
        return $value ? '1' : '0';
    }, $_SESSION['merlin_decisions']));

    $command = 'python3 /var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/wwmd.py ' . escapeshellarg($m) . ' ' . escapeshellarg($u);
    $merlin_decision = exec($command);

    $_SESSION['merlin_decisions'][] = $merlin_decision;

    $user_decision = end($_SESSION['merlin_decisions']);

    if ($user_decision && $merlin_decision) {
        $_SESSION['user_score'] += 5;
        $_SESSION['user_score'] += 5;
    } else if ($user_decision && !$merlin_decision) {
        $_SESSION['user_score'] -= 1;
        $_SESSION['user_score'] += 10;
    } else if (!$user_decision && $merlin_decision) {
        $_SESSION['user_score'] += 10;
        $_SESSION['user_score'] -= 1;
    }
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
            <h1>Your Score: <?php
                echo $_SESSION['user_score'];
            ?></h1>
            <h1>Merlin's Score: <?php
                echo $_SESSION['merlin_score'];
            ?></h1>
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
                    if (end($_SESSION['merlin_decisions'])) {
                        echo "Trust";
                    } else {
                        echo "Betray";
                    }
                }
             ?></h1>
            <form method="post">
                <button class="go" type="submit" name="reset">Reset</button>
            </form>
        </div>
    </body>
</html>
