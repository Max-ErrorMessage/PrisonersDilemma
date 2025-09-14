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
$id = $_GET["id"];

$stmt = $pdo->prepare('SELECT id, decklist_url, ELO, provided_archetype, name
FROM decks
WHERE id = :id;
');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare('SELECT c.card_name as name, cid.quantity as n, c.image_url as url
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 1
order by n desc;');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$mb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT c.card_name as name, cid.quantity as n, c.image_url as url
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 0
order by n desc;');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$sb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare('SELECT ec.elo_change, w.name AS winner, l.name AS loser FROM elo_changes ec
INNER JOIN matches m ON ec.match_id = m.id
LEFT JOIN decks w ON w.id = m.winner_id
LEFT JOIN decks l ON l.id = m.loser_id
WHERE ec.deck_id = :id;');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();

$elo = [];
$winners = [];
$losers = [];
$elo_rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $elo[] = (int)$row['elo_change'];
    $winners[] = $row['winner'];
    $losers[] = $row['loser'];
    $elo_rows[] = $row;
}


foreach ($decks as $d) {
    $deck = $d;
}

$stmt = $pdo->prepare('
    SELECT
        SUM(
            CASE cod.colour_id
                WHEN 1 THEN 1
                WHEN 2 THEN 2
                WHEN 3 THEN 4
                WHEN 4 THEN 8
                WHEN 5 THEN 16
                ELSE 0
            END
        ) AS colour
    FROM colours_of_decks cod
    RIGHT JOIN decks d ON d.id = cod.deck_id
    WHERE d.id = :id
    GROUP BY d.id
');

$stmt->execute([':id' => $id]);
$colors = $stmt->fetch(PDO::FETCH_ASSOC);
$color_url = "images/" . $colors['colour'] . ".png";




$venvPython = '/var/www/Unres-Meta/venv/bin/python';
$pythonScript = 'similarity_from_matrix.py';
$command = 'cd /var/www/Unres-Meta/elo && ' . escapeshellcmd($venvPython) . ' ' . ($pythonScript) . ' ' . ($id) . ' 2>&1';
$sim_output = shell_exec($command);


$sim_rows = str_getcsv($sim_output, "\n"); // Split by line
$sim_data = [];

array_shift($sim_rows);
if (count($sim_rows) > 0) {
    foreach ($sim_rows as $row) {
        $fields = str_getcsv($row);
        $sim_data[] = array_combine(["name","id","sim"], $fields); // Convert to assoc array
    }
}



?>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <title>Unrestricted Vintage Matchups</title>
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <meta name="description" content="Create bots to compete in fun minigames! :)">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <style>
        .bg-bg, .bg-img, .bg-fg {
          height:100%;
          background-size:110% auto;
          background-position: 50% 50%;
          position: relative;
          transition: background-position 0.15s;
        }



        .bg-fg{
          background-image: url("images/lg3.png");
        }

        .bg-img{
          background-image: url("images/lg2.png");
        }

        .bg-bg{
          background-image: url("images/lg1.png");
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


        .bg-img .illustration {
          text-align:center;
        }

        #lb {
            min-width: 320px;
            width: 40%;
            background-color: #1e2833;
            padding: 40px;
            border-radius: 4px;
            transform: translate(-50%, -50%);
            position: absolute;
            top: 50%;
            left: 50%;
            color: #fff;
            box-shadow: 3px 3px 4px rgba(0, 0, 0, 0.2);
            height: 70%;
            overflow-y: scroll;
        }

        ::-webkit-scrollbar{
            display:none;
        }


        td{
            color:#fff;
        }




    #mb ,#sb{
        width:45%;
        position:absolute;
        top:15%

    }

    #mb{
        left:3%;
    }

    #sb{
        right:3%;
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
        filter:  brightness(1.41) saturate(0.55);
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

    #match-table, #sim-table{
        left:15%;
        width:70%;
        position:absolute;
        overflow-y:scroll;
        border:1px white solid;
        border-radius:3px;
    }

    #match-table tr, #sim-table tr{
        border-bottom: 1px #555 solid;
    }

    #match-table table, #sim-table table{
        width:100%;
    }

    #match-table p, #sim-table p{
        margin:auto;
    }

    #match-table td{
        padding:4px;
    }
    #sim-table td{
        padding:8px;
    }

    #sim-table{
        height:70%;
    }

    #match-table{
        height:30%;
    }

    #sim-table tr:hover{
            background-color:#345;
            transition:background-color 0.2s;
    }

    #crd-prvw{
        position:absolute;
        left:10%;
        width:15%;
        height:auto;
        top:50%;
        transform:translate(0,-50%);
        display:none;
        border-radius:10px;
    }

    #clr-img{
        position:absolute;
        bottom:3%;
        width:15%;
        right:3%;
    }

    </style>
