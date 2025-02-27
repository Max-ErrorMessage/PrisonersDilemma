<!DOCTYPE HTML>

<link rel="icon" type="image/x-icon" href="../images/blueRook.png">
<head>
    <title>get legal moves testing</title>
</head>

<?php
$servername = "127.0.0.1:3306";
$username = "u753770036_DougSantry";
$password = "demorgansL4W?";
$dbname = "u753770036_Chess";

$gameID =  "1"; //$_POST["gameID"];
$move = "1111";  //$_POST["move"];

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table_query = "SELECT * FROM Games WHERE GameID = " . $gameID;
$table_result = $conn->query($table_query);

if ($table_result === false) {
    die("Query failed: " . $conn->error);
}


if ($table_result) {
    if ($table_result->num_rows === 1) {
        $row = $table_result->fetch_assoc();
        $board_state = $row["Boardstate"];
        $player1_turn = $row["Player1Turn"];
        $invulnerable = $row["Invulnerable"];
        $forced = $row["ForcedMove"];
    } else {
        // Handle the case where there are more than one result
        echo "Error: More than one result found.";
    }
    echo "</table>";
} else {
    // Handle the case where the query failed
    echo "Error: " . $conn->error;
}

$conn->close();

exec("python3 find_legal_moves.py $board_state $player1_turn $invulnerable $forced $move", $output);

echo json_encode($output);

?>