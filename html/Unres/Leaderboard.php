<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



include "/var/www/unresdb.php";

// Fetch all decks
$stmt = $pdo->query('SELECT
	t1.deck as id,
	SUM(
		CASE t1.colour
            		WHEN 1 THEN 1
            		WHEN 2 THEN 2
            		WHEN 3 THEN 4
            		WHEN 4 THEN 8
            		WHEN 5 THEN 16
        	END
	) AS colour,
	d.elo as elo,
	d.provided_archetype as arch
FROM
(
	SELECT
		cid.deck_id AS deck,
		coc.colour_id AS colour
	FROM cards_in_deck cid
	LEFT JOIN colours_of_cards coc ON cid.card_id = coc.card_id
	WHERE coc.colour_id IS NOT NULL
	GROUP BY cid.deck_id, coc.colour_id
) as t1
inner join decks d on d.id = t1.deck
GROUP BY deck
ORDER BY deck
LIMIT 10;
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
          backdrop-filter: blur(5px);
          pointer-events: none;
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
        }


        .login-dark .illustration {
          text-align:center;
        }

        td{
            color:#fff;
        }

        table{
            text-align:center;
        }

        .lbimg{
            width:50px;
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
                        <?php $imageUrl = "images/".$deck['colour'].".png"; ?>
                        <img class="lbimg" src="<?= htmlspecialchars($imageUrl) ?>" alt="color">
                    </td><td>
                        <?= htmlspecialchars($deck['arch']) ?><br><span style="color:#aaa;">#<?= $deck['id'] ?></span>
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