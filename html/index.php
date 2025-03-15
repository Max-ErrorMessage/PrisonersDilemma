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
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
	<meta name = "description" content = "Twokie Bots, Code bots to play minigames and compete on leaderboards">
        <meta name = "keywords" content = "prisoner's dilemma, prisoners dilemma, bots, yahtzee, python, code, coding, machine learning, ai">
	<title>Twokie - Home</Title>
        <style>
            
            #circleContainer {
                display: grid;
                grid-template-columns: repeat(10, 50px);
                grid-gap: 10px;
                width: 100%;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f0f0f0;
                border-radius: 8px;
            }
    
            .circle {
                width: 40px;
                height: 40px;
                background-color: gray;
                border-radius: 50%;
                transition: background-color 0s;
            }

            .rooks{
                width:60px;
                height:60px;
            }
            
            #rook{
                position:absolute;
                left:-150px;
            }
            #rook2{
                position:absolute;
                right:-150px;
            }
            #rook3{
                position:absolute;
                left:-125px;
            }
            #rook4{
                position:absolute;
                right:-125px;
            }
            #rook5{
                position:absolute;
                left:-100px;
            }
            .go{
                position:absolute;
                background-color:3f7652;
                padding:20px;
                border-radius:50px;
                left:50%;
                transform: translateX(-50%);
                text-decoration: none;
                color: white;
                border-bottom: 4px #183b2a solid;
            }
            .rpsCircleContainer {
              position: relative;
              width: 400px;
              height: 400px;
              margin: 0 auto;
            }

            .circleImage {
              position: absolute;
              top: 50%;
              left: 50%;
              width: 50px;
              height: 50px;
              transform-origin: -50px;
              transition: transform 0.1s linear;
            }
            
            
                
        </style>
    </head>
    <body>
        <div id="NavBar">
            <img src="images/twokielogo.png" id="navbarLogo">
            <a href="#" class="nav-link">Home</a>
            <a href="leaderboards.php" class="nav-link">Leaderboards</a>
            <a href="wwmd.php" class="nav-link">WWMD?</a>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
        </div>
        <div id="Main">
            <br><br>
            <h1>Twokie: Bot Playground</h1>
            <br>
            <p>Twokie: Bot Playground is a website designed for users to compete in various challenges, where the aim is to code Python bots to partake in different games and come up with new and innovative solutions.</p><br><br>
            <p>Each night, at midnight, each simulation will run. Agents will compete against each other, receive a score and the corresponding leaderboards will be updated.</p>
            <div id="PD">
                <h2>Prisoner's Dilemma</h2>
                <p>The Prisoner's Dilemma is a problem in Game Theory where two agents have to make a choice between personal cooperation for mutual gain or acting in their own self interest</p>
                <p>In this version of the Prisoner's Dilemma there will be 200 rounds where bots will get the chance to work out what their opponent's strategies and try to adapt to either exploit them or collaborate</p> <br>
                <div id="circleContainer">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                </div>
                <a class="go" href="newSubmission.php">Begin</a>
                <br><br><br><br>
            </div>
            <div id="Yahtzee">
                <h2>Yahtzee</h2>
                <p>The classic dice-based strategy game.</p>
                <p>In this challenge users will write 2 functions. One that, upon being given 5 dice, returns which ones you would like to reroll.</p>
                <p>Then after three rolls you have to choose how you are going to score these dice</p>
                <a class="go" href="newYahtzeeSubmission.php">Begin!</a>
                <br><br><br><br>
            </div>
            <div id="Chess">
		<!--
                <h2>Chess</h2>
                <p>We all know what chess is.</p>
                <p>What we wanted to test was how good can a chess bot get when we introduce a limit on the length of the code users can write</p>
                <p>In this challenge users are tasked to write chess bots that cannot be over [insert restriction]</p>
                -->
		<?php echo '<img class = "rooks" id = "rook" src = "images/dice'.rand(1,6).'.png"'; ?></img><br><br><br>
		<?php echo '<img class = "rooks" id = "rook2" src = "images/dice'.rand(1,6).'.png"'; ?></img><br><br><br>
		<?php echo '<img class = "rooks" id = "rook3" src = "images/dice'.rand(1,6).'.png"'; ?></img><br><br><br>
		<?php echo '<img class = "rooks" id = "rook4" src = "images/dice'.rand(1,6).'.png"'; ?></img><br><br><br>
		<?php echo '<img class = "rooks" id = "rook5" src = "images/dice'.rand(1,6).'.png"'; ?></img><br><br><br>
