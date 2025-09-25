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
WITH avg_elo AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        ROUND(AVG(d.elo), 2) AS average_elo
    FROM card_in_deck cid
    INNER JOIN cards c   ON cid.card_id = c.id
    INNER JOIN decks d   ON cid.deck_id = d.id
    where cid.mainboard = 1
    GROUP BY c.id, c.card_name
),
card_winloss AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        COUNT(CASE WHEN m.winner_id = cid.deck_id THEN 1 END) AS wins,
        COUNT(CASE WHEN m.loser_id  = cid.deck_id THEN 1 END) AS losses,
        COUNT(
            CASE
                WHEN m.winner_id = cid.deck_id
                 AND EXISTS (
                       SELECT 1
                       FROM card_in_deck cid2
                       WHERE cid2.deck_id = m.loser_id
                         AND cid2.card_id = cid.card_id
                         and cid2.mainboard = 1
                 )
                THEN 1
            END
        ) AS both_sides
    FROM matches m
    JOIN card_in_deck cid
        ON cid.deck_id IN (m.winner_id, m.loser_id)
    JOIN cards c
        ON c.id = cid.card_id
    where cid.mainboard = 1
    GROUP BY c.id, c.card_name
),
dcc AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        c.image_url,
        COUNT(cid.deck_id) AS decks_containing_card
    FROM cards c
    LEFT JOIN card_in_deck cid
        ON cid.card_id = c.id
       AND cid.mainboard = 1
    GROUP BY c.id, c.card_name, c.image_url
)
SELECT
    a.card_name,
    d.image_url,
    a.average_elo,
    ROUND(
        100.0 * w.wins / NULLIF(w.wins + w.losses - w.both_sides, 0),
        2
    ) AS winrate_percentage,
    ROUND(
        d.decks_containing_card * 100.0 / (SELECT COUNT(*) FROM decks),
        2
    ) AS percentage_playrate
FROM avg_elo a
JOIN card_winloss w
    ON a.card_id = w.card_id
JOIN dcc d
    ON a.card_id = d.card_id
ORDER BY percentage_playrate DESC;
');
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;
$jsoncards = json_encode($cards);

$stmt = $pdo->query('
WITH avg_elo AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        ROUND(AVG(d.elo), 2) AS average_elo
    FROM card_in_deck cid
    INNER JOIN cards c   ON cid.card_id = c.id
    INNER JOIN decks d   ON cid.deck_id = d.id
    where cid.mainboard = 0
    GROUP BY c.id, c.card_name
),
card_winloss AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        COUNT(CASE WHEN m.winner_id = cid.deck_id THEN 1 END) AS wins,
        COUNT(CASE WHEN m.loser_id  = cid.deck_id THEN 1 END) AS losses,
        COUNT(
            CASE
                WHEN m.winner_id = cid.deck_id
                 AND EXISTS (
                       SELECT 1
                       FROM card_in_deck cid2
                       WHERE cid2.deck_id = m.loser_id
                         AND cid2.card_id = cid.card_id
                         and cid2.mainboard = 0
                 )
                THEN 1
            END
        ) AS both_sides
    FROM matches m
    JOIN card_in_deck cid
        ON cid.deck_id IN (m.winner_id, m.loser_id)
    JOIN cards c
        ON c.id = cid.card_id
    where cid.mainboard = 0
    GROUP BY c.id, c.card_name
),
dcc AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        c.image_url,
        COUNT(cid.deck_id) AS decks_containing_card
    FROM cards c
    LEFT JOIN card_in_deck cid
        ON cid.card_id = c.id
       AND cid.mainboard = 0
    GROUP BY c.id, c.card_name, c.image_url
)
SELECT
    a.card_name,
    d.image_url,
    a.average_elo,
    ROUND(
        100.0 * w.wins / NULLIF(w.wins + w.losses - w.both_sides, 0),
        2
    ) AS winrate_percentage,
    ROUND(
        d.decks_containing_card * 100.0 / (SELECT COUNT(*) FROM decks),
        2
    ) AS percentage_playrate
