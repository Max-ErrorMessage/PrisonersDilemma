<?php

/**
 * Allows users to either sign up and create a new account or log in to an existing account
 *
 * Author: James Aris
 */

session_start();
?>

<html>
    <head>
	<meta name="viewport" content="width=device-width, initial-scale=0.75">
	<title>Twokie - Login/Signup</title>
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <style>
            #particleCanvas {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              z-index: -1;
            }
        </style>
        <link rel="stylesheet" href="login.css">
	<meta name="description" content="Create bots to compete in fun minigames! :)">
    </head>
    <body style="background-image:linear-gradient(to bottom right, rgb(0,0,0), rgb(0,20,10))" id="body">

        
        <canvas id="particleCanvas"></canvas>
        <div id="bg">
            <div id = "login">
                <?php
                    session_start();
                    if(isset($_SESSION['Error'])){
                        echo "<h2>" . $_SESSION['Error'] . "</h2>";
                    } 
                ?>
                <h1 style = "text-align: center;"> Log in </h1>
                <form style = "text-align: center;" action="login.php" method = "post">
                    <label for="uname">Username:</label><br>
                    <input type = "text" id = "uname" name="uname"><br>
                    <label for="pword">Password:</label><br>
                    <input type = "password" id = "pword" name="pword"><br><br>
                    <input class = "butt" type="submit" value="Submit">
                </form>
            </div>
            <div id = "signup">
                <?php
                    if(isset($_SESSION['Error2'])){
                        echo "<h2>" . $_SESSION['Error2'] . "</h2>";
                    } 
                ?>
                <h1 style = "text-align: center;">Sign up </h1>
                <form style = "text-align: center;" action="signup.php" method = "post">
                    <label for="uname">Username:</label><br>
                    <input type = "text" id = "uname" name="uname"><br>
                    <label for="pword">Password:</label><br>
                    <input type = "password" id = "pword" name="pword"><br>
                    <label for="pword2">Confirm password:</label><br>
                    <input type = "password" id = "pword2" name="pword2"><br><br>
                    <input class = "butt" type="submit" value="Submit">
                </form>
            </div>
        </div>

        
        <script>
            const canvas = document.getElementById('particleCanvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            const particles = [];
            var wind ={
              x: 0,
              y: 0
            }

            const mouse = {
              x: null,
              y: null
            };
            
            window.addEventListener('mousemove', (event) => {
              mouse.x = event.x;
              mouse.y = event.y;
            });
            
            // Particle class
            class Particle {
              constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 2; 
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
                this.alpha = Math.random() * 0.25 + 0.75;
                this.xModifier = 0
                this.yModifier = 0
              }
            
              update() {
                 
                var dx = this.x - mouse.x;
                var dy = this.y - mouse.y;
                
                var distance = Math.sqrt(dx * dx + dy * dy);
                
                var xComp = dx/distance;
                var yComp = dy/distance;
                
                var modifier = 20/distance;




                this.xModifier = modifier*xComp
                this.yModifier = modifier*yComp
                  
                this.x += this.speedX + this.xModifier + wind.x;
                this.y += this.speedY + this.yModifier + wind.y;

                //if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                //if (this.y > canvas.height || this.y < 0) this.speedY *= -1;
                
                if (this.x > canvas.width) this.x -= canvas.width;
                if (this.x < 0) this.x += canvas.width;
                if (this.y > canvas.height) this.y -= canvas.height;
                if (this.y < 0) this.y+= canvas.height;
              }
            
              draw() {
                ctx.fillStyle = `rgba(0, 100, 0, 1)`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.closePath();
                ctx.fill();
              }
            }
            
            // Create particles
            function init() {
              for (let i = 0; i < 100; i++) { // Number of particles
                particles.push(new Particle());
              }
            }
            
            // Animate particles
            function animate() {
              ctx.clearRect(0, 0, canvas.width, canvas.height);

              if (Math.random() < 0.001){
                wind.x += (Math.random() * 10) - 5
                wind.y += (Math.random() * 10) - 5
              }
              else
              {
                wind.x *= 0.995
                wind.y *= 0.995
              }
              particles.forEach((particle) => {
                particle.update();
                particle.draw();
              });
            
              requestAnimationFrame(animate);
            }
            
            // Adjust canvas on window resize
            window.addEventListener('resize', () => {
              canvas.width = window.innerWidth;
              canvas.height = window.innerHeight;
            });
            
            init();
            animate();

        </script>
        
    </body>
</html>
