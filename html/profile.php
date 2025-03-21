<?php

/**
 * Allows users to view their own profile and all code that they have submitted.
 *
 * Author: James Aris
 */

session_start();

// Check if the session variable 'uname' is set
if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
    header("Location: /signin.php");
    exit();
}

// If 'uname' is set, display the welcome message
$uname = htmlspecialchars($_SESSION['uname']); 
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css">
        <link rel="stylesheet" href="profile.css">
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>

    	<title>Twokie - My profile</title>

    </head>
    <body>
        <div id="NavBar">
            <img src="images/twokielogo.png" id="navbarLogo">
            <a href="index.php" class="nav-link">Home</a>
            <a href="leaderboards.php" class="nav-link">Leaderboards</a>
            <a href="wwmd.php" class="nav-link">WWMD?</a>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
        </div>
        <?php
            include '../db.php';

            $sql = "
                SELECT m.Gold, m.Silver, m.Bronze, m.YaGo, m.YaSi, m.YaBr, m.RPSGo, m.RPSSi, m.RPSBr
                FROM Accounts a
                JOIN Medals m on a.User_ID = m.User_ID
                WHERE a.Username = :username;
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $uname);
            $stmt->execute();
            $medals = $stmt->fetch(PDO::FETCH_ASSOC);

            $gold = $medals["Gold"];
            $silver = $medals["Silver"];
            $bronze = $medals["Bronze"];
            $yg = $medals["YaGo"];
            $ys = $medals["YaSi"];
            $yb = $medals["YaBr"];
            $rg = $medals["RPSGo"];
            $rs = $medals["RPSSi"];
            $rb = $medals["RPSBr"];
        ?></p>
        <div id="medals">
            <div id="medals1">
                <div id="gold" class="medal">
                    <img src="images/gold.png"/>
                    <p><?php echo $gold;?></p>
                </div>
                <div id="silver" class="medal">
                    <img src="images/silver.png"/>
                    <p><?php echo $silver;?></p>
                </div>
                <div id="bronze" class="medal">
                    <img src="images/bronze.png"/>
                    <p><?php echo $bronze;?></p>
                </div>
            </div>
            <div id="medals2">
                <div id="gold2" class="medal">
                    <img src="images/yahtzee_gold.png"/>
                    <p><?php echo $yg;?></p>
                </div>
                <div id="silver2" class="medal">
                    <img src="images/yahtzee_silver.png"/>
                    <p><?php echo $ys;?></p>
                </div>
                <div id="bronze2" class="medal">
                    <img src="images/yahtzee_bronze.png"/>
                    <p><?php echo $yb;?></p>
                </div>
            </div>
	    <div id="medals3">
		<div id="gold3" class="medal">
		    <img src="images/rps_gold.png"/>
		    <p><?php echo $rg;?></p>
		</div>
		<div id="silver3" class="medal">
		    <img src="images/rps_silver.png"/>
		    <p><?php echo $rs;?></p>
		</div>
		<div id="bronze3" class="medal">
		    <img src="images/rps_bronze.png"/>
		    <p><?php echo $rb;?></p>
		</div>
	    </div>
        </div>
        <div id="Main">
            <br><br>
            <h1>My Profile:</h1>
            <br>
            <p>Username: <?php echo $uname; ?></p>
            <p>Submissions: <?php

                                $sql = "
                                    SELECT Submission.Code, Submission.Game_ID
                                    FROM Submission
                                    INNER JOIN Accounts ON Submission.User_ID = Accounts.User_ID
                                    WHERE Accounts.Username = :username
				    ORDER BY Submission.Game_ID;
                                ";

                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(":username", $uname);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $code = $row['Code'];
                                    $gameid = $row['Game_ID'];


                                    if ($gameid == 1){
                                        echo "<h3>Prisoner's Dilemma:</h3>";
                                    }

                                    if ($gameid == 2){
                                        echo "<h3>Yahtzee:</h3>";
                                        $code = str_replace("$","\n\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n\n", $code);
                                    }

                                    if ($gameid ==3){
                                        echo "<h3> Rock Paper Scissors</h3>";
                                    }
                                    echo "<pre id='example'><code class='language-python'>" . htmlspecialchars($code) . "</code></pre>";
                                }

                                $pdo = null;

                                ?></p>
        </div>

    </body>
</html>
