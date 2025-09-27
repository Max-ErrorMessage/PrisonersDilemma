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
        d.id as id,
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
        d.custom_id as cid,
        d.name as name,
        d.elo AS elo
FROM colours_of_decks cod
RIGHT JOIN decks d ON d.id = cod.deck_id
GROUP BY id
ORDER BY elo DESC;');
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;

$one = $decks[0];
$two = $decks[1];
$three = $decks[2];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unres Leaderboard as a Train Station Departure Board</title>
  <style>
    body {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #000;
    }
    .image-container {
      position: relative;
      width: 800px;
    }
    .image-container img {
      width: 100%;
      display: block;
    }

    .overlay{
        position:absolute;
        width:482px;
        height:136px;
        left:145px;
        top:135px;
        transform: rotate(-1.6deg);
    }

    .overlay{
        background-color: #221;
        background-image:
        /*linear-gradient(to bottom, rgba(0,0,0,0.2), transparent),*/
        radial-gradient(rgba(100,155,055,0.1) 2px, transparent 2px);
        background-size: 6px 4px;
    }

    .overlay::after{
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image:
        linear-gradient(to bottom, rgba(0,0,0,0.2), transparent),
        linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, transparent 10%),
        linear-gradient(to left, rgba(0,0,0,0.2), transparent),
        linear-gradient(to left, rgba(0,0,0,0.6) 0%, transparent 5%);
    }
    @font-face {
    font-family: 'Ucka';
    src: url('ucka.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
    }

    .row{
        background-color: #331;
        background-image:
        radial-gradient(rgba(200,255,155,0.1) 1px, transparent 1px);
        background-size: 4px 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left:12px;
        padding-right:12px;
        padding-top:2px;
        margin-top:3px;
        font-family: 'Ucka';
        letter-spacing: 2px;
        font-size:13px;

        color: #e80;
        text-shadow: 0 0 1px #fc1, 0 0 3px #fc1;
        overflow-x:hidden;
    }

    .row span{
        white-space: nowrap;
    }

    .mt-12{
        margin-top:10px;
    }

    .big{
        background-color: #221;
        background-image:
        radial-gradient(rgba(100,155,055,0.1) 1px, transparent 2px);
        background-size: 6px 4px;
        font-size:22px;
    }

    #time{
        background-color: #331;
        background-image:
        radial-gradient(rgba(200,255,155,0.1) 2px, transparent 2px);
        background-size: 6px 4px;
        padding:3px;
        height:25px;
    }

    .init-hid{
        display:none
    }

  </style>
</head>
<body>
    <div class="image-container">
        <img src="bg.jpg" alt="Background">
        <div class="overlay">


                <div class="row mt-12">
                    <span>1st <?= substr($one['elo'], 0, 2)?>:<?= substr($one['elo'], 2, 2)?> <?= $one['name']?></span>
                    <span>On time</span>
                </div>


                <div class="row init-hid">
                    <span>2nd <?= substr($two['elo'], 0, 2)?>:<?= substr($two['elo'], 2, 2)?> <?= $two['name']?></span>
                    <span>On time</span>
                </div>

                <div class="row">
                    <span>Calling at: </span><span>Dark Ritual, Mindbreak Trap, Mental Misstep, Barrowgoyf, Vexing Bauble, Orcish Bowmasters, Urza's Saga, Snow-Covered Swamp, Polluted Delta, Thoughtseize, Mox Jet, Black Lotus, Hymn to Tourach, Dauthi Voidwalker, Null Rod, Underground Sea, Urborg, Tomb of Yawgmoth, Sudden Edict, Feed the Swarm</span>
                </div>


                <div class="row">
                    <span>3rd <?= substr($three['elo'], 0, 2)?>:<?= substr($three['elo'], 2, 2)?> <?= $three['name']?></span>
                    <span>On time</span>
                </div>

                <div class="row big mt-12">
                    <span></span>
                    <span id="time">22:22:56</span>
                    <span></span>
                </div>
        </div>
    </div>
</body>
<script>
    function updateClock() {
        const now = new Date();

        // Format with leading zeros
        const hours   = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        // Put the formatted time into the element
        document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update immediately, then every second
    updateClock();
    setInterval(updateClock, 1000);
   </script>
</html>