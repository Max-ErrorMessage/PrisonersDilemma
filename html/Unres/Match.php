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
              padding: 0.5rem 0.75rem;
              border-radius: 6px;
            }

            .custom-select .options {
              display: none;
              position: absolute;
              top: 100%;
              left: 0;
              right: 0;
              background: #001500;
              border: 2px solid #00ff00;
              border-radius: 6px;
              max-height: 200px;
              overflow-y: auto;
              z-index: 100;
            }

            .custom-select .options li {
              padding: 0.5rem 0.75rem;
              transition: background 0.2s;
            }

            .custom-select .options li:hover {
              background: rgba(0, 255, 0, 0.2);
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
                document.querySelectorAll(".options").forEach(opt => {
                  if (opt !== options) opt.style.display = "none";
                });
                options.style.display = options.style.display === "block" ? "none" : "block";
              });

              // Choose option
              options.querySelectorAll("li").forEach(option => {
                option.addEventListener("click", () => {
                  selected.textContent = option.textContent;
                  hiddenInput.value = option.dataset.value;
                  options.style.display = "none";
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
