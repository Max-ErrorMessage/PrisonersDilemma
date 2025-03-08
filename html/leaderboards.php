<?php
include '../db.php'; // database connection is in a separate file for security reasons (TODO: db.php file should be moved out of public html)

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
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
	<title>Twokie - Leaderboard</title>    
	<style>
	    a {
		text-decoration:none;
		color:white;
	    }
	</style>
    </head>
    <body>
        <div id="NavBar">
            <img src="images/twokielogo.png" id="navbarLogo">
            <a href="index.php" class="nav-link">Home</a>
            <a href="#" class="nav-link">Leaderboards</a>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
        </div>
        <div id="Main">
            <br><br>
            <h1>Prisoner's Dilemma Leaderboard:</h1>
            <br>
            <?php
            $sql = "SELECT a.Username, s.Points
                    FROM Submission s
                    INNER JOIN Accounts a ON s.User_ID = a.User_ID
                    WHERE s.Submission_ID = (
                        SELECT MAX(Submission_ID)
                        FROM Submission
                        WHERE User_ID = s.User_ID
                        AND Game_ID = 1
                    )
                    AND s.Game_ID = 1
                    ORDER BY s.Points DESC;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($rows) {
		$i = 0;
                echo "<ol>";
                foreach ($rows as $row){
		    $i++;
                    echo "<li><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'><strong>#" . $i . ":</strong> " . htmlspecialchars($row['Username']) . "<br><strong>Average Points per round:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";
                }
                echo "</ol>";
            } else {
                echo "<p>No user found.</p>";
            }
            
            ?>


        </div>
        <div id="Main2">
            <br><br>
            <h1>Yahtzee Leaderboard:</h1>
            <br>
            <?php
            $sql = "SELECT a.Username, s.Points
                FROM Submission s
                INNER JOIN Accounts a ON s.User_Id = a.User_ID
                WHERE s.Game_ID = 2
                ORDER BY s.Points DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows) {
		$i = 0;
                echo "<ol>";
                foreach ($rows as $row){
		    $i++;
                    echo "<li><strong>#" . $i . ":</strong> " . htmlspecialchars($row['Username']) . "<br><strong>Points:</strong> " . htmlspecialchars($row['Points']) . "</li>";
                }
                echo "</ol>";
            } else {
                echo "<p>No user found.</p>";
            }

            ?>


        </div>

    </body>
</html>
