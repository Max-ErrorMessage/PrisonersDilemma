<?php

/**
 * Allows users to submit code for Yahtzee submissions.
 * Based on newSubmission.php
 *
 * Author: James Aris
 */

session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
    header("Location: /signin.php");
    exit();
}

//If 'uname' is set, display the welcome message
$uname = htmlspecialchars($_SESSION['uname']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twokie - New Submission</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="submission.css">
    <script type="module">
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
    </script>
    
</head>

<code>
<div id="title">Yahtzee:</div>
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
            <p>First box: reroll function<br>
            &emsp;     - This function will be called when you need to reroll dice<br>
            &emsp;     - It should return a list of the dice you want to reroll<br>
            &emsp;     - It should return a list of the dice you want to reroll<br>
            &emspl     - The list should contain the indexes of the dice you want to reroll</p>
            <p>Second box: select function<br>
            &emsp;     - This function will be called when you need to select a move after rolling<br>
            &emsp;     - It should return the move you want to make</p>
        </div>
        <div class="box">
            <h1>Variables:</h1>
            <div id="var">dice:</div>
            <p> &emsp;     - an array of your current dice</p>
            <div id="var">availability:</div>
            <p> &emsp;     - a dictionary containing the availability of each move<br>
            (key is the move, value is a boolean)</p>
            <div id="var">available_points:</div>
            <p> &emsp;     - a dictionary containing the amount of points each move would score</p>
            <div id="var">claimed_points:</div>
            <p> &emsp;     - a dictionary containing the amount of points each scored move<br>
            (key is the move, value is the points)<br>
            list of keys:<br>
            &emsp;      - "Yahtzee", "Large Straight", "Full House", "Small Straight", "4 of a Kind", <br>
            &emsp;      "3 of a Kind", "Sixes", "Fives", "Fours", "Threes", "Twos", "Ones", "Chance"</p>
        </div>
    </div>
    <div class="container">
        <div class="box2">
            <form action="submission.php" method="POST">
                <label for="name">Enter your reroll function!:</label>
                <textarea id="name" name="code" is="highlighted-code" placeholder="return []   #reroll no dice" onkeydown="return stopTab(event);" required></textarea>
                <label for="name">Enter your select function!:</label>
                <textarea id="name2" name="code2" is="highlighted-code" placeholder="return choices[0]   #returns first available move" onkeydown="return stopTab(event);" required></textarea>
                <input type="hidden" name="game_id" value=2>
                <button type="submit" name="submitCode" value="submit">Submit</button>
            </form>
        </div>
        <div class="box" id="php"><?php
            session_start();
            if(isset($_SESSION['Error3'])){
                echo "<h2>" . $_SESSION['Error3'] . "</h2>";
            } 
        ?></div>
    </div>
</body>
    <h1>
        Example bot:
    </h1>
    <pre id="example" is="highlighted-code" language="python">counts = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0}
dice_to_reroll = []
for i in dice:
    counts[i] += 1
max_key = max(counts, key=counts.get)
for i in range(0,5):
    if dice[i] != max_key:
        dice_to_reroll.append(i)
return dice_to_reroll

max_key = max(available_points, key=available_points.get)
if max_key in choices:
    return max_key
else:
    return choices[0]
    </pre>
</div>
</code>
</html>



