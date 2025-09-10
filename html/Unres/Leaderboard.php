<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



include "/var/www/html/Unres/db.php";

// Fetch all decks
$stmt = $pdo->query('SELECT
        cod.deck_id as id,
        SUM(
                CASE cod.colour_id
                        WHEN 1 THEN 1
                        WHEN 2 THEN 2
                        WHEN 3 THEN 4
                        WHEN 4 THEN 8
                        WHEN 5 THEN 16
                        ELSE 0
                END
        ) AS colour,
	d.name as name,
	d.custom_id as cid,
        d.elo AS elo,
        d.provided_archetype AS arch
FROM colours_of_decks cod
INNER JOIN decks d ON d.id = cod.deck_id
GROUP BY d.id
ORDER BY elo;
');
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;
?>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <title>Unrestricted Vintage Matchups</title>
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <meta name="description" content="Create bots to compete in fun minigames! :)">
    <meta property="og:title" content="Unres Leaderboard">
    <meta property="og:description" content="The best decks in unres! Take a look at the rankings and see who is on top!">
    <meta property="og:image" content="2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .bg-img {
          height:100%;
          background:#475d62 url("images/vb2.png");
          background-size:110% auto;
          background-position: 50% 5%;
          transition: background-position 0.15s;
        }

        .bg-img::before {
          content: "";
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.3)), url("images/vb3.png");
          backdrop-filter: blur(5px);
          pointer-events: none;
          background-position:  50% 5%;;
        }
        .bg-img::after {
          background-image: url("images/vb1.png");
          background-position:  50% 5%;;
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
          height:80%;
          overflow-y:scroll;
        }

        ::-webkit-scrollbar{
            display:none;
        }

        .bg-img .illustration {
          text-align:center;
        }
        .illustration img{
            width:65px;
        }
        td{
            color:#fff;
        }

        table{
            text-align:left;
            font-size:15px;
            width:80%;
            position:absolute;
            left:10%;
        }

        .lbimg{
            width:30px;
        }

        #r1, #r2, #r3{
            color:black;
        }

        .n{
            width:30px;
            height:30px;
            background-color:#113;
            border-radius:8px;
            align-content: center;
            text-align: center;
        }
        .ra{
            text-align:right;
        }

        .c1{
            background-color:#FFE177;
            border: 2px solid black;
            font-weight:bolder;
        }

        .c2{
            background-color:#DEECF1;
            border: 2px solid black;
            font-weight:bold;
        }

        .c3{
            background-color:#FE646F;
            border: 2px solid black;
            font-weight:bold;
        }

    </style>
</head>
<body>

    <div class="bg-img">
        <div id="lb">
            <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png"/></div>
            <br>
            <table>
            <?php foreach ($decks as $deck): ?>
                <tr>
                    <td>
                        <div class="n c<?= $rank?>"><span id="r<?= $rank?>"><?= $rank?>.</span></div>
                    </td><td>
                        <?php $imageUrl = "images/".$deck['colour'].".png"; ?>
                        <img class="lbimg" src="<?= htmlspecialchars($imageUrl) ?>" alt="color">
                    </td><td>
                        <?= htmlspecialchars($deck['name']) ?><br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $deck['cid'] ?></span>
                    </td><td>

                        <div class="ra"><?= htmlspecialchars($deck['elo']) ?></div>
                    </td>
                </tr>
                <?php $rank++; ?>
            <?php endforeach; ?>
            </table>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        const div = document.querySelector('.bg-img');

        div.addEventListener('mousemove', (e) => {
          const { width, height } = div.getBoundingClientRect();

          // Get mouse position relative to the div (0 to 1)
          const x = e.clientX / width;
          const y = e.clientY / height;

          // Map 0-1 to background-position offsets (-10% to +10%)
          const offsetX = (0.5 - x) * 10; // -10% to +10%
          const offsetY = (0.5 - y) * 5;

          div.style.backgroundPosition = `${50 + offsetX}% ${5 + offsetY}%`;
        });
    </script>
</body>
</html>
