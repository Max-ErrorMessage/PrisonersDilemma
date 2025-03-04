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

    <style>

        body {
            background: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        nav {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #003300; /* Dark green */
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-size: 1rem;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        nav a:hover {
            background: #006600;
        }

        form {
        position: absolute;
        top: 250px;
        right: 70px;
        background: #003300;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 66.66%;
        max-width: none;
    }


        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        textarea {
            width: 90%; /* Fill the form's width */
            max-width: 1000px; /* Wider input box */
            min-height: 500px; /* Increased starting height */
            max-height: 600px; /* Larger maximum height */
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid white;
            border-radius: 5px;
            background: black;
            color: white;
            font-size: 1rem;
            resize: none; /* Prevent manual resizing */
            overflow-y: auto; /* Add vertical scroll when needed */
        }
        #info {
            position: absolute;
            top: 160px;
            left: 930px;
        }
        #title{
            position: absolute;
            top: 100px;
            left: 100px;
            font-size: 5rem;
        }
        #var{
            color:rgb(192, 97, 49);
            font-size: 1rem;
        }
        h1{
            color: rgb(91, 169, 107);
            font-size: 3rem;
        }
        pre{
            font-size: 1rem;
            text-align: left;
        }
        #php{
            position: absolute;
            top: 930px;
            left: 100px;
            padding: 40px;
        }

        #var {
            text-align:left;
        }

        #example {
            border: 2px #003300;
            padding: 10px;
            background: #222;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        button {
            background: #006600; /* Bright green */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            align-self: center; /* Center the button horizontally */
        }

        button:hover {
            background: #00cc00;
        }
    </style>
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
    <pre id='example'>if n > 0:
    return opponent_decisions[-1]
else:
    return False</pre>
</code>
</html>