</head>
<body>
    <div class="bg-bg">
        <div class="bg-img">
            <div class="bg-fg">
                <img src="https://assets.moxfield.net/cards/card-O9Ovn-normal.webp?226499050" id="crd-prvw">
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
                        <h3 style="text-align:center;"> <?= $deck['name'] ?> </h3>
                        <div id="mb">
                            <strong>Mainboard:</strong>
                            <?php foreach ($mb_cards as $card): ?>
                            <div style="justify-content:space-between;display:flex; width:100%;">
                                <span
                                    onmouseenter='imgBecome("<?= htmlspecialchars($card['url']) ?>")'
                                    onmouseleave='imgLeave()'
                                    ><?= htmlspecialchars($card['name']) ?></span>
                                <span><?= htmlspecialchars($card['n']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <br>
                        <div id="sb">
                            <strong>Sideboard:</strong>
                            <?php foreach ($sb_cards as $card): ?>
                            <div style="justify-content:space-between;display:flex; width:100%">
                                <span
                                    onmouseenter='imgBecome("<?= htmlspecialchars($card['url']) ?>")'
                                    onmouseleave='imgLeave()'
                                    ><?= htmlspecialchars($card['name']) ?></span>
                                <span><?= htmlspecialchars($card['n']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <br><br>
                            <a style="color:#ccc;" href= <?= '"'.$deck['decklist_url'].'"' ?> >Click here for the deck page</a>
                        </div>
                        <img id="clr-img" src= "<?= $color_url ?>">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const div2 = document.querySelector('.bg-img');

        div2.addEventListener('mousemove', (e) => {
          const div1 = document.querySelector('.bg-fg');
          const div3 = document.querySelector('.bg-bg');
          const { width, height } = div2.getBoundingClientRect();

          // Get mouse position relative to the div (0 to 1)
          const x = e.clientX / width;
          const y = e.clientY / height;


          const offsetX = (0.5 - x) * 4;
          const offsetY = (0.5 - y) * 2;

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${50 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*2}% ${50 + offsetY*2}%`;
          div3.style.backgroundPosition = `${50 + offsetX}% ${50 + offsetY}%`;
        });

        const elo_changes = <?php echo json_encode($elo); ?>;
        const winners = <?php echo json_encode($winners); ?>;
        const losers = <?php echo json_encode($losers); ?>;

        console.log(elo_changes);
        elo_arr = [1000]
        for (let i = 0; i < elo_changes.length; i++) {
          elo_arr.push(elo_arr[elo_arr.length - 1] + elo_changes[i]);
        }

        labels = ["Starting Elo"]
        for (var i = 0; i < winners.length; i++){
            labels.push(winners[i] + " beat " + losers[i])
        }
        console.log(winners)
        console.log(labels)
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

        function imgBecome(url){
            document.getElementById('crd-prvw').src = url
            document.getElementById('crd-prvw').style.display="block"
        }
        function imgLeave(){
            document.getElementById('crd-prvw').style.display="none"
        }

        const cardUrls = [
            <?php foreach ($mb_cards as $card): ?>
                "<?= htmlspecialchars($card['url']) ?>",
            <?php endforeach; ?>
            <?php foreach ($sb_cards as $card): ?>
                "<?= htmlspecialchars($card['url']) ?>",
            <?php endforeach; ?>
        ];

        // Preload all images
        const preloadedImages = [];
        cardUrls.forEach(url => {
            const img = new Image();
            img.src = url; // Browser will start loading the image
            preloadedImages.push(img);
        });

        function goToDeck(id){
            window.location = "deck.php?id=" + id
        }


    </script>
</body>
</html>
