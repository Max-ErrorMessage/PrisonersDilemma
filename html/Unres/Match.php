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
form {
    display: flex;
    flex-direction: column;
    gap: 1rem; /* spacing between elements */
    padding: 2rem;
    border-radius: 1rem;
    background: rgba(0, 40, 0, 0.85);
    box-shadow: 0 0 15px rgba(0, 255, 0, 0.3);
    color: #eee;
    font-family: Arial, sans-serif;
}
label {
    font-weight: bold;
    margin-bottom: 0.25rem;
    color: #aaffaa;
}
select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: #001000;
    color: #aaffaa;
    border: 2px solid #00ff00;
    border-radius: 8px;
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    font-size: 1rem;
    cursor: pointer;
    outline: none;
    box-shadow: 0 0 8px rgba(0, 255, 0, 0.4);
}
select::after {
    content: "▼";
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #00ff00;
    outline: none;
}
select:focus {
    outline: none;
    border-color: #00ff00;
    box-shadow: 0 0 5px #00ff00;
}
input[type="submit"] {
    padding: 0.75rem;
    border-radius: 0.75rem;
    border: none;
    background: linear-gradient(to right, #00aa00, #007700);
    color: white;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s, background 0.2s;
}
input[type="submit"]:hover {
    background: linear-gradient(to right, #00ff00, #009900);
    transform: scale(1.05);
}
#login {
    max-width: 400px;
    margin: 5rem auto;
    border-radius: 1.25rem;
    padding: 2rem;
}
</style>
<link rel="stylesheet" href="login.css">
<meta name="description" content="Create bots to compete in fun minigames! :)">
</head>
<body style="background-image:linear-gradient(to bottom right, rgb(0,0,0), rgb(0,20,10))" id="body">
<canvas id="particleCanvas"></canvas>
<div id="bg">
    <div id="login" style="background-color:#005500">
        <form action="submit_match.php" method="post">
            <label for="playerA">Player A:</label>
            <select id="playerA" name="playerA">
                <?php foreach ($decks as $deck): ?>
                    <option value="<?= htmlspecialchars($deck['id']) ?>">
                        <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="playerB">Player B:</label>
            <select id="playerB" name="playerB">
                <?php foreach ($decks as $deck): ?>
                    <option value="<?= htmlspecialchars($deck['id']) ?>">
                        <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Submit Match">
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

        var modifier = 5/distance;




        this.xModifier = modifier*xComp
        this.yModifier = modifier*yComp

        this.speedX += this.xModifier + wind.x;
        this.speedY += this.yModifier + wind.y;
        if (this.speedX > 10) this.speedX = 10
        if (this.speedY > 10) this.speedY = 10
        if (this.speedX < -10) this.speedX = -10
        if (this.speedY < -10) this.speedY = -10
        this.x += this.speedX;
        this.y += this.speedY;

        //if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
        //if (this.y > canvas.height || this.y < 0) this.speedY *= -1;

        if (this.x > canvas.width) this.x -= canvas.width;
        if (this.x < 0) this.x += canvas.width;
        if (this.y > canvas.height) this.y -= canvas.height;
        if (this.y < 0) this.y+= canvas.height;

        if (this.speedX + this.speedY > 8){
            this.speedX *= 0.9
            this.speedY *= 0.9
        }
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
        wind.x += (Math.random() * 6) - 3
        wind.y += (Math.random() * 6) - 3
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
