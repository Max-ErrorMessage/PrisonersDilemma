<?php
session_start();

if (!isset($_SESSION['uname'])) {
    // Redirect to login page if 'uname' is not set
    header("Location: /signin.php");
    exit();
}

$otherUname = $_GET["user"];
$uname = htmlspecialchars($_SESSION['uname']);
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css">
        <link rel="icon" href="/t.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    </head>
    <body>
        <div id="NavBar">
            <img src="images/twokielogo.png" id="navbarLogo">
            <a href="index.php" class="nav-link">Home</a>
            <a href="leaderboards.php" class="nav-link">Leaderboards</a>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="signin.php" id="signinbutton" class="nav-link"><?php echo $uname; ?></a>
        </div>
        <div id="Main">
            <br><br>
            <h1>My Profile:</h1>
            <br>
            <p>Username: <?php echo $otherUname; ?></p>
            <p>Submissions: <?php
                                include '../db.php';

                                $sql = "
                                    SELECT Submission.Code
                                    FROM Submission
                                    INNER JOIN Accounts ON Submission.User_ID = Accounts.User_ID
                                    WHERE Accounts.Username = :username;
                                ";

                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(":username", $otherUname);
                                $stmt->execute();
                                $submissions = $stmt->fetchAll(PDO::FETCH_COLUMN);


                                $pdo = null;

                                foreach ($submissions as $code){
                                    echo "<pre>" . htmlspecialchars($code) . "<pre>";
                                }
                                ?></p>
        </div>

    </body>
</html>
