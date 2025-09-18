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
$stmt = $pdo->query('
WITH dcc AS (
    SELECT c.card_name, COUNT(cid.deck_id) as decks_containing_card, image_url
    FROM cards c
    LEFT JOIN card_in_deck cid ON cid.card_id = c.id
    WHERE cid.mainboard = 1
    GROUP BY c.id
)
SELECT dcc.card_name, dcc.image_url, dcc.decks_containing_card * 100 / (
    SELECT COUNT(*) from decks
) AS percentage_playrate
FROM dcc
ORDER BY percentage_playrate DESC;');
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;

$stmt = $pdo->query('
WITH dcc AS (
    SELECT c.card_name, COUNT(cid.deck_id) as decks_containing_card, image_url
    FROM cards c
    LEFT JOIN card_in_deck cid ON cid.card_id = c.id
    WHERE cid.mainboard = 0
    GROUP BY c.id
)
SELECT dcc.card_name, dcc.image_url, dcc.decks_containing_card * 100 / (
    SELECT COUNT(*) from decks
) AS percentage_playrate
FROM dcc
ORDER BY percentage_playrate DESC;');
$sbcards = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sbrank = 1;
?>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <title>Unrestricted Vintage Leaderboard</title>
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <meta name="description" content="Create bots to compete in fun minigames! :)">
    <meta property="og:title" content="Unres Leaderboard">
    <meta property="og:description" content="The best decks in unres! Take a look at the rankings and see who is on top!">
    <meta property="og:image" content="6.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .bg-bg, .bg-img, .bg-fg {
          height:100%;
          background-size:110% auto;
          background-position: 50% 50%;
          position: relative;
          transition: background-position 0.15s;
        }



        .bg-fg{
          background-image: url("images/vb3.png");
        }

        .bg-img{
          background-image: url("images/vb2.png");
        }

        .bg-bg{
          background-image: url("images/vb1.png");
        }


        .bg-fg::before {
          content: "";
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.3));
          backdrop-filter: blur(5px);
          pointer-events: none;
        }




        .bg-img .content{
            position:relative;
        }


        #lb {
          min-width:320px;
          width:50%;
          background-color:#1e2833;
          padding:40px;
          border-radius:4px;
          transform:translate(-50%, -50%);
          position:absolute;
          top:50%;
          left:50%;
          color:#fff;
          box-shadow:3px 3px 4px rgba(0,0,0,0.2);
          height:70%;
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
            font-weight:bold;
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

        tr:hover{
            background-color:#345;
            transition:background-color 0.2s;
        }
        tr{
            border-radius:5px;
        }

        tr td:first-child {
          border-top-left-radius: 5px;
          border-bottom-left-radius: 5px;
        }

        tr td:last-child {
          border-top-right-radius: 5px;
          border-bottom-right-radius: 5px;
        }

        #back{
            color:white;
            background-color:#1e2833;
            width:50px;
            height:50px;
            position:absolute;
            top:25px;
            left:50px;
            padding: 0px;
            border-radius:15px;
            font-weight:bolder;
            text-decoration:none;
        }

        #back img{
            margin:10px 12px 10px 8px;
            width:30px;
            height:auto;
            filter:  brightness(1.4) saturate(0.7) hue-rotate(-10deg);
        }

            .tab{
        background-image: linear-gradient(to top, #101820, #1e2833);
        position: absolute;
        bottom: 84.9%;
        height: 50px;
        width: 80px;
        border-radius: 5px 5px 0px 0px;
    }

    .tab img{
        width: 40px;
        height: auto;
        text-align: center;
        margin: auto;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    #t1{
        background-color:#1e2833;
        background-image:none;
        left: 31%;
    }
    #t2{
        left: calc(32% + 80px);
    }
    #t3{
        left: calc(33% + 160px);
    }

    </style>
