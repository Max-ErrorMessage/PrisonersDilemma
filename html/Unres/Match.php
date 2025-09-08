<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "/var/www/html/Unres/db.php";

// Fetch all decks
$stmt = $pdo->query("SELECT id, provided_archetype, decklist_url, time_submitted, custom_id, elo FROM decks");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <title>Unrestricted Vintage Matchups</title>
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <meta name="description" content="Create bots to compete in fun minigames! :)">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-dark {
          height:100%;
          background:#475d62 url("https://cards.scryfall.io/art_crop/front/8/c/8c2996d9-3287-4480-8c04-7a378e37e3cf.jpg?1707237513");
          background-size:110% auto;
          background-position: 50% 15%;
          transition: background-position 0.1s ease;
          position:relative;
        }

        .login-dark::before {
          content: "";
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.3));
          backdrop-filter: blur(5px); /* apply blur to everything behind */
          pointer-events: none; /* so it doesn’t block clicks */
        }

        .login-dark form {
          min-width:320px;
          width:20%;
          background-color:#1e2833;
          padding:40px;
          border-radius:4px;
          transform:translate(-50%, -50%);
          position:absolute;
          top:50%;
          left:50%;
          color:#fff;
          box-shadow:3px 3px 4px rgba(0,0,0,0.2);
        }

        .login-dark .illustration {
          text-align:center;
          padding:15px 0 20px;
          font-size:100px;
          color:#2980ef;
        }


        .login-dark form .form-control {
          background:none;
          border:none;
          border-bottom:1px solid #434a52;
          border-radius:0;
          box-shadow:none;
          outline:none;
          color:inherit;
        }

        .login-dark form .btn-primary {
          background:#99E6FC;
          color:#1e2833;
          border:none;
          border-radius:4px;
          padding:11px;
          box-shadow:none;
          margin-top:26px;
          text-shadow:none;
          outline:none;
        }

        .login-dark form .btn-primary:hover, .login-dark form .btn-primary:active {
          background:#bff;
          outline:none;
        }

        .login-dark form .forgot {
          display:block;
          text-align:center;
          font-size:12px;
          color:#6f7a85;
          opacity:0.9;
          text-decoration:none;
        }

        .login-dark form .forgot:hover, .login-dark form .forgot:active {
          opacity:1;
          text-decoration:none;
        }

        .login-dark form .btn-primary:active {
          transform:translateY(1px);
        }

        option{
            background-color: #1e2833 !important;
        }

    </style>
</head>
<body>

    <div class="login-dark">
        <form action="submit_match.php" method="post">
            <h2 class="sr-only">Login Form</h2>
            <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/6967/6967688.png"/></div>
            <div class="form-group">
                <label for="winner">Winner:</label>
                <select class="form-control"  id="winner" name="winner">
                    <?php foreach ($decks as $deck): ?>
                        <option value="<?= htmlspecialchars($deck['id']) ?>">
                            <?= htmlspecialchars($deck['custom_id']) ?> - <?= htmlspecialchars($deck['provided_archetype']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="loser">Loser:</label>
                <select class="form-control"  id="loser" name="loser">
                    <?php foreach ($decks as $deck): ?>
                        <option value="<?= htmlspecialchars($deck['id']) ?>">
                            <?= htmlspecialchars($deck['custom_id']) ?> - <?= htmlspecialchars($deck['provided_archetype']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><button class="btn btn-primary btn-block" type="submit">Submit</button></div>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        const div = document.querySelector('.login-dark');

        div.addEventListener('mousemove', (e) => {
          const { width, height } = div.getBoundingClientRect();

          // Get mouse position relative to the div (0 to 1)
          const x = e.clientX / width;
          const y = e.clientY / height;

          // Map 0-1 to background-position offsets (-10% to +10%)
          const offsetX = (0.5 - x) * 10; // -10% to +10%
          const offsetY = (0.5 - y) * 10;

          div.style.backgroundPosition = `${50 + offsetX}% ${15 + offsetY}%`;
        });
    </script>

</body>
</html>
