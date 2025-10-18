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


$stmt = $pdo->prepare('SELECT c.id as id, c.card_name as name, cid.quantity as n, c.image_url as url
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 1
order by n desc;');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$mb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT c.id as id, c.card_name as name, cid.quantity as n, c.image_url as url
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 0
order by n desc;');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$sb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$decklist = "";

foreach ($mb_cards as $card) {
    $decklist .= $card['n'] . " " . $card['name'] . "\n";
}
$decklist .= "\n";
foreach ($sb_cards as $card) {
    $decklist .= $card['n'] . " " . $card['name'] . "\n";
}



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



// --- DECK CHANGES JSON READING

$changes_json = file_get_contents('/var/www/Unres-Meta/db/db_changes.json');
$changes_data = json_decode($changes_json, true);

$additions = [];
$removals = [];

$now = new DateTime('now', new DateTimeZone('UTC'));
$oneWeekAgo = (clone $now)->modify('-7 days');

foreach ($changes_data as $change_batch) {
    //convert timestamp to DateTime object for comparison
    $timestamp = new DateTime($change_batch["timestamp"], new DateTimeZone('UTC'));

    if ($timestamp > $oneWeekAgo){
        foreach ($change_batch["logs"] as $change){
            if ($change["deck_id"] == $id){
                 $new_change = [
                    "id" => $change["card_id"],
                    "amount" => $change["quantity_after"] - $change["quantity_before"],
                    "mb" => $change["mainboard"]
                ];
                if ($new_change["amount"] > 0){
                    $additions[] = $new_change;
                } else {
                    $removals[] = $new_change;
                }
            }
        }
    }
}


$additions_id = array_column($additions, "id");
$removals_id = array_column($removals, "id");

// ---- FIND CARD INFO FOR CARDS THAT HAVE BEEN COMPLETELY REMOVED

$card_ids = array_column($cards, "id");
$ids_to_check = [];

$full_removals_mb = [];
$full_removals_sb = [];
foreach ($removals_id as $rid){
    if (!in_array($rid, $card_ids)){
        $stmt = $pdo->prepare('
            SELECT card_name as name, id
            FROM cards
            WHERE id = :id;
        ');

        $stmt->execute([':id' => $id]);
        $full_removal_to_add = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach($removals as $rem){
            if($rem["id"] == $rid){
                $full_removal_to_add["mb"] = $rem["mb"];
                $full_removal_to_add["n"] = $rem["amount"];
                break;
            }
        }
        if ($full_removal_to_add["mb"] == 1){
            $full_removals_mb[] = $full_removal_to_add;
        } else {
            $full_removals_sb[] = $full_removal_to_add;
        }
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
    <link rel="stylesheet" href="unres.css">
    <style>

        .bg-fg{
          background-image: url("images/lg3.png");
        }

        .bg-img{
          background-image: url("images/lg2.png");
        }

        .bg-bg{
          background-image: url("images/lg1.png");
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
                    <img src="images/dt1.png"/>
                </a>
                <a class="tab" id="t2" onclick="switchTab(2)">
                    <img src="images/dt2.png"/>
                </a>
                <a class="tab" id="t3" onclick="switchTab(3)">
                    <img src="images/dt3.png"/>
                </a>
                <div id="lb" class="wide">
                    <div id = "page1">
                        <img id="cc" onclick="copyToClipboard()" src="https://cdn-icons-png.flaticon.com/128/4891/4891669.png">
                        <h3 style="text-align:center;"> <?= $deck['name'] ?> </h3>
                        <div id="mb">
                            <strong>Mainboard:</strong>
                            <?php foreach ($mb_cards as $card): ?>
                            <div style="justify-content:space-between;display:flex; width:100%;">
                                <div style="justify-content:space-between;display:flex; width:100%;">
                                    <span
                                        onmouseenter='imgBecome("<?= htmlspecialchars($card['url']) ?>")'
                                        onmouseleave='imgLeave()'
                                        ><?= htmlspecialchars($card['name']) ?></span>
                                    <span><?= htmlspecialchars($card['n']) ?></span>
                                </div>
                                <span style="color:#0f0; width:30px; text-align:right"><?php

                                $amount = "";

                                if (in_array($card['id'], $additions_id)) {
                                    // Find the matching addition entry
                                    foreach ($additions as $a) {
                                        if ($a["id"] == $card["id"] && $a["mb"] == 1) {
                                            $amount = "+" . $a["amount"];
                                        }
                                    }
                                }

                                if (in_array($card['id'], $removals_id)) {
                                    // Find the matching removal entry
                                    foreach ($removals as $r) {
                                        if ($r["id"] == $card["id"] && $r["mb"] == 1) {
                                            $amount = $r["amount"]; // likely negative already
                                        }
                                    }
                                }

                                echo $amount;

                                ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <br>
                        <div id="sb">
                            <strong>Sideboard:</strong>
                            <?php foreach ($sb_cards as $card): ?>
                            <div style="justify-content:space-between;display:flex; width:100%">
                                <div style="justify-content:space-between;display:flex; width:100%">
                                    <span
                                        onmouseenter='imgBecome("<?= htmlspecialchars($card['url']) ?>")'
                                        onmouseleave='imgLeave()'
                                        ><?= htmlspecialchars($card['name']) ?></span>
                                    <span><?= htmlspecialchars($card['n']) ?></span>
                                </div>
                                <span style="color:#0f0; width:30px; text-align:right"><?php

                                $amount = "";

                                if (in_array($card['id'], $additions_id)) {
                                    // Find the matching addition entry
                                    foreach ($additions as $a) {
                                        if ($a["id"] == $card["id"] && $a["mb"] == 0) {
                                            $amount = "+" . $a["amount"];
                                        }
                                    }
                                }

                                if (in_array($card['id'], $removals_id)) {
                                    // Find the matching removal entry
                                    foreach ($removals as $r) {
                                        if ($r["id"] == $card["id"] && $r["mb"] == 0) {
                                            $amount = $r["amount"];
                                        }
                                    }
                                }

                                echo $amount;

                                ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php foreach ($full_removals_sb as $card): ?>
                            <div style="justify-content:space-between;display:flex; width:100%">
                                <div style="justify-content:space-between;display:flex; width:100%">
                                    <span
                                        onmouseenter='imgBecome("<?= htmlspecialchars($card['url']) ?>")'
                                        onmouseleave='imgLeave()'
                                        ><?= htmlspecialchars($card['card_name']) ?></span>
                                    <span><?= htmlspecialchars($card['n']) ?></span>
                                </div>
                                <span style="color:#0f0; width:30px; text-align:right"></span>
                            </div>
                            <?php endforeach; ?>
                            <br><br>
                            <a style="color:#ccc;" href= <?= '"'.$deck['decklist_url'].'"' ?> >Click here for the deck page</a>
                        </div>
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

               <img id="clr-img" src= "<?= $color_url ?>">
            </div>
        </div>
    </div>
    <textarea readonly style="position:absolute; left:-9999px;" id="decklist"><?= addslashes($decklist)?></textarea>
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
                document.getElementById('clr-img').style.display = "block"
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
                document.getElementById('clr-img').style.display = "none"
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
                document.getElementById('clr-img').style.display = "none"
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

        function copyToClipboard(){
          const textarea = document.getElementById("decklist");

          textarea.select();
          textarea.setSelectionRange(0, 99999);

          try {
            document.execCommand("copy");
            alert("Decklist copied to clipboard!");
          } catch (err) {
            alert("Failed to copy");
          }
        }
    </script>
</body>
</html>
