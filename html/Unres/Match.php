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
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $err = ($_POST['err']);
    if($err == "alnum"){
        $err_output = "Name must be alphanumeric";
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
          background-image: url("images/fs3.png");
        }

        .bg-img{
          background-image: url("images/fs2.png");
        }

        .bg-bg{
          background-image: url("images/fs1.png");
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

        .bg-img form {
          min-width:320px;
          width:20%;
          background-color:#1e2833;
          padding:40px;
          border-radius:4px;
          transform:translate(-50%, -50%);
          position:absolute;
          top:50%;
          left:50%;
          color:#fff;
          box-shadow:3px 3px 4px rgba(0,0,0,0.2);
        }

        .bg-img .illustration {
          text-align:center;
          padding:15px 0 20px;
          font-size:100px;
          color:#2980ef;
        }


        .bg-img form .form-control {
          background:none;
          border:none;
          border-bottom:1px solid #434a52;
          border-radius:0;
          box-shadow:none;
          outline:none;
          color:inherit;
        }

        .bg-img form .btn-primary {
          background:#99E6FC;
          color:#1e2833;
          border:none;
          border-radius:4px;
          padding:11px;
          box-shadow:none;
          margin-top:26px;
          text-shadow:none;
          outline:none;
        }

        .bg-img form .btn-primary:hover, .bg-img form .btn-primary:active {
          background:#bff;
          outline:none;
        }

        .bg-img form .forgot {
          display:block;
          text-align:center;
          font-size:12px;
          color:#6f7a85;
          opacity:0.9;
          text-decoration:none;
        }

        .bg-img form .forgot:hover, .bg-img form .forgot:active {
          opacity:1;
          text-decoration:none;
        }

        .bg-img form .btn-primary:active {
          transform:translateY(1px);
        }

        option{
            background-color: #1e2833 !important;
        }

        body{
            overflow-y:hidden;
        }

            #back{
            color:white;
            background-color:#1e2833;
            width:50px;
            height:50px;
            position:absolute;
            top:25px;
            right:50px;
            padding: 0px;
            border-radius:15px;
            font-weight:bolder;
            text-decoration:none;
        }

        #back img{
            margin:10px 8px 10px 12px;
            width:30px;
            height:auto;
            filter:  brightness(1.4) saturate(0.7) hue-rotate(-10deg);
            transform: scaleX(-1);
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
                <form action="submit_match.php" method="post">
                    <h2 class="sr-only">Login Form</h2>
                    <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/6967/6967688.png"/></div>
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

          div1.style.backgroundPosition = `${50 + offsetX*6}% ${50 + offsetY*6}%`;
          div2.style.backgroundPosition = `${50 + offsetX*3}% ${50 + offsetY*3}%`;
          div3.style.backgroundPosition = `${50 + offsetX}% ${50 + offsetY}%`;
        });
    </script>

</body>
</html>
