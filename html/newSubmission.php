<?php
session_start();

// Check if the session variable 'uname' is set
//if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
//    header("Location: /signin.php");
//    exit();
//}

// If 'uname' is set, display the welcome message
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
    <!--simple script that disables the user from the tab key while they are tabbed into the code box-->
    <script>
        function stopTab( e ) {
        var evt = e || window.event
        if ( evt.keyCode === 9 ) {
           return false
        }
}
    </script>
    <link rel="stylesheet" href="main.css">
</head>
<code>
<div id="title">Prisoner's Dilemma:</div>

<!--Right side includes all elements that want the background, left side is everything without-->
<!--right side form (includes nav)-->
<body>
    <div id="NavBar">
        <img src="images/twokielogo.png" id="navbarLogo">
        <a href="index.php" class="nav-link">Home</a>
        <a href="leaderboards.php" class="nav-link">Leaderboards</a>
        <a href="profile.php" class="nav-link">My Profile</a>
        <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
    </div>
    <form action="submission.php" method="POST">
        <label for="name">Enter your code!:</label>
        <textarea id="name" name="code" required value="<?= $txt ?>" placeholder="return True


# The input field is in Python, which cares about indentation. Here, just use 4 spaces as a substitute for <TAB>" onkeydown="return stopTab(event);"></textarea>
        <input type="hidden" name="game_id" value="1">
        <button type="submit" name="submitCode" value="submit">Submit</button>
    </form>
</body>
<div id="php">
<?php
        session_start();
        if(isset($_SESSION['Error3'])){
            echo "<h2>" . $_SESSION['Error3'] . "</h2>";
        }
    ?></div>
<!--left side of window (not including nav)-->
<div id="info">
    <h1>Your task:</h1>
    <pre>
- Write a function that returns a boolean value
    True == trust
    False == betray

- You will play multiple games in a row with the same bot
- Aim for the highest score!
- Your bot will be pitted against every other bot - try to do as well as possible against the whole field!
    </pre>
    <h1>
        Variables:
    </h1>
    <div id="var">self_decisions:</div>
    <pre>   - an array containing all previous decisions your
        bot has made (also accessible with 's')</pre>
    <div id="var">opponent_decisions:</div>
    <pre>   - an array containing all previous decisions your opponent's
        bot has made (also accessible with 'o')</pre>
    <div id="var">n:</div>
    <pre>   - the number of previous moves made</pre>
    <h1>
        Example bot:
    </h1>
    <pre id="example">if n > 0:
    return opponent_decisions[-1]
else:
    return False
    </pre>
</code>
</html>