<br><br><br><br><br><br><br><br>
		<!--
                <a class="go" href="#">Coming Soon!</a>
           	-->
	    </div>
            <div id="RPS">
                <h2>Rock Paper Scissors</h2>
                <p>Who doesn't love Rock Paper Sciessors.</p>
                <p>In this challenge users will a function which returns rock, paper or scissors for many rounds.</p>
                <p>Users will not have access to "Random" so will have to come up with some strategy</p>
                <a class="go" href="newRPSSubmission.php">Begin!</a>
                <br><br><br><br>
            </div>
            <div class="rpsCircleContainer">
              <img class="circleImage" src="images/dice1.jpg" alt="Rock">
              <img class="circleImage" src="images/dice2.jpg" alt="Paper">
              <img class="circleImage" src="images/dice3.jpg" alt="Scissors">
            </div>
        </div>
	<br><br><br>

        <script>
            const circles = document.querySelectorAll('.circle');
            
            window.addEventListener('scroll', () => {
                // Calculate the percentage of scroll down the page
                const scrollPercent = window.scrollY/500;
                
                // Determine how many circles should be colored based on scroll percentage
                const circlesToColor = Math.floor(scrollPercent * (circles.length / 2))-1;
        
                // Change the color of circles one by one
                for (let i = 0; i < circles.length / 2; i++) {
                    if (i <= circlesToColor) {
                        if (i % 2 === 1) {
                            circles[i].style.backgroundColor = 'green';
                            if (circles[i + 10]) circles[i + 10].style.backgroundColor = 'red';
                        } else {
                            circles[i].style.backgroundColor = 'red';
                            if (circles[i + 10]) circles[i + 10].style.backgroundColor = 'green';
                        }
                    } else {
                        // Reset top and bottom row circles to gray if not in range
                        circles[i].style.backgroundColor = 'gray';
                        if (circles[i + 10]) circles[i + 10].style.backgroundColor = 'gray';
                    }
                }
                
    
                document.getElementById("rook").style.transform = `translateX(${window.scrollY * 2}px)`;
                document.getElementById("rook2").style.transform = `translateX(-${window.scrollY * 2}px)`;
                document.getElementById("rook3").style.transform = `translateX(${window.scrollY * 1.9}px)`;
                document.getElementById("rook4").style.transform = `translateX(-${window.scrollY * 1.9}px)`;
                document.getElementById("rook5").style.transform = `translateX(${window.scrollY * 1.8}px)`;



                const scrollPercentage = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
                const angle = scrollPercentage * 360;  // Angle ranges from 0 to 360 degrees

                // Get all images
                const images = document.querySelectorAll('.circle-image');
                const radius = 150;  // Distance from the center of the circle

                images.forEach((img, index) => {
                    // Calculate angle for each image (offset based on index)
                    const offsetAngle = angle + (index * 120); // Space them 120 degrees apart
                    const x = radius * Math.cos(offsetAngle * Math.PI / 180); // Convert angle to radians
                    const y = radius * Math.sin(offsetAngle * Math.PI / 180);

                    // Apply the transform
                    img.style.transform = `translate(-50%, -50%) translate(${x}px, ${y}px)`;
                  });
            });
        </script>

    </body>
</html>
