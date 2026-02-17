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
$stmt = $pdo->query('WITH deck_totals AS (
    SELECT COUNT(*) AS total_decks
    FROM decks
),

match_totals AS (
    SELECT COUNT(*) AS total_matches
    FROM matches
),

-- Deck-level stats
deck_card_stats AS (
    SELECT
        cid.card_id,
        COUNT(DISTINCT cid.deck_id) AS decks_containing_card,
        SUM(cid.quantity) AS total_quantity_decks,
        ROUND(AVG(d.elo), 2) AS average_elo
    FROM card_in_deck cid
    JOIN decks d
        ON d.id = cid.deck_id
    WHERE cid.mainboard = 1
    GROUP BY cid.card_id
),

-- Match-level presence + total quantity (corrected, no duplication)
match_card_stats AS (
    SELECT
        x.card_id,
        COUNT(*) AS matches_with_card,
        SUM(x.total_quantity_in_match) AS total_quantity_matches
    FROM (
        SELECT
            m.id AS match_id,
            COALESCE(w.card_id, l.card_id) AS card_id,
            COALESCE(w.quantity, 0) + COALESCE(l.quantity, 0) AS total_quantity_in_match
        FROM matches m

        LEFT JOIN card_in_deck w
            ON w.deck_id = m.winner_id
           AND w.mainboard = 1

        LEFT JOIN card_in_deck l
            ON l.deck_id = m.loser_id
           AND l.card_id = w.card_id
           AND l.mainboard = 1

        WHERE w.card_id IS NOT NULL
    ) x
    GROUP BY x.card_id
)

SELECT
    c.id as card_id,
    c.card_name,
    c.image_url,

    dcs.average_elo,


    ROUND(
        100.0 * dcs.decks_containing_card
        / dt.total_decks,
        2
    ) AS percentage_playrate,

    dcs.total_quantity_decks,

    ROUND(
        100.0 * mcs.matches_with_card
        / mt.total_matches,
        2
    ) AS percentage_matches_with_card,

    mcs.total_quantity_matches

FROM cards c
LEFT JOIN deck_card_stats dcs
    ON dcs.card_id = c.id
LEFT JOIN match_card_stats mcs
    ON mcs.card_id = c.id
CROSS JOIN deck_totals dt
CROSS JOIN match_totals mt

