<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);



include "/var/www/html/Unres/db.php";


$id = $_GET["id"];


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
        d.elo AS elo,
        t2.position_change
FROM colours_of_decks cod
RIGHT JOIN decks d ON d.id = cod.deck_id
LEFT join
(
    WITH matches_in_order AS (
        SELECT
            ec.*,
            ROW_NUMBER() OVER (PARTITION BY deck_id ORDER BY id DESC) AS rn
        FROM elo_changes ec
    ),
    elo_1_game_ago AS (
        SELECT
            deck_id,
            SUM(elo_change) AS elo_1_game_ago
        FROM matches_in_order
        WHERE rn > 1
        GROUP BY deck_id
    ),
    past_elo AS (
        SELECT
            deck_id,
            RANK() OVER (ORDER BY elo_1_game_ago DESC) AS position
        FROM elo_1_game_ago
    )
    SELECT
        d.id As deck,
        CASE
            WHEN RANK() OVER (ORDER BY d.elo DESC) = p.position THEN 0
            WHEN RANK() OVER (ORDER BY d.elo DESC) > p.position THEN -1
            ELSE 1
        END AS position_change
    FROM decks d
    JOIN past_elo p ON p.deck_id = d.id
    order by p.position desc
) t2 on d.id = t2.deck
GROUP BY id, position_change
ORDER BY elo DESC;');
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);



$targetSize = 128;
$currentSize = count($decks);

for ($i = 0; $i < 3; $i++) {
    $decks[] = [
        'id' => $i,
        'colour' => 0,
        'cid' => 'Ukn01',
        'name' => 'Unknown deck',
        'elo' => 1000,
        'position_change' => 0
    ];
}


$decksbyid = [];
$rank = 1;
foreach ($decks as $deck) {
    $deck['rank'] = $rank;
    $decksbyid[$deck['id']] = $deck;
    $rank += 1;
}

$stmt = $pdo->prepare(
    'SELECT * FROM matches_in_tourney WHERE tournament_id = :id'
);

$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

$matchesbyid = [];
foreach ($matches as &$match) {
    if (!is_numeric($match['leftid'])) {// checks if in the form [W/L][num], if this is true, the rightid equivalent will also be true
        if ($match["leftid"][0] === 'W') { 
            if ($matchesbyid[(int)substr($match["leftid"], 1)]["winnerid"] !== null){
                $match["leftid"] = $matchesbyid[(int)substr($match["leftid"], 1)]["winnerid"];
            }
        } else {
            if ($matchesbyid[(int)substr($match["leftid"], 1)]["winnerid"] !== null){
                $match["leftid"] = $matchesbyid[(int)substr($match["leftid"], 1)]["loserid"];
            }
        }
        if ($match["rightid"][0] === 'W') { 
            if ($matchesbyid[(int)substr($match["rightid"], 1)]["winnerid"] !== null){
                $match["rightid"] = $matchesbyid[(int)substr($match["rightid"], 1)]["winnerid"];
            }
        } else {
            if ($matchesbyid[(int)substr($match["rightid"], 1)]["winnerid"] !== null){
                $match["rightid"] = $matchesbyid[(int)substr($match["rightid"], 1)]["loserid"];
            }
        }
    }
    $matchesbyid[$match['id']] = $match;
}

$stmt = $pdo->prepare(
    'SELECT MAX(round) FROM matches_in_tourney WHERE tournament_id = :id'
);
$stmt->execute(['id' => $id]);

$maxRound = $stmt->fetchColumn();

$venvPython = '/var/www/Unres-Meta/venv/bin/python';
$pythonScript = 'elo_changes_by_archetype.py';
$command = 'cd /var/www/Unres-Meta/db && ' . escapeshellcmd($venvPython) . ' ' . ($pythonScript) . ' ' . ' 2>&1';
$arch_output = shell_exec($command);

$arch_output = str_replace("'", "\'", $arch_output);

// --- CHECK FOR NEW CHANGES

$changes_json = file_get_contents('/var/www/Unres-Meta/db/db_changes.json');
$changes_data = json_decode($changes_json, true);

$changed_deck_ids = [];
$added_deck_ids = [];
$now = new DateTime('now', new DateTimeZone('UTC'));
$oneWeekAgo = (clone $now)->modify('-7 days');

