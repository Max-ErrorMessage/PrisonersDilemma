<?php

/*
 * Allows users to either sign up and create a new account or log in to an existing account
 *
 * Author: James Aris
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "/var/www/unresdb.php";


// Fetch all decks
$stmt = $pdo->query("SELECT id, elo FROM decks");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
    <head>
	<meta name="viewport" content="width=device-width, initial-scale=0.75">
	<title>Unrestricted Vintage Matchups</title>
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

            body {
              background-image: linear-gradient(to bottom right, #000000, #001500);
              font-family: Arial, sans-serif;
              color: #aaffaa;
              margin: 0;
              height: 100vh;
              display: flex;
              align-items: center;
              justify-content: center;
            }

            /* Form container "card" */
            #login {
              background: rgba(0, 30, 0, 0.9);
              padding: 2rem;
              border-radius: 1rem;
              box-shadow: 0 0 25px rgba(0, 255, 100, 0.25);
              width: 100%;
            }

            #bg{
                width: 40%;
            }

            /* Form layout */
            form {
              display: flex;
              flex-direction: column;
              gap: 1.25rem;
            }

            /* Labels */
            label {
              font-weight: bold;
              margin-bottom: 0.25rem;
              color: #7fff7f;
            }

            /* Custom select style */
            .custom-select, select {
              appearance: none;
              -webkit-appearance: none;
              -moz-appearance: none;

              background: #001000;
              color: #aaffaa;
              border: 2px solid #00cc66;
              border-radius: 8px;
              padding: 0.6rem 0.75rem;
              font-size: 1rem;
              cursor: pointer;
              transition: border-color 0.2s, box-shadow 0.2s;
              width:90%
            }

            select:focus, .custom-select.open {
              border-color: #00ff99;
              box-shadow: 0 0 10px #00ff99;
              outline: none;
            }

            /* Submit button */
            input[type="submit"] {
              padding: 0.8rem;
              border-radius: 0.6rem;
              border: none;
              background: linear-gradient(90deg, #00cc66, #00994d);
              color: #fff;
              font-size: 1rem;
              font-weight: bold;
              cursor: pointer;
              transition: transform 0.2s, background 0.2s;
            }

            input[type="submit"]:hover {
              background: linear-gradient(90deg, #00ff99, #00cc66);
              transform: scale(1.05);
            }

            .custom-select {
              position: relative;
              width: 100%;
              background: #001000;
              border: 2px solid #00ff00;
              border-radius: 8px;
              cursor: pointer;
              color: #aaffaa;
              font-size: 1rem;
              margin-bottom: 1rem;
            }

            .custom-select .selected {
              padding: 0.6rem 0.75rem;
            }

            .custom-select .options {
              display: none;
              position: absolute;
              top: calc(100% + 2px);
              left: 0;
              right: 0;
              background: #001500;
              border: 2px solid #00ff00;
              border-radius: 8px;
              overflow: hidden;
              z-index: 100;
            }

            .custom-select.open .options {
              display: block;
            }

            .custom-select .options li {
              list-style: none;              /* removes bullet points */
              padding: 0.6rem 0.75rem;
              color: #aaffaa;
              cursor: pointer;
              transition: background 0.2s;
            }

            .custom-select .options li:hover {
              background: rgba(0, 255, 0, 0.2);
              color: #00ff99;
            }

        </style>
        <link rel="stylesheet" href="login.css">
	<meta name="description" content="Create bots to compete in fun minigames! :)">
    </head>
    <body style="background-image:linear-gradient(to bottom right, rgb(0,0,0), rgb(0,20,10))" id="body">

        
        <canvas id="particleCanvas"></canvas>
        <div id="bg">
            <div id = "login" style="background-color:#005500">
                 <form action="submit_match.php" method="post">
                      <label for="playerA">Player A:</label>
                      <div class="custom-select" data-name="playerA">
                        <div class="selected">Select a deck</div>
                        <ul class="options">
                          <?php foreach ($decks as $deck): ?>
                            <li data-value="<?= htmlspecialchars($deck['id']) ?>">
                              <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="playerA">
                      </div>

                      <label for="playerB">Player B:</label>
                      <div class="custom-select" data-name="playerB">
                        <div class="selected">Select a deck</div>
                        <ul class="options">
                          <?php foreach ($decks as $deck): ?>
                            <li data-value="<?= htmlspecialchars($deck['id']) ?>">
                              <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="playerB">
                      </div>

                      <input type="submit" value="Submit Match">
                    </form>


            </div>
        </div>

        
        <script>
            document.querySelectorAll(".custom-select").forEach(select => {
              const selected = select.querySelector(".selected");
              const options = select.querySelector(".options");
              const hiddenInput = select.querySelector("input[type=hidden]");

              // Open/close dropdown
              selected.addEventListener("click", () => {
                  document.querySelectorAll(".custom-select").forEach(s => {
                    if (s !== select) s.classList.remove("open");
                  });
                  select.classList.toggle("open");
                });

              // Choose option
              options.querySelectorAll("li").forEach(option => {
                option.addEventListener("click", () => {
                  selected.textContent = option.textContent;
                  hiddenInput.value = option.dataset.value;
                  select.classList.remove("open");
                });
              });
            });

            // Close dropdowns when clicking outside
            window.addEventListener("click", e => {
              if (!e.target.closest(".custom-select")) {
                document.querySelectorAll(".options").forEach(opt => opt.style.display = "none");
              }
            });

            const canvas = document.getElementById('particleCanvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            const particles = [];
            
            const mouse = {
              x: null,
              y: null,
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
                this.yModifer = 0
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
                  
                this.x += this.speedX + this.xModifier;
                this.y += this.speedY + this.yModifier;
            
                
                //if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                //if (this.y > canvas.height || this.y < 0) this.speedY *= -1;
                
                if (this.x > canvas.width && this.speedX > 0) this.speedX *= -1;
                if (this.x < 0 && this.speedX < 0) this.speedX *= -1;
                if (this.y > canvas.height && this.speedY > 0) this.speedY *= -1;
                if (this.y < 0 && this.speedY < 0) this.speedY *= -1;
              }
            
              draw() {
                ctx.fillStyle = `rgba(0, 100, 0, ${this.alpha})`;
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
