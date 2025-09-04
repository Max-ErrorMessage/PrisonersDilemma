<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "/var/www/unresdb.php";

// Fetch all decks
$stmt = $pdo->query("SELECT id, provided_archetype, elo FROM decks ORDER BY elo DESC LIMIT 10");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;
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
          background:#475d62 url("https://cards.scryfall.io/art_crop/front/3/d/3de472d0-cca2-4bc3-ab0a-9f79fa6325ce.jpg?1717015384");
          background-size:cover;
          background-position: top center;
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

        #lb {
          min-width:320px;
          width:40%;
          background-color:#1e2833;
          padding:40px;
          border-radius:4px;
          transform:translate(-50%, -50%);
          position:absolute;
          top:50%;
          left:50%;
          color:#fff;
          box-shadow:3px 3px 4px rgba(0,0,0,0.2);
          display: flex;
          justify-content: center;
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

        .item{
            width:80%;
            background:#99E6FC;
            color:#1e2833;
            text-align:center;
            padding:15px;
            font-size:20px;
        }

        .item p{
            text-align: center;
        }

    </style>
</head>
<body>

    <div class="login-dark">
        <div id="lb">
            <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/6967/6967688.png"/></div>
            <table>
            <?php foreach ($decks as $deck): ?>
                <tr>
                    <td>
                       <?= $rank?>.
                    </td><td>
                        <?= htmlspecialchars($deck['id']) ?>:<?= htmlspecialchars($deck['provided_archetype']) ?>
                    </td><td>
                        <?= htmlspecialchars($deck['elo']) ?>
                    </td>
                </tr>
                <?php $rank++; ?>
            <?php endforeach; ?>
            <table>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>

</body>
</html>