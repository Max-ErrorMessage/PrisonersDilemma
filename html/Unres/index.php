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

        #discord-btn{
            background-color: #7289da;
            width: 200px;
            height: 30px;
            margin-left: calc(50% - 100px);
            border-radius: 5px;
            display: block;
            color: white;
            text-align: center;
        }

        #discord-btn:hover{
            background-color: #5b6eae;
        }

        #discord-btn svg{
            width: 20px;
            height: 20px;
            fill: white;
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
                <div id="lb">
                    <h2>Unrestricted Vintage Homepage</h2>   
                    <div class="illustration"><img src="https://cdn-icons-png.flaticon.com/128/6967/6967688.png"/></div>
                    <p style="white-space: pre-line;">Unrestricted Vintage is a custom Magic: The Gathering format where every card ever printed is legal with no bans and no restrictions.
                    Power Nine, fast mana, and game-ending combos aren't just allowed; they're expected.</p>
                    
                    <p style="white-space: pre-line;">Decks compete in tracked matches, Elo ratings rise and fall, and the metagame evolves in public.
                    Nothing is theoretical here. If it works, the data will show it.</p>
                   
                    <p style="white-space: pre-line;">The format is shaped by its players, and the conversation happens in the Discord.
                    If you love broken Magic, experimental formats, and watching history unfold in real time;
                    come help define what Unrestricted Vintage becomes.</p>   
                    <a
                      href="https://discord.gg/ndvgeRCpJW"
                      target="_blank"
                      rel="noopener"
                      id="discord-btn"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M492.5 69.8c-.2-.3-.4-.6-.8-.7-38.1-17.5-78.4-30-119.7-37.1-.4-.1-.8 0-1.1 .1s-.6 .4-.8 .8c-5.5 9.9-10.5 20.2-14.9 30.6-44.6-6.8-89.9-6.8-134.4 0-4.5-10.5-9.5-20.7-15.1-30.6-.2-.3-.5-.6-.8-.8s-.7-.2-1.1-.2c-41.3 7.1-81.6 19.6-119.7 37.1-.3 .1-.6 .4-.8 .7-76.2 113.8-97.1 224.9-86.9 334.5 0 .3 .1 .5 .2 .8s.3 .4 .5 .6c44.4 32.9 94 58 146.8 74.2 .4 .1 .8 .1 1.1 0s.7-.4 .9-.7c11.3-15.4 21.4-31.8 30-48.8 .1-.2 .2-.5 .2-.8s0-.5-.1-.8-.2-.5-.4-.6-.4-.3-.7-.4c-15.8-6.1-31.2-13.4-45.9-21.9-.3-.2-.5-.4-.7-.6s-.3-.6-.3-.9 0-.6 .2-.9 .3-.5 .6-.7c3.1-2.3 6.2-4.7 9.1-7.1 .3-.2 .6-.4 .9-.4s.7 0 1 .1c96.2 43.9 200.4 43.9 295.5 0 .3-.1 .7-.2 1-.2s.7 .2 .9 .4c2.9 2.4 6 4.9 9.1 7.2 .2 .2 .4 .4 .6 .7s.2 .6 .2 .9-.1 .6-.3 .9-.4 .5-.6 .6c-14.7 8.6-30 15.9-45.9 21.8-.2 .1-.5 .2-.7 .4s-.3 .4-.4 .7-.1 .5-.1 .8 .1 .5 .2 .8c8.8 17 18.8 33.3 30 48.8 .2 .3 .6 .6 .9 .7s.8 .1 1.1 0c52.9-16.2 102.6-41.3 147.1-74.2 .2-.2 .4-.4 .5-.6s.2-.5 .2-.8c12.3-126.8-20.5-236.9-86.9-334.5zm-302 267.7c-29 0-52.8-26.6-52.8-59.2s23.4-59.2 52.8-59.2c29.7 0 53.3 26.8 52.8 59.2 0 32.7-23.4 59.2-52.8 59.2zm195.4 0c-29 0-52.8-26.6-52.8-59.2s23.4-59.2 52.8-59.2c29.7 0 53.3 26.8 52.8 59.2 0 32.7-23.2 59.2-52.8 59.2z"/></svg>
                      Join the Discord
                    </a>
                </div>
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
