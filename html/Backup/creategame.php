<?php
$servername = "127.0.0.1:3306";
$username = "u753770036_DougSantry";
$password = "demorgansL4W?";
$dbname = "u753770036_Chess";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $dbname);
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "creating";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST["uname"];

    $statement = $conn->prepare("INSERT INTO Games (Player1) VALUES (?)");
    $statement->bind_param("s", $_SESSION['PlayerID']);
    echo "1";
    if ($statement->execute()) {
        $statement = $conn->prepare("SELECT * FROM Games Where Player1 = ?");
        $statement->bind_param("s", $_SESSION['PlayerID']);
        if ($statement->execute()) {
            $result = $statement->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "we're in boys";
                $_SESSION['GameID'] = $row["GameID"];
                header("Location: game.php");
                exit;
            }
        }
    } else {
        echo "Error: " . $statement->error . "! Dont do that :)";
        header("Location: main.php");
        exit;
    }
} else {
    $statement = $conn->prepare("INSERT INTO Games (Player1, Player2) VALUES (?,0)");
    $statement->bind_param("s", $_SESSION['PlayerID']);
    echo $_SESSION['PlayerID'];
    if ($statement->execute()) {
        echo "3";
        $statement = $conn->prepare("SELECT * FROM Games Where Player1 = ?");
        $statement->bind_param("s", $_SESSION['PlayerID']);
        if ($statement->execute()) {
            echo "4"; 
            $result = $statement->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "we're in boys";
                $_SESSION['GameID'] = $row["GameID"];
                header("Location: game.php");
                exit;
            }
        }
    } else {
        echo "Error: " . $statement->error . "! Dont do that :)";
        header("Location: main.php");
        exit;
    }
}
?>

