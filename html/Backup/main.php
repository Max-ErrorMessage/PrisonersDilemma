<?php
session_start();
if(isset($_SESSION['PlayerID'])){
    $id = $_SESSION['PlayerID'];
    $uname = $_SESSION['Username'];
} else {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twokie</title>
    <link rel="stylesheet" href="mainstyle.css">
    <script>
        const middlegroundSpeed = 0.1;
        const backgroundSpeed = 0.2;
        const middleground2Speed = 2;

        window.addEventListener("scroll", function() {
            let scrollY = window.scrollY;

            document.getElementById("middleground").style.transform = `translateY(${scrollY * middlegroundSpeed}px)`;
            document.getElementById("background").style.transform = `translateY(${-scrollY * backgroundSpeed}px)`;
            document.getElementById("middleground2").style.transform = `translateX(-${scrollY * middleground2Speed}px)`;
        });
    </script>
</head>
<body>
    <div id = "main">
        <img id = "background"></img>
        <img id = "middleground" src="blueKing.png"></img>
        <img id = "middleground2" src = "redRook.png"></img>
        <div id = "foreground">
            <div id = "fg1">
                <header>
                    <h1>Welcome to Twokie <?php echo $uname; ?>!<br>Your move.</h1>
                </header>
            </div>
            <a id = "cg", href = "creategame.php">Create game</a>
            <a id = "jg">Join game</a>
            <div id = "fg2">
                <section>
                    <h2>About Us</h2>
                    <p>kinda gay ngl</p>
                </section>    
                <footer>
                    <p>&copy; <?php echo date("Y"); ?> Twokie</p>
                </footer> 
            </div>
            <div id = "fg3">
                <h2>Current Games:</h2>
                <div id = "gamesbox">
                    <table>
                        <tr>
                            <td>GameID</td>
                            <td>Player1</td>
                            <td>Player2</td>
                        </tr>
                        <?php
                            $servername = "127.0.0.1:3306";
                            $username = "u753770036_DougSantry";
                            $password = "demorgansL4W?";
                            $dbname = "u753770036_Chess";
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            $statement = $conn->prepare("SELECT Games.GameID, P1.Username AS Player1, P2.Username AS Player2 FROM Games 
                                INNER JOIN Players P1 ON Games.Player1 = P1.PlayerID
                                INNER JOIN Players P2 ON Games.Player2 = P2.PlayerID");

                            if ($statement->execute()) {
                                $result = $statement->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["GameID"] . "</td>";
                                        echo "<td>" . $row["Player1"] . "</td>";
                                        echo "<td>" . $row["Player2"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<p> Error getting Data from table</p>";
                                }
                            } 
                            $conn->close();
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id = "nav">
        <ul>
            <li><a class = "active">Home</a></li>
            <li><a class = "butts">Play</a></li>
            <li><a class = "butts">Social</a></li>
            <li><a class = "butts">Puzzles</a></li>
            <li><a class = "butts">Learn</a></li>
            <li id = "prof"><a class = "butts">Profile</a></li>
        </ul>
    </div>
</body>
</html>