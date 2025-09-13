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
$stmt = $pdo->prepare('SELECT id, decklist_url, ELO, provided_archetype
FROM decks
WHERE id = :id;
');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;

foreach ($decks as $d) {
    $deck = $d;
}

$stmt = $pdo->prepare('SELECT id, decklist_url, ELO, provided_archetype, name
FROM decks
WHERE id = :id;
');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare('SELECT c.card_name as name, cid.quantity as n
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 1');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$mb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT c.card_name as name, cid.quantity as n
FROM card_in_deck cid
inner join cards c on cid.card_id = c.id
where cid.deck_id = :id
and cid.mainboard = 0');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();
$sb_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT elo_change FROM elo_changes
WHERE deck_id = :id');
$stmt->bindParam(':id',$id, PDO::PARAM_INT);
$stmt->execute();

$elo = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $elo[] = (int)$row['elo_change']; // force integer
}


foreach ($decks as $d) {
    $deck = $d;
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
          height:80%;
          overflow-y:scroll;
        }

        ::-webkit-scrollbar{
            display:none;
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



    #mb ,#sb{
        width:45%;
        position:absolute;
        top:25%
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
          background-color:#1e2833;
          position: absolute;
          top:10%;
          height:70px;
          width:80px;
    }

    .tab img{
        width:40px;
        height:auto;
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
                <a class="tab decklist">
                    <img src="https://cdn-icons-png.flaticon.com/128/9874/9874735.png"/>
                </a>
                <div id="lb">
                    <br>
                    <h3 style="text-align:center;"> <?= $deck['name'] ?> </h3>
                    <div id="mb">
                        <strong>Mainboard:</strong>
                        <?php foreach ($mb_cards as $card): ?>
                        <div style="justify-content:space-between;display:flex; width:100%;">
                            <span><?= htmlspecialchars($card['name']) ?></span>
                            <span><?= htmlspecialchars($card['n']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <br>
                    <div id="sb">
                        <strong>Sideboard:</strong>
                        <?php foreach ($sb_cards as $card): ?>
                        <div style="justify-content:space-between;display:flex; width:100%">
                            <span><?= htmlspecialchars($card['name']) ?></span>
                            <span><?= htmlspecialchars($card['n']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <br><br>
                        <a style="color:#ccc;" href= <?= '"'.$deck['decklist_url'].'"' ?> >Click here for the deck page</a>
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


          const offsetX = (0.5 - x) * 4;
          const offsetY = (0.5 - y) * 2;

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${50 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*2}% ${50 + offsetY*2}%`;
          div3.style.backgroundPosition = `${50 + offsetX}% ${50 + offsetY}%`;
        });

        const elo_changes = <?php echo json_encode($elo); ?>;

        console.log(elo_changes);
        elo_arr = [1000]
        for (let i = 0; i < elo_changes.length; i++) {
          elo_arr.push(elo_arr[elo_arr.length - 1] + elo_changes[i]);
        }
        console.log(elo_arr)
    </script>
</body>
</html>