foreach ($changes_data as $change_batch) {
    //convert timestamp to DateTime object for comparison
    $timestamp = new DateTime($change_batch["timestamp"], new DateTimeZone('UTC'));

    if ($timestamp > $oneWeekAgo){
        foreach ($change_batch["logs"] as $change){
            if ($change["change_type"] == "added_deck"){
                $added_deck_ids[] = $change["deck_id"];
            }
            else{
                $changed_deck_ids[] = $change["deck_id"];
            }
        }
    }
}

?>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <title>Unrestricted Vintage Tournament</title>
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <meta name="description" content="Create bots to compete in fun minigames! :)">
    <meta property="og:title" content="Unrestricted Vintage Tournament">
    <meta property="og:description" content="The best decks in unres compete for the ultimate prize, bragging rights!">
    <meta property="og:image" content="Images/6.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="unres.css">
    <style>
        .bg-bg, .bg-img, .bg-img2, .bg-fg {
          height:100%;
          background-size:110% auto;
          background-position: 50% 50%;
          position: relative;
          transition: background-position 0.15s;
        }



        .bg-fg{
          background-image: url("images/fon4.png");
        }
        
        .bg-img2{
          background-image: url("images/fon3.png");
        }
        
        .bg-img{
          background-image: url("images/fon2.png");
        }

        .bg-bg{
          background-image: url("images/fon1.png");
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

    </style>
</head>
<body>
    <div class="bg-bg">
        <div class="bg-img">
        <div class="bg-img2">
            <div class="bg-fg">

                <!-- Page Buttons -->

                <a href="Match.php" id="p1" class="pButton">
                    <img src="https://cdn-icons-png.flaticon.com/128/9795/9795832.png">
                    <span>Match Submission</span>
                </a>
                <a href="Leaderboard.php" id="p2" class="pButton">
                    <img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png">
                    <span>Leaderboard</span>
                </a>
                <a href="cards.php" id="p3" class="pButton">
                    <img src="https://cdn-icons-png.flaticon.com/128/6831/6831865.png">
                    <span>Top Cards</span>
                </a>


                <!-- Tab Buttons -->

                <a class="tab" id="wt1" onclick="incrementTab(1, <?=$maxRound?>)">
                    <img src="https://cdn-icons-png.flaticon.com/128/17222/17222665.png"/>
                </a>
                <a class="tab" id="wt2" onclick="incrementTab(2,<?= $maxRound?>)">
                    <img src="https://cdn-icons-png.flaticon.com/512/17222/17222669.png"/>
                </a>

                <div style="width:50%" id="lb">
                    <?php for($i=1;$i<$maxRound + 1;$i++):?>
                        <?php 
                            if ($i==1){
                                $style = "block";
                            } else {
                                $style = "none";
                            }
                        ?>
                        <div id="page<?= $i ?>" style="display:<?= $style?>">
                            <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/5200/5200866.png"/></div>
                            <br>
                            <h3 style="text-align:center;"> Round <?= $i ?> </h3>

                            <table style="border-collapse: separate;border-spacing: 0 12px; table-layout:fixed; left:calc(10% - 33px); width:calc(80% + 36px)">
                                <colgroup>
                                    <col style="width:33px">   <!-- match number-->
                                    <col style="width:30px">   <!-- left icon -->
                                    <col style="width:calc(50% - 95px)">  <!-- left name -->
                                    <col style="width:50px">   <!-- left score -->
                                    <col style="width:33px">   <!-- VS -->
                                    <col style="width:50px">   <!-- right score -->
                                    <col style="width:calc(50% - 95px)">  <!-- right name -->
                                    <col style="width:30px">   <!-- right icon -->
                                </colgroup>
                                <?php foreach ($matches as $match): ?>
                                    <?php if ($match['round'] !== $i) continue; ?>
                                    <tr>
                                        <td>
                                            <div class="n c2 ca" style="margin:auto"><span id="r2"><?= $match['id'] + 1?></span></div>
                                        </td>
                                        <?php if (is_numeric($match["leftid"])): ?>
                                            <?php
                                                $class = " ";
                                                if ($match["leftid"] == $match["winnerid"]){
                                                    $class = " winner";
                                                }
                                                if ($match["leftid"] == $match["loserid"]){
                                                    $class = " loser";
                                                }
                                            ?>
                                            <td class = 'trl<?= $class;?>'>
                                                <?php $imageUrl = "images/".$decksbyid[$match["leftid"]]['colour'].".png"; ?>
                                                <img class="lbimg" src="<?= htmlspecialchars($imageUrl) ?>" alt="color">
                                            </td><td class = 'trm limit<?= $class;?>' onclick="submitMatch(<?= $match["id"];?>, <?= $match["leftid"];?>, <?= $match["rightid"];?>, '<?= addslashes($decksbyid[$match["leftid"]]['name']); ?>')">
                                                <?= htmlspecialchars($decksbyid[$match["leftid"]]['name']) ?>
                                                <a style="width:20px;" href=deck.php?id=<?= $match["leftid"]?> onclick="event.stopPropagation();">
                                                <img style="width:20px;" src="https://cdn-icons-png.flaticon.com/512/6938/6938456.png" title="View decklist">
                                                </a>
                                                <?php
                                                if(in_array($decksbyid[$match["leftid"]]['id'],$added_deck_ids)){
                                                    echo '<img style="width:20px;" src="https://cdn-icons-png.flaticon.com/128/3161/3161551.png" title="New Deck!">';
                                                }
                                                if(in_array($decksbyid[$match["leftid"]]['id'],$changed_deck_ids)){
                                                    echo '<img style="width:20px;" src="https://cdn-icons-png.flaticon.com/128/616/616656.png" title="This deck has new changes!">';
                                                }
                                                ?>
                                                

                                                <br>
                                                <span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $decksbyid[$match["leftid"]]['cid'] ?></span>
                                                <span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $decksbyid[$match["leftid"]]['rank'] ?></span>

                                            </td><td class="trr ra<?= $class;?>">
                                                <span class="ra"><?= explode('.',htmlspecialchars($decksbyid[$match["leftid"]]['elo']))[0] ?></span>
                                            </td>
                                        <?php else: ?>
                                            <td class = "trl ra">
                                                <img src="images/0.png" class="lbimg"/>
                                            </td>
                                            <?php if ($match["leftid"][0] === 'W'): ?>
                                                <td class = 'trm'>Winner of match <?php echo (int)substr($match["leftid"], 1) + 1; ?></td>
                                            <?php else: ?>
                                                <td class = 'trm'>Loser of match <?php echo (int)substr($match["leftid"], 1) + 1; ?></td>
                                            <?php endif; ?>
                                            <td class = 'trr ra'>
                                                
                                            </td>
                                        <?php endif; ?>

                                        <td class="ca">
                                            <div class="n c1 ca" style="margin:auto"><span id="r1">VS</span></div>
                                        </td>

                                        <?php if (is_numeric($match["rightid"])): ?>
                                            <?php
                                                $class = " ";
                                                if ($match["rightid"] == $match["winnerid"]){
                                                    $class = " winner";
                                                }
                                                if ($match["rightid"] == $match["loserid"]){
                                                    $class = " loser";
                                                }
                                            ?>
                                            <td class = 'trl<?= $class;?>'>
                                                <span><?= explode('.',htmlspecialchars($decksbyid[$match["rightid"]]['elo']))[0] ?></span>
                                            </td><td class="trm ra limit<?= $class;?>" onclick="submitMatch(<?= $match["id"];?>, <?= $match["rightid"];?>, <?= $match["leftid"];?>, '<?= addslashes($decksbyid[$match["leftid"]]['name']); ?>')">
                                                <?= htmlspecialchars($decksbyid[$match["rightid"]]['name']) ?>
                                                <a style="width:20px;" href=deck.php?id=<?= $match["rightid"]?> onclick="event.stopPropagation();">
                                                <img style="width:20px;" src="https://cdn-icons-png.flaticon.com/512/6938/6938456.png" title="View decklist">
                                                </a>
                                                <?php
                                                if(in_array($decksbyid[$match["rightid"]]['id'],$added_deck_ids)){
                                                    echo '<img style="width:20px;" src="https://cdn-icons-png.flaticon.com/128/3161/3161551.png" title="New Deck!">';
                                                }
                                                if(in_array($decksbyid[$match["rightid"]]['id'],$changed_deck_ids)){
                                                    echo '<img style="width:20px;" src="https://cdn-icons-png.flaticon.com/128/616/616656.png" title="This deck has new changes!">';
                                                }
                                                ?>
                                                <br>
                                                <span class="ra" style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $decksbyid[$match["rightid"]]['cid'] ?></span>
                                                <span class="ra" style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">#<?= $decksbyid[$match["rightid"]]['rank'] ?></span>
                                            </td><td class = 'trr<?= $class;?>'>
                                                <?php $imageUrl = "images/".$decksbyid[$match["rightid"]]['colour'].".png"; ?>
                                                <img class="ra lbimg" src="<?= htmlspecialchars($imageUrl) ?>" alt="color">
                                            </td>
                                        <?php else: ?>
                                            <td class = 'trl ra'>
                                                
                                            </td>
                                            <?php if ($match["rightid"][0] === 'W'): ?>
                                                <td class = 'trm'>Winner of match <?php echo (int)substr($match["rightid"], 1) + 1; ?></td>
                                            <?php else: ?>
                                                <td class = 'trm'>Loser of match <?php echo (int)substr($match["rightid"], 1) + 1; ?></td>
                                            <?php endif; ?>
                                            <td class = "trr ra">
                                                <img src="images/0.png" class="lbimg"/>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var currentTab = 1;
        const archetypes = JSON.parse('<?php echo (trim($arch_output)); ?>')
        console.log(archetypes);


        
        const div2 = document.querySelector('.bg-img');
        const div2_2 = document.querySelector('.bg-img2');

        div2.addEventListener('mousemove', (e) => {
          const div1 = document.querySelector('.bg-fg');
          const div3 = document.querySelector('.bg-bg');
          const { width, height } = div2.getBoundingClientRect();

          // Get mouse position relative to the div (0 to 1)
          const x = e.clientX / width;
          const y = e.clientY / height;


          const offsetX = (0.5 - x) * 3;
          const offsetY = (0.5 - y);

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${50 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*2}% ${50 + offsetY*2}%`;
          //div2_2.style.backgroundPosition = `${50 + offsetX}% ${50 + offsetY}%`;
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

                let chartData = archetypes

                // X-axis labels (assuming all series have same length)
                let labels = chartData[Object.keys(chartData)[0]].map((_, i) => `Match ${i+1}`);

                // Convert dictionary to Chart.js datasets
                let datasets = Object.entries(chartData).map(([key, values], i) => ({
                    label: key,
                    data: values,
                    borderColor: `hsl(${i * (360/Object.keys(archetypes).length)}, 70%, 50%)`, // auto-color each line
                    fill: false,
                    backgroundColor: `hsl(${i * (360/Object.keys(archetypes).length)}, 70%, 50%)`,
                    pointRadius: 0,
                    pointHoverRadius: 2
                }));

                new Chart(document.getElementById("elograph"), {
                    type: "line",
                    data: {
                      labels: labels,
                      datasets: datasets
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

                      },
                       interaction: {
                          mode: 'nearest',
                          intersect: false
                        },
                      maintainAspectRatio: false
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

        function incrementTab(dir, maxround){
            console.log("round" + maxround)
            prev = currentTab
            if (dir == 1){
                currentTab -= 1
                if (currentTab==0){
                    currentTab = 1
                }
            }
            if (dir == 2){
                currentTab += 1
                if (currentTab==maxround + 1){
                    currentTab = maxround
                }
            }
            tab1 = document.getElementById("wt1")
            tab2 = document.getElementById("wt2")
            oldtab = document.getElementById("page" + prev)
            newtab = document.getElementById("page" + currentTab)
            if (currentTab==1){
                tab2.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab2.style.backgroundColor = "none"
                tab1.style.backgroundColor = "#1e2833"
                tab1.style.backgroundImage = "none"


            } else if (currentTab==maxround){
                tab2.style.backgroundColor = "#1e2833"
                tab2.style.backgroundImage = "none"
                tab1.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab1.style.backgroundColor = "none"

                

            }else{
                tab1.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab1.style.backgroundColor = "none"
                tab2.style.backgroundImage = "linear-gradient(to top, black, #1e2833)"
                tab2.style.backgroundColor = "#none"

            }
            
            oldtab.style.display = "none"
            newtab.style.display = "block"
        }


    </script>
</body>
</html>
