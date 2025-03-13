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
                WHERE s.Submission_ID = (
                    SELECT MAX(Submission_ID)
                    FROM Submission
                    WHERE User_ID = s.User_ID
                    AND Game_ID = 1
                    AND Points <> 0
                )
                AND s.Game_ID = 1
                AND Points <> 0
                ORDER BY s.Points DESC
                LIMIT 10;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the current user's rank and score (if not in top 10)
        $sql = "SELECT a.Username, s.Points, 
                       (SELECT COUNT(*) + 1 
                        FROM Submission s2 
                        INNER JOIN Accounts a2 ON s2.User_ID = a2.User_ID
                        WHERE s2.Game_ID = 1
                        AND s2.Points > s.Points) AS Rank
                FROM Submission s
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE a.Username = ?
                AND s.Game_ID = 1
                ORDER BY s.Points DESC
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
                $highlightStyle = ($row["Username"] == $uname) ? "background-image: linear-gradient(90deg,#225522,#448844);" : "";
                echo "<li style='$highlightStyle'><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'>
                    <strong>#$i:</strong> " . htmlspecialchars($row['Username']) . "<br>
                    <strong>Average Points per round:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";

                if ($row["Username"] == $uname) {
                    $userInTop10 = true;
                }
            }

            // If the user is NOT in the top 10, show their personal rank
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
        // Fetch the top 10 users for Yahtzee
        $sql = "SELECT a.Username, s.Points
                FROM Submission s
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE s.Game_ID = 2
                ORDER BY s.Points DESC
                LIMIT 10;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the current user's rank and score (if not in top 10)
        $sql = "SELECT a.Username, s.Points, 
                       (SELECT COUNT(*) + 1 
                        FROM Submission s2 
                        INNER JOIN Accounts a2 ON s2.User_ID = a2.User_ID
                        WHERE s2.Game_ID = 2
                        AND s2.Points > s.Points) AS Rank
                FROM Submission s
                INNER JOIN Accounts a ON s.User_ID = a.User_ID
                WHERE a.Username = ?
                AND s.Game_ID = 2
                ORDER BY s.Points DESC
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
                $highlightStyle = ($row["Username"] == $uname) ? "background-image: linear-gradient(90deg,#225522,#448844);" : "";
                echo "<li style='$highlightStyle'><a href='otherProfile.php?user=" . htmlspecialchars($row['Username']) . "'>
                    <strong>#$i:</strong> " . htmlspecialchars($row['Username']) . "<br>
                    <strong>Points:</strong> " . htmlspecialchars($row['Points']) . "</a></li>";

                if ($row["Username"] == $uname) {
                    $userInTop10 = true;
                }
            }

            // If the user is NOT in the top 10, show their personal rank
            if (!$userInTop10 && $userRow) {
                echo "<li style='background-image: linear-gradient(90deg,#225522,#448844);'>
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
</body>
</html>