</head>
<body>

    <div class="bg-bg">
        <div class="bg-img">
            <div class="bg-fg">
                <a href="Leaderboard.php" id="back">
                    <img src="https://cdn-icons-png.flaticon.com/128/9795/9795832.png">
                </a>
                <a class="tab" id="t1" onclick="switchTab(1)">
                    <img src="https://cdn-icons-png.flaticon.com/128/6831/6831865.png"/>
                </a>
                <a class="tab" id="t2" onclick="switchTab(2)">
                    <img src="https://cdn-icons-png.flaticon.com/128/9874/9874735.png"/>
                </a>
                <a class="tab" id="t3" onclick="switchTab(3)">
                    <img src="https://cdn-icons-png.flaticon.com/128/3867/3867474.png"/>
                </a>
                <div id="lb">
                    <div id = "page1">
                        <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png"/></div>
                        <br>
                        <table>
                        <?php foreach ($cards as $card): ?>
                            <tr>
                                <td>
                                    <div class="n c<?= $rank?>"><span id="r<?= $rank?>"><?= $rank?>.</span></div>
                                </td><td>
                                    <img src="<?= htmlspecialchars($card['image_url']) ?>" style = "width:40%; border-radius:10px; border:3px #aef solid;">
                                </td><td>
                                    <?= htmlspecialchars($card['card_name']) ?>
                                </td><td>

                                    <div class="ra"><?= explode('.',htmlspecialchars($card['percentage_playrate']))[0]?>%</div>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>

                    <div id = "page2" style="display:none">
                        <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png"/></div>
                        <br>
                        <table>
                        <?php foreach ($sbcards as $card): ?>
                            <tr>
                                <td>
                                    <div class="n c<?= $sbrank?>"><span id="r<?= $rank?>"><?= $sbrank?>.</span></div>
                                </td><td>
                                    <img src="<?= htmlspecialchars($card['image_url']) ?>" style = "width:40%; border-radius:10px; border:3px #aef solid;">
                                </td><td>
                                    <?= htmlspecialchars($card['card_name']) ?>
                                </td><td>
                                    <div class="ra"><?= explode('.',htmlspecialchars($card['percentage_playrate']))[0]?>%</div>
                                </td>
                            </tr>
                            <?php $sbrank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>



                    <div id = "page3" style="display:none">
                        <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png"/></div>
                        <br>
                        <table>
                        <?php foreach ($cards as $card): ?>
                            <tr>
                                <td>
                                    <div class="n c<?= $rank?>"><span id="r<?= $rank?>"><?= $rank?>.</span></div>
                                </td><td>
                                    <img src="<?= htmlspecialchars($card['image_url']) ?>" style = "width:40%; border-radius:10px; border:3px #aef solid;">
                                </td><td>
                                    <?= htmlspecialchars($card['card_name']) ?>
                                </td><td>

                                    <div class="ra"><?= explode('.',htmlspecialchars($card['percentage_playrate']))[0]?>%</div>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        const div2 = document.querySelector('.bg-img');

        div2.addEventListener('mousemove', (e) => {
          const div1 = document.querySelector('.bg-fg');
          const div3 = document.querySelector('.bg-bg');
          const { width, height } = div2.getBoundingClientRect();

          // Get mouse position relative to the div (0 to 1)
          const x = e.clientX / width;
          const y = e.clientY / height;


          const offsetX = (0.5 - x) * 2;
          const offsetY = (0.5 - y) * 2;

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${50 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*2}% ${50 + offsetY*2}%`;
          div3.style.backgroundPosition = `${50 + offsetX}% ${50 + offsetY}%`;
        });

        function goToDeck(id){
            window.location = "deck.php?id=" + id
        }

    function switchTab(n){
            tab1 = document.getElementById("t1")
            tab2 = document.getElementById("t2")
            tab3 = document.getElementById("t3")
            page1 = document.getElementById("page1")
            page2 = document.getElementById("page2")
            page3 = document.getElementById("page3")

            if (n==1){
                tab1.style.backgroundColor = "#1e2833"
                tab1.style.backgroundImage = "none"
                tab2.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab2.style.backgroundColor = "none"
                tab3.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab3.style.backgroundColor = "#none"

                page1.style.display = "block"
                page2.style.display = "none"
                page3.style.display = "none"

            } else if (n==2){
                tab2.style.backgroundColor = "#1e2833"
                tab2.style.backgroundImage = "none"
                tab1.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab1.style.backgroundColor = "none"
                tab3.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab3.style.backgroundColor = "#none"

                page2.style.display = "block"
                page1.style.display = "none"
                page3.style.display = "none"

                new Chart(document.getElementById("elograph"), {
                    type: "line",
                    data: {
                      labels: labels,
                      datasets: [{
                        label: "Elo Over Time",
                        data: elo_arr,
                        borderColor: "White",
                        fill: true,
                        backgroundColor: "rgba(255,255,255,0.2)"
                      }]
                    },
                    options: {
                      scales: {
                        x: {
                          ticks: { color: "#ddd" },
                          grid: { color: "#444" },
                          display:false
                        },
                        y: {
                          ticks: { color: "#ddd" },
                          grid: { color: "#444" }
                        }
                      },
                      plugins: {
                        legend: {
                          labels: { color: "#ddd" }
                        }
                      }
                    }
                  });

            }  else if (n==3){
                tab3.style.backgroundColor = "#1e2833"
                tab3.style.backgroundImage = "none"
                tab1.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab1.style.backgroundColor = "none"
                tab2.style.backgroundImage = "linear-gradient(to top, #101820, #1e2833)"
                tab2.style.backgroundColor = "#none"

                page3.style.display = "block"
                page1.style.display = "none"
                page2.style.display = "none"
            }
        }
    </script>
</body>
</html>
