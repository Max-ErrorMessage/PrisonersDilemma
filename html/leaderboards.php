<?php
include '../db.php'; // database connection

// Start session and check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uname'])) {
    header("Location: /signin.php");
    exit();
}

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
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div id="NavBar">
        <img src="images/twokielogo.png" id="navbarLogo">
        <a href="index.php" class="nav-link">Home</a>
        <a href="#" class="nav-link">Leaderboards</a>
        <a href="wwmd.php" class="nav-link">WWMD?</a>
        <a href="profile.php" class="nav-link">My Profile</a>
        <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
    </div>

    <div id="Main">
        <br><br>
        <h1>Prisoner's Dilemma Leaderboard:</h1>
        <br>
        <?php
        // Fetch the top 10 users
        $sql = "SELECT a.Username, s.Points
                FROM Submission s
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE s.Game_ID = 1
                ORDER BY s.Points DESC
                LIMIT 10;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the current user's rank and score (if not in top 10)
        $sql = "SELECT Username, Points, `Rank` FROM (
                    SELECT a.Username, s.Points, 
                           RANK() OVER (ORDER BY s.Points DESC) AS `Rank`
                    FROM Submission s
                    INNER JOIN Accounts a ON s.User_ID = a.User_ID
                    WHERE s.Game_ID = 1
                ) ranked_users
                WHERE Username = ?
                LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uname]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($topUsers) {
            echo "<ol>";
            $i = 0;
            $userInTop10 = false;

            foreach ($topUsers as $row) {
                $i++;
		$htmlid = "";
		$htmlid = $row["Username"];
		if ($row["Username"] == $uname){
		    $htmlid = "self";
		}
		$highlightStyle = "background-image: linear-gradient(90deg, #225522, #003300);";
                $highlightStyle = ($row["Username"] == $uname) ? "background-image: linear-gradient(-90deg,#225522,#448844);" : $highlightStyle;
		$highlightStyle = ($row["Username"] == "MerlinBOT") ? "background-image: linear-gradient(90deg,rgb(77,92,186),rgb(12,22,80));"  : $highlightStyle;
                echo "<li id = " . $htmlid . " style='$highlightStyle'><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'>
                    <strong>#$i:</strong> " . htmlspecialchars($row['Username']) . "<br>
                    <strong>Average Points per round:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";

                if ($row["Username"] == $uname) {
                    $userInTop10 = true;
                }
            }

            if (!$userInTop10 && $userRow) {
                echo "<li style='background-image: linear-gradient(90deg,#225522,#448844);'>
                    <a href='profile.php'>
                    <strong>#" . htmlspecialchars($userRow['Rank']) . ":</strong> " . htmlspecialchars($userRow['Username']) . "<br>
                    <strong>Average Points per round:</strong> " . htmlspecialchars($userRow['Points']) . "</a></li>";
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
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE s.Game_ID = 2
                ORDER BY s.Points DESC
                LIMIT 10;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT Username, Points, `Rank` FROM (
                    SELECT a.Username, s.Points, 
                           RANK() OVER (ORDER BY s.Points DESC) AS `Rank`
                    FROM Submission s
                    INNER JOIN Accounts a ON s.User_ID = a.User_ID
                    WHERE s.Game_ID = 2
                ) ranked_users
                WHERE Username = ?
                LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uname]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($topUsers) {
            echo "<ol>";
            $i = 0;
            $userInTop10 = false;

            foreach ($topUsers as $row) {
                $i++;
		$htmlid = ($row["Username"] == $uname) ? "self" : $uname;
                $highlightStyle = "background-image: linear-gradient(90deg, #225522, #003300);";
                $highlightStyle = ($row["Username"] == $uname) ? "background-image: linear-gradient(-90deg,#225522,#448844);" : $highlightStyle;
                echo "<li id = " . $htmlid . " style='$highlightStyle'><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'>
                    <strong>#$i:</strong> " . htmlspecialchars($row['Username']) . "<br>
                    <strong>Points:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";

                if ($row["Username"] == $uname) {
                    $userInTop10 = true;
                }
            }

            if (!$userInTop10 && $userRow) {
                echo "<li style='background-image: linear-gradient(-90deg,#225522,#448844);'>
                    <a href='profile.php'>
                    <strong>#" . htmlspecialchars($userRow['Rank']) . ":</strong> " . htmlspecialchars($userRow['Username']) . "<br>
                    <strong>Points:</strong> " . htmlspecialchars($userRow['Points']) . "</a></li>";
            }

            echo "</ol>";
        } else {
            echo "<p>No user found.</p>";
        }
        ?>
    </div>
    <div id="Main3">
        <br><br>
        <h1>Rock Paper Scissors Leaderboard:</h1>
        <br>
        <?php
        // Fetch the top 10 users
        $sql = "SELECT a.Username, s.Points
                FROM Submission s
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE s.Game_ID = 3
                ORDER BY s.Points DESC
                LIMIT 10;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the current user's rank and score (if not in top 10)
        $sql = "SELECT Username, Points, `Rank` FROM (
                    SELECT a.Username, s.Points,
                           RANK() OVER (ORDER BY s.Points DESC) AS `Rank`
                    FROM Submission s
                    INNER JOIN Accounts a ON s.User_ID = a.User_ID
                    WHERE s.Game_ID = 3
                ) ranked_users
                WHERE Username = ?
                LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uname]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($topUsers) {
            echo "<ol>";
            $i = 0;
            $userInTop10 = false;

            foreach ($topUsers as $row) {
                $i++;
		$htmlid = "";
		$htmlid = $row["Username"];
		if ($row["Username"] == $uname){
		    $htmlid = "self";
		}
		$highlightStyle = "background-image: linear-gradient(90deg, #225522, #003300);";
        $highlightStyle = ($row["Username"] == $uname) ? "background-image: linear-gradient(-90deg,#225522,#448844);" : $highlightStyle;
		$highlightStyle = ($row["Username"] == "MerlinBOT") ? "background-image: linear-gradient(90deg,rgb(189,15,249),rgb(100,50,150));"  : $highlightStyle;
                echo "<li id = " . $htmlid . " style='$highlightStyle'><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'>
                    <strong>#$i:</strong> " . htmlspecialchars($row['Username']) . "<br>
                    <strong>Average Points per round:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";

                if ($row["Username"] == $uname) {
                    $userInTop10 = true;
                }
            }

            if (!$userInTop10 && $userRow) {
                echo "<li style='background-image: linear-gradient(90deg,#225522,#448844);'>
                    <a href='profile.php'>
                    <strong>#" . htmlspecialchars($userRow['Rank']) . ":</strong> " . htmlspecialchars($userRow['Username']) . "<br>
                    <strong>Average Points per round:</strong> " . htmlspecialchars($userRow['Points']) . "</a></li>";
            }

            echo "</ol>";
        } else {
            echo "<p>No user found.</p>";
        }
        ?>
    </div>
</body>
</html>