ORDER BY percentage_playrate DESC;
');
$oldstmt = $pdo->query('WITH avg_elo AS (
    SELECT
        c.id AS card_id,
        c.card_name,
        ROUND(AVG(d.elo), 2) AS average_elo
    FROM card_in_deck cid
    INNER JOIN cards c   ON cid.card_id = c.id
    INNER JOIN decks d   ON cid.deck_id = d.id
    where cid.mainboard = 1
    GROUP BY c.id, c.card_name
),card_winloss AS (
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
    a.card_id,
    a.card_name,
    d.image_url,
    a.average_elo,
    ROUND(
        100.0 * w.wins / NULLIF(w.wins + w.losses - w.both_sides, 0),
        2
    ) AS winrate_percentage,
    ROUND(
        100.0 * w.wins / NULLIF(w.wins + w.losses, 0),
        2
    ) AS winrate_percentage_2,
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

$othercards = $oldstmt->fetchAll(PDO::FETCH_ASSOC);
$othercardsbyid = [];
foreach ($othercards as &$card) {
    $othercardsbyid[$card['card_id']] = $card;
}
foreach ($cards as &$card) {
    $id = $card['card_id'];

    if (isset($othercardsbyid[$id]['winrate_percentage'])) {
        $card['winrate_percentage'] =
            $othercardsbyid[$id]['winrate_percentage'];
    } else {
        $card['winrate_percentage'] = 0;
    }
}
unset($card);
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
        100.0 * (w.wins - w.both_sides) / NULLIF(w.wins + w.losses - w.both_sides, 0),
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
$jsonsbcards = json_encode($sbcards);

$stmt= $pdo->query('

with card_combos as (
select distinct
    c1.card_id id1,
    c2.card_id id2
from card_in_deck c1
inner join card_in_deck c2 
    on c1.deck_id = c2.deck_id
    and c1.card_id > c2.card_id
),
decks_combos as (select distinct
    cc.*,
    cid1.deck_id
from card_combos cc
left join card_in_deck cid1
    on cid1.card_id = cc.id1
left join card_in_deck cid2
    on cid2.card_id = cc.id2
where cid1.deck_id = cid2.deck_id
)
select 
    c1.card_name as name1,
    c2.card_name as name2,
    sum(case when m.winner_id = dc.deck_id then 1 else 0 end) matches_won,
    sum(case when m.loser_id = dc.deck_id then 1 else 0 end) matches_lost,
    sum(
   	case
        WHEN m.winner_id = dc.deck_id
         AND EXISTS (
               SELECT 1
               FROM card_in_deck cid3
               WHERE cid3.deck_id = m.loser_id
                 AND cid3.card_id = dc.id1
         )
	AND EXISTS (
               SELECT 1
               FROM card_in_deck cid4
               WHERE cid4.deck_id = m.loser_id
                 AND cid4.card_id = dc.id2
         )
        THEN 1
        END
   ) AS both_sides,
    ROUND(
        100.0 * sum(case when m.winner_id = dc.deck_id then 1 else 0 end) / 
NULLIF(
	sum(case when m.winner_id = dc.deck_id then 1 else 0 end) + 
	sum(case when m.loser_id = dc.deck_id then 1 else 0 end) - 
	sum(
   		case
        		WHEN m.winner_id = dc.deck_id
         		AND EXISTS (
              			SELECT 1
               			FROM card_in_deck cid3
               			WHERE cid3.deck_id = m.loser_id
                 		AND cid3.card_id = dc.id1
         		)
			AND EXISTS (
	               		SELECT 1
               			FROM card_in_deck cid4
               			WHERE cid4.deck_id = m.loser_id
                 		AND cid4.card_id = dc.id2
         		)
        		THEN 1
        	ELSE 0 END
   	)
, 0),
        2
    ) AS winrate_percentage,
   
   count(distinct dc.deck_id) decks,
   count(distinct m.id) matches
from decks_combos dc
left join matches m
    on dc.deck_id in (m.winner_id, m.loser_id)
left join cards c1
    on c1.id = dc.id1
left join cards c2
    on c2.id = dc.id2
group by dc.id1, dc.id2, c1.card_name, c2.card_name
order by winrate_percentage desc
');

//$card_pairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
//$jsoncards2 = json_encode($card_pairs);
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

        #p3{
            background-color: #444
        }



    </style>
</head>
<body>

    <div class="bg-bg">
        <div class="bg-img">
            <div class="bg-fg">

                <!-- Page Buttons -->

                <?php include __DIR__ . '/partials/PageButtons.php'; ?>

                <!-- Tab Buttons -->

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
                        <strong>Mainboard Cards:</strong><br>
                        <table style="table-layout:fixed; width:90%">
                            <tbody id="mainboardBody"></tbody>
                        </table>
                    </div>

                    <div id = "page2" style="display:none">
                        <strong>Sideboard Cards:</strong><br>
                        <table style="table-layout:fixed; width:90%">
                            <tbody id="sideboardBody"></tbody>
                        </table>
                    </div>



                    <div id = "page3" style="display:none; height:100%">
                        <strong> Winrate vs Playrate </strong>
                        <a style="position:absolute; right:10%", onclick="switchGraph()"> switch </a>
                        <canvas id="cardgraph" style="height:70vh"></canvas>
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


    const sbdata = <?php echo $jsonsbcards; ?>


    
    console.log(data)
    const graphData = data.map(point => ({
        x: point.winrate_percentage,
        y: point.percentage_playrate,
        backgroundColor: `rgba(${(point.average_elo - 700)/2.5},${(point.average_elo - 700)/2.5},${(point.average_elo - 700)/2}, 1)`,
        label: `${point.card_name}: PR: ${point.percentage_playrate}% - ${WR: point.winrate_percentage} - AE: ${point.average_elo}`
    }))

    
    const graphData2 = data.map(point => ({
        x: point.total_quantity_decks,
        y: point.percentage_playrate,
        backgroundColor: `rgba(${(point.average_elo - 700)/2.5},${(point.average_elo - 700)/2.5},${(point.average_elo - 700)/2}, 1)`,
        label: `${point.card_name}: ${point.percentage_playrate}% play rate - ${point.total_quantity_decks} copies accross all decks decks`
    }))

    var graph = 1
    var graphinstance = null;
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



                graphinstance = new Chart(document.getElementById("cardgraph"), {
                    type: "scatter",
                    data: {
                      datasets: [{
                        label: 'Playrate vs Winrate',
                        data: graphData,
                        pointBackgroundColor: graphData.map(p => p.backgroundColor),
                        pointRadius: 5,
                        pointBorderWidth: 0
                      }]
                    },
                    options: {
                      scales: {
                        x: {
                            min: -1,
                            max: 101,
                            title: {
                              display: true,
                              text: '% of Decks Containing Card',
                              color: "#ddd"

                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,

                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 100) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        },
                        y: {
                              min: -1,
                              max: 101,
                              suggestedMin: -3,
                              suggestedMax: 103,
                            title: {
                              display: true,
                              text: '% of Matches Won Against Decks Without Card',
                              color: "#ddd"
                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,
                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 100) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        }
                      },
                      plugins: {

                        legend: {
                          labels: { color: "#ddd" }
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              return context.raw.label; // use the label we built into graphData
                            }
                          }
                        }
                      },
                      maintainAspectRatio: false
                    }
                  });

            }
        }

        function switchGraph(){
            graphinstance.destroy();
            if (graph == 1){
                graph = 2
                graphinstance = new Chart(document.getElementById("cardgraph"), {
                    type: "scatter",
                    data: {
                      datasets: [{
                        label: 'Playrate vs Winrate',
                        data: graphData2,
                        pointBackgroundColor: '#88b',
                        pointRadius: 3,
                        pointBorderWidth: 0
                      }]
                    },
                    options: {
                      scales: {
                        x: {
                            min: -1,
                            max: 501,
                            title: {
                              display: true,
                              text: 'Total Quantity accross all decks',
                              color: "#ddd"

                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,

                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 500) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        },
                        y: {
                              min: -1,
                              max: 101,
                              suggestedMin: -3,
                              suggestedMax: 103,
                            title: {
                              display: true,
                              text: 'Playrate (%)',
                              color: "#ddd"
                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,
                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 100) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        }
                      },
                      plugins: {

                        legend: {
                          labels: { color: "#ddd" }
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              return context.raw.label;
                            }
                          }
                        }
                      },
                      maintainAspectRatio: false
                    }
                  });
            } else {
                graph = 1
                graphinstance = new Chart(document.getElementById("cardgraph"), {
                    type: "scatter",
                    data: {
                      datasets: [{
                        label: 'Playrate vs Winrate',
                        data: graphData,
                        pointBackgroundColor: graphData.map(p => p.backgroundColor),
                        pointRadius: 5,
                        pointBorderWidth: 0
                      }]
                    },
                    options: {
                      scales: {
                        x: {
                            min: -1,
                            max: 101,
                            title: {
                              display: true,
                              text: 'Decks',
                              color: "#ddd"

                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,

                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 100) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        },
                        y: {
                              min: -1,
                              max: 101,
                              suggestedMin: -3,
                              suggestedMax: 103,
                            title: {
                              display: true,
                              text: 'Winrate (%)',
                              color: "#ddd"
                            },
                          ticks: {
                            color: "#ddd",
                            stepSize: 1,
                              callback: function(value) {
                                if (value % 10 === 0 && value >= 0 && value <= 100) {
                                  return value;
                                }
                                return null;
                              }
                            },
                          grid: { color: "#444" }
                        }
                      },
                      plugins: {

                        legend: {
                          labels: { color: "#ddd" }
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              return context.raw.label;
                            }
                          }
                        }
                      },
                      maintainAspectRatio: false
                    }
                 });

            }
            
        }

    function renderSideboard(data) {
        const body = document.getElementById("sideboardBody");
        body.innerHTML = "";

        data.forEach((card, index) => {
            body.innerHTML += `
            <tr>
                <td style="width:5"><div class="n c${index + 1}"><span id="r${index + 1}"> ${index + 1}.</span></td>
                <td style="width:25%"><img src="${card.image_url}" style="width:9vw; border-radius:10px; border:3px #aef solid;"></td>
                <td style="width:15%">${card.card_name}</td>
                <td style="width:20%" onclick="sortSideboard('elo')"><div class="ca">${Math.floor(card.average_elo)}<br><span style="color:#aaa;font-family:monospace;">Average Elo</span></div></td>
                <td style="width:20%" onclick="sortSideboard('winrate')"><div class="ca">${Math.floor(card.winrate_percentage)}%<br><span style="color:#aaa;font-family:monospace;">Winrate</span></div></td>
                <td style="width:15%" onclick="sortSideboard('playrate')"><div class="ca">${Math.floor(card.percentage_playrate)}%<br><span style="color:#aaa;font-family:monospace;">Playrate</span></div></td>
            </tr>`;
        });
    }

    function sortSideboard(type) {
        if (type === "playrate") {
            sbdata.sort((a, b) => b.percentage_playrate - a.percentage_playrate);
        } else if (type === "winrate") {
            sbdata.sort((a, b) => b.winrate_percentage - a.winrate_percentage);
        } else if (type === "elo") {
            sbdata.sort((a, b) => b.average_elo - a.average_elo);
        }

        renderSideboard(sbdata);
    }

    function renderMainboard(data) {
        const body = document.getElementById("mainboardBody");
        body.innerHTML = "";

        data.forEach((card, index) => {
            body.innerHTML += `
            <tr>
                <td style="width:5"><div class="n c${index + 1}"><span id="r${index + 1}"> ${index + 1}.</span></td>
                <td style="width:25%"><img src="${card.image_url}" style="width:9vw; border-radius:10px; border:3px #aef solid;"></td>
                <td style="width:15%">${card.card_name}</td>
                <td style="width:20%" onclick="sortMainboard('elo')"><div class="ca">${Math.floor(card.average_elo)}<br><span style="color:#aaa;font-family:monospace;">Average Elo</span></div></td>
                <td style="width:20%" onclick="sortMainboard('winrate')"><div class="ca">${Math.floor(card.winrate_percentage)}%<br><span style="color:#aaa;font-family:monospace;">Winrate</span></div></td>
                <td style="width:15%" onclick="sortMainboard('playrate')"><div class="ca">${Math.floor(card.percentage_playrate)}%<br><span style="color:#aaa;font-family:monospace;">Playrate</span></div></td>
            </tr>`;
        });
    }

    function sortMainboard(type) {
        if (type === "playrate") {
            data.sort((a, b) => b.percentage_playrate - a.percentage_playrate);
        } else if (type === "winrate") {
            data.sort((a, b) => b.winrate_percentage - a.winrate_percentage);
        } else if (type === "elo") {
            data.sort((a, b) => b.average_elo - a.average_elo);
        }

        renderMainboard(data);
    }
    
    renderSideboard(sbdata);
    renderMainboard(data);
    </script>
</body>
</html>
