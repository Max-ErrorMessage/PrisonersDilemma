<?php
session_start();

// Check if the session variable 'uname' is set
//if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
//    header("Location: /signin.php");
//    exit();
//}

// If 'uname' is set, display the welcome message
//$uname = htmlspecialchars($_SESSION['uname']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twokie - New Submission</title>
    <link rel="stylesheet" href="main.css">
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
            left: 70px;
            background: #003300;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            align-items: stretch; /* Stretch to the form's width */
            width: 100%;
            max-width: 700px; /* Wider form to fit the longer input box */
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        textarea {
            width: 100%; /* Fill the form's width */
            max-width: 680px; /* Wider input box */
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
            text-align: left;
        }
        #info {
            position: absolute;
            top: 160px;
            left: 910px;
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
        }
        #php{
            position: absolute;
            top: 1530px;
            left: 100px;
            padding: 40px;
        }
        #example{
            text-align: left;
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

    <form action="submission.php" method="POST">
        <label for="name">Enter your reroll function!:</label>
        <textarea id="name" name="code" placeholder="return []   #reroll no dice" onkeydown="return stopTab(event);" required></textarea>
        <label for="name">Enter your select function!:</label>
        <textarea id="name2" name="code2" placeholder="return choices[0]   #returns first available move" onkeydown="return stopTab(event);" required></textarea>
        <input type="hidden" name="game_id" value=2>
        <button type="submit" name="submitCode" value="submit">Submit</button>
    </form>

    <div class="box" id="php"><?php
        session_start();
        if(isset($_SESSION['Error3'])){
            echo "<h2>" . $_SESSION['Error3'] . "</h2>";
        } 
    ?></div>
</body>
<div id="info">
    <h1>Your task:</h1>
    <pre>
First box: reroll function
    - This function will be called when you need to reroll dice
    - It should return a list of the dice you want to reroll
    - The list should contain the indexes of the dice you want to reroll

Second box: select function
    - This function will be called when you need to select a move after rolling
    - It should return the move you want to make
    </pre>
    <h1>
        Variables:
    </h1>
    <div id="var">dice:</div>
    <pre>   - an array of your current dice</pre>
    <div id="var">availability:</div>
    <pre>   - a dictionary containing the availability of each move
    (key is the move, value is a boolean)</pre>
    <div id="var">available_points:</div>
    <pre>   - a dictionary containing the amount of points each move would score
    <div id="var">claimed_points:</div>
    <pre>   - a dictionary containing the amount of points each scored move
    (key is the move, value is the points)</pre>
    <pre>
list of keys:
    - "Yahtzee", "Large Straight", "Full House", "Small Straight", "4 of a Kind", 
    "3 of a Kind", "Sixes", "Fives", "Fours", "Threes", "Twos", "Ones", "Chance"
    </pre>
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



