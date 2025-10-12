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

$stmt = $pdo->query('
SELECT c.card_name as name
FROM card_in_deck cid
LEFT JOIN cards c on cid.card_id = c.id
WHERE cid.deck_id = 500;
');
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('
SELECT c.card_name as name, cid.quantity as n
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 1
order by n desc
limit 20;');
$stmt->bindParam(':id',$one['id'], PDO::PARAM_INT);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        overflow: hidden;
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

          overflow: hidden;
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
        overflow: hidden;
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

        color: #fda;
        text-shadow: 0px 0px 1px #e80;
        overflow-x:hidden;
    }

    .row span{
        white-space: nowrap;
    }

    .mt-12{
        margin-top:10px;
    }

    .beegee{
        background-color: #331;
        background-image:
        radial-gradient(rgba(200,255,155,0.1) 1px, transparent 1px);
        background-size: 4px 4px;
        z-index: 3;
    }

    .big{
        background-color: #221;
        background-image:
        radial-gradient(rgba(100,155,055,0.1) 1px, transparent 2px);
        background-size: 6px 4px;
        font-size:22px;
        overflow: hidden;
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

    .scroll-text {
        display:inline-block;
        padding-left: 100%;
        animation: scroll-left 40s linear infinite;
    }

    @keyframes scroll-left {
      0%   { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }

    .fl-1{
        flex: 1;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
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


                <div class="row" id="r2-ca">
                    <span>Playing: </span><div class="fl-1"><span class="scroll-text"><?= implode(", ", array_column($cards, "name"))?></span></div>
                </div>

                <div class="row" id="r2-d2">
                    <span>2nd <?= substr($two['elo'], 0, 2)?>:<?= substr($two['elo'], 2, 2)?> <?= $two['name']?></span>
                    <span>On time</span>
                </div>



                <div class="row init-hid" id="r3-d3">
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

        const hours   = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    function switchRow2(){
        div1 = document.getElementById('r2-d2')
        div2 = document.getElementById('r2-ca')

        if (div1.classList.contains("init-hid")) {
            div1.classList.remove("init-hid")
            div2.classList.add("init-hid")
        } else {
            div2.classList.remove("init-hid")
            div1.classList.add("init-hid")
        }
    }

    function switchRow3(){
        div1 = document.getElementById('r2-d2')
        div2 = document.getElementById('r3-d3')
        not_scrolling = document.getElementById('r2-ca').classList.contains('init-hid')

        if (!not_scrolling){
            if (div1.classList.contains("init-hid")) {
                div1.classList.remove("init-hid")
                div2.classList.add("init-hid")
            } else {
                div2.classList.remove("init-hid")
                div1.classList.add("init-hid")
            }
        } else {
            if (div1.classList.contains("init-hid")) {
                div1.classList.remove("init-hid")
                div2.classList.add("init-hid")
            }
        }
    }

    updateClock();
    setInterval(updateClock, 1000);

    setInterval(switchRow2, 40000);
    setInterval(switchRow3, 10000);
   </script>
</html>