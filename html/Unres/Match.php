<?php
/*
 * Allows users to enter decks and adjust elo calculations
 *
 * Author: James Aris
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "/var/www/html/Unres/db.php";

// Fetch all decks
$stmt = $pdo->query("SELECT id, provided_archetype, decklist_url, custom_id, elo, name FROM decks ORDER BY custom_id");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);


$err_output = "";
if (isset($_GET['err']) && $_GET['err'] === "alnum") {
    $err_output = "Name must be alphanumeric";
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
          background-image: url("images/fs3.png");
        }

        .bg-img{
          background-image: url("images/fs2.png");
        }

        .bg-bg{
          background-image: url("images/fs1.png");
        }



        #p1{
            top:25px;
            background-color: #444
        }





    </style>
</head>
<body>

    <div class="bg-bg">
        <div class="bg-img">
            <div class="bg-fg">

                <!-- Page Buttons -->

                <a id="p1" class="pButton">
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

                <form action="submit_match.php" method="post">
                    <h2 class="sr-only">Login Form</h2>
                    <!--
                    <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/6967/6967688.png"/></div>
                    -->
                    <div class="illustration"><img src="images/pumpkin.png"/></div>
                    <strong style="color:#a00"><?= $err_output ?></strong>
                    <div class="form-group">
                        <label for="winner">Winner:</label>
                        <select class="form-control"  id="winner" name="winner">
                            <?php foreach ($decks as $deck): ?>
                                <option value="<?= htmlspecialchars($deck['id']) ?>">
                                    <?= htmlspecialchars($deck['custom_id']) ?> - <?= htmlspecialchars($deck['name']) ?> - <?= htmlspecialchars($deck['provided_archetype']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="loser">Loser:</label>
                        <select class="form-control"  id="loser" name="loser">
                            <?php foreach ($decks as $deck): ?>
                                <option value="<?= htmlspecialchars($deck['id']) ?>">
                                    <?= htmlspecialchars($deck['custom_id']) ?> - <?= htmlspecialchars($deck['name']) ?> - <?= htmlspecialchars($deck['provided_archetype']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="Name">Match Submitter:</label>
                        <input class="form-control" type="text" id="name" name="name">
                    </div>
                    <div class="form-group"><button class="btn btn-primary btn-block" type="submit">Submit</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>`
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

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${75 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*3}% ${75 + offsetY*3}%`;
          div3.style.backgroundPosition = `${50 + offsetX}% ${75 + offsetY}%`;
        });


        window.addEventListener("DOMContentLoaded", function() {
          var select = document.getElementById("winner");
          var options = select.options;
          var randomIndex = Math.floor(Math.random() * options.length);
          select.selectedIndex = randomIndex;

          select = document.getElementById("loser");
          options = select.options;
          randomIndex = Math.floor(Math.random() * options.length);
          select.selectedIndex = randomIndex;
        })
    </script>

</body>
</html>
