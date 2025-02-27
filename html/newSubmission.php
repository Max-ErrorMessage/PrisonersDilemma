<?php
//session_start();

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
    <title>New Submission</title>

    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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


        form {
            background: #003300;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            align-items: stretch; /* Stretch to the form's width */
            width: 100%;
            max-width: 500px; /* Wider form to fit the longer input box */
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        textarea {
            width: 100%; /* Fill the form's width */
            max-width: 480px; /* Wider input box */
            min-height: 180px; /* Increased starting height */
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
<body>
    <div id="NavBar">
        <img src="images/twokielogo.png" id="navbarLogo">
        <a href="index.php" class="nav-link">Home</a>
        <a href="leaderboards" class="nav-link">Leaderboards</a>
        <a href="profile.php" class="nav-link">My Profile</a>
        <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
    </div>

    <form action="submission.php" method="POST">
        <label for="name">Enter your code!:</label>
        <textarea id="name" name="code" required></textarea>
        <input type="hidden" name="game_id" value="1">
        <button type="submit" name="submitCode" value="submit">Submit</button>
    </form>
    <?php
        session_start();
        if(isset($_SESSION['Error3'])){
            echo "<h2>" . $_SESSION['Error3'] . "</h2>";
        } 
    ?>
    <script>
        textarea.addEventListener('keydown', event => {
            if (event.key === 'Tab') {
                const start = textarea.selectionStart
                const end = textarea.selectionEnd

                textarea.value = textarea.value.substring(0, start) + '\t' + textarea.value.substring(end)
                textarea.focus()

                event.preventDefault()
              }
            })
    </script>
</body>
</html>

