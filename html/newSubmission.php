<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uname'])) {
   header("Location: /signin.php");
   exit();
}


$uname = htmlspecialchars($_SESSION['uname']);
if(!isset($_SESSION['code'])){
    $txt = "";
} else {
    $txt = $_SESSION['code'];
}  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Twokie - New Submission</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="submission.css">
    <!-- <script type="module">
        (async ({chrome, netscape}) => {

            // add Safari polyfill if needed
            if (!chrome && !netscape)
                await import('https://unpkg.com/@ungap/custom-elements');

            const {default: HighlightedCode} =
                await import('https://unpkg.com/highlighted-code');

            // bootstrap a theme through one of these names
            // https://github.com/highlightjs/highlight.js/tree/main/src/styles
            HighlightedCode.useTheme('github-dark');
        })(self);
    </script> -->
</head>
<code>
<div id="title">Prisoner's Dilemma:</div>
<body>
    <div id="NavBar">
        <img src="images/twokielogo.png" id="navbarLogo">
        <a href="index.php" class="nav-link">Home</a>
        <a href="leaderboards.php" class="nav-link">Leaderboards</a>
        <a href="wwmd.php" class="nav-link">WWMD?</a>
        <a href="profile.php" class="nav-link">My Profile</a>
        <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
    </div>
    <div class="container">
        <div class="box">
            <h1>Your task:</h1>
            <p>- Write a function that returns a boolean value<br>
            &emsp; True == trust<br>
            &emsp; False == betray
            </p>
            <p>- You will play multiple games in a row with the same bot<br>
            &emsp; - Aim for the highest score!<br>
            &emsp; - Your bot will be pitted against every other bot<br>
            &emsp; - Try to do as well as possible against the whole field!<br>
            </p>
        </div>
        <div class="box">
            <h1>Variables:</h1>
            <div id="var">self_decisions:</div>
            <p> &emsp;  - an array containing all previous decisions your bot has made (also accessible with 's')</p>
            <div id="var">opponent_decisions:</div>
            <p> &emsp; - an array containing all previous decisions your opponent's bot has made (also accessible with 'o')</p>
            <div id="var">n:</div>
            <p>   - the number of previous moves made</p>
        </div>
    </div>

    <div class="container">
        <div class="box2">
            <form action="submission.php" method="POST">
                <label for="name">Enter your code!:</label>
                <textarea id="name" name="code" required value="<?= $txt ?>" placeholder="return True


# The input field is in Python, which cares about indentation. Here, just use 4 spaces as a substitute for <TAB>" onkeydown="return stopTab(event);"></textarea>
                <input type="hidden" name="game_id" value="1">
                <button type="submit" name="submitCode" value="submit">Submit</button>
            </form>
        </div>
        <div class="box" id="php">
            <?php
                if(isset($_SESSION['Error3'])){
                    echo "<h2>" . $_SESSION['Error3'] . "</h2>";
                }
            ?>
        </div>
    </div>
</body>

    <h1>
        Example bot:
    </h1>
    <pre id='example'>if n > 0:
    return opponent_decisions[-1]
else:
    return False</pre>
</code>
</html>
