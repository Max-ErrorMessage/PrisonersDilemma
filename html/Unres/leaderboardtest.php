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
    <link rel="stylesheet" href="unres.css">
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



    </style>
</head>
<body>

    <div class="bg-bg">
        <div class="bg-img">
            <div class="bg-fg">
                <a href="Match.php" id="back">
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
                        <?php foreach ($decks as $deck): ?>
                            <tr onclick=goToDeck(<?= $deck['id']?>)>
                                <td>
                                    <div class="n c<?= $rank?>"><span id="r<?= $rank?>"><?= $rank?>.</span></div>
                                </td><td>
                                    <?php $imageUrl = "images/".$deck['colour'].".png"; ?>
                                    <img class="lbimg" src="<?= htmlspecialchars($imageUrl) ?>" alt="color">
                                </td><td>
                                    <?= htmlspecialchars($deck['name']) ?><br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $deck['cid'] ?></span>
                                </td><td>

                                    <div class="ra"><?= explode('.',htmlspecialchars($deck['elo']))[0] ?></div>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>

                    <div id="page2" style="display:none">
                        <h3 style="text-align:center;"> <?= $deck['name'] ?> </h3>
                        <canvas id="elograph"></canvas>
                        <div id="match-table">
                            <table>
                                <?php foreach ($elo_rows as $row): ?>
                                    <tr>
                                        <td>
                                            <p><?= htmlspecialchars($row['winner'])?> beat <?= htmlspecialchars($row['loser'])?></p>
                                        </td><td>
                                            <p style="text-align:right;"><?= htmlspecialchars($row['elo_change'])?> elo gain</p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <div id="page3" style="display:none">
                        <h3 style="text-align:center;"> <?= $deck['name'] ?> </h3>
                        <strong>Similar Decks:</strong>
                        <br>
                        <br>
                        <div id="sim-table">
                            <table id=>
                                <?php foreach ($sim_data as $deck): ?>
                                    <tr onclick=goToDeck(<?= $deck['id']?>)>
                                        <td>
                                            <p><?= htmlspecialchars($deck['name'])?></p>
                                        </td><td>
                                            <p style="text-align:right;"><?=  round($deck['sim'] * 100) ?>%</p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
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
            if (n==1){
                tab1.style.backgroundColor = "#1e2833"
                tab1.style.backgroundImage = "none"
                tab2.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab2.style.backgroundColor = "none"
                tab3.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab3.style.backgroundColor = "#none"

                page1.style.display = "block"
                page2.style.display = "none"
                page3.style.display = "none"

            } else if (n==2){
                tab2.style.backgroundColor = "#1e2833"
                tab2.style.backgroundImage = "none"
                tab1.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab1.style.backgroundColor = "none"
                tab3.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
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
                tab1.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab1.style.backgroundColor = "none"
                tab2.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab2.style.backgroundColor = "#none"

                page3.style.display = "block"
                page1.style.display = "none"
                page2.style.display = "none"
            }
        }

    </script>
</body>
</html>