FROM avg_elo a
JOIN card_winloss w
    ON a.card_id = w.card_id
JOIN dcc d
    ON a.card_id = d.card_id
ORDER BY percentage_playrate DESC;
');
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
    <link rel="stylesheet" href="unres.css">
    <style>



        .bg-fg{
          background-image: url("images/vb3.png");
        }

        .bg-img{
          background-image: url("images/vb2.png");
        }

        .bg-bg{
          background-image: url("images/vb1.png");
        }







        #lb {
          width:50%;
          height:70%;
        }


        .illustration img{
            width:65px;
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
                <a class="tab" id="ct1" onclick="switchTab(1)">
                    <img src="https://cdn-icons-png.flaticon.com/128/4391/4391661.png"/>
                </a>
                <a class="tab" id="ct2" onclick="switchTab(2)">
                    <img src="https://cdn-icons-png.flaticon.com/128/2137/2137887.png"/>
                </a>
                <a class="tab" id="ct3" onclick="switchTab(3)">
                    <img src="https://cdn-icons-png.flaticon.com/128/4961/4961626.png"/>
                </a>
                <div id="lb">
                    <div id = "page1">
                        <strong>Most Played Mainboard Cards:</strong><br>
                        <table style="table-layout:fixed; width:90%">
                        <?php foreach ($cards as $card): ?>
                            <tr>
                                <td style="width:5%;">
                                    <div class="n c<?= $rank?>"><span id="r<?= $rank?>"><?= $rank?>.</span></div>
                                </td><td style="width:25%;">
                                    <img src="<?= htmlspecialchars($card['image_url']) ?>" style = "width:9vw; border-radius:10px; border:3px #aef solid;">
                                </td><td style="width:15%;">
                                    <?= htmlspecialchars($card['card_name']) ?>
                                </td><td  style="width:20%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['average_elo']))[0]?>
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Average Elo</span></div>
                                </td><td  style="width:20%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['winrate_percentage']))[0]?>%
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Winrate</span></div>
                                </td><td  style="width:15%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['percentage_playrate']))[0]?>%
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Playrate</span></div>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>

                    <div id = "page2" style="display:none">
                        <strong>Most Played Sideboard Cards:</strong><br>
                        <table style="table-layout:fixed; width:90%;">
                        <?php foreach ($sbcards as $card): ?>
                            <tr>
                                <td style="width:5%;">
                                    <div class="n c<?= $sbrank?>"><span id="r<?= $sbrank?>"><?= $sbrank?>.</span></div>
                                </td><td style="width:25%;">
                                    <img src="<?= htmlspecialchars($card['image_url']) ?>" style = "width:9vw; border-radius:10px; border:3px #aef solid;">
                                </td><td style="width:15%;">
                                    <?= htmlspecialchars($card['card_name']) ?>
                                </td><td  style="width:20%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['average_elo']))[0]?>
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Average Elo</span></div>
                                </td><td  style="width:20%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['winrate_percentage']))[0]?>%
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Winrate</span></div>
                                </td><td  style="width:15%;">
                                    <div class="ca"><?= explode('.',htmlspecialchars($card['percentage_playrate']))[0]?>%
                                    <br><span style="color:#aaa;font-family: 'JetBrains Mono', 'IBM Plex Mono', 'Source Code Pro', monospace;">Playrate</span></div>
                                </td>
                            </tr>
                            <?php $sbrank++; ?>
                        <?php endforeach; ?>
                        </table>
                    </div>



                    <div id = "page3" style="display:none">
                        <strong> Stats or something </strong>
                        <canvas id="cardgraph"></canvas>
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

    const data = <?php echo $jsoncards; ?>
    console.log(data)

    function switchTab(n){
            tab1 = document.getElementById("ct1")
            tab2 = document.getElementById("ct2")
            tab3 = document.getElementById("ct3")
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



                new Chart(document.getElementById("cardgraph"), {
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

            }
        }
    </script>
</body>
</html>
