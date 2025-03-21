<?php

/**
 * Fetches user codes from the database.
 *
 * Based on the get_user_code file in the Prisoner's Dilemma directory.
 *
 * Author: James Aris
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'var/www/db.php';


class TableRows extends RecursiveIteratorIterator {
  function __construct($it) {
    parent::__construct($it, self::LEAVES_ONLY);
  }

  function current() {
    return "<td style='width:150px;border:1px solid black;'>" . parent::current(). "</td>";
  }

  function beginChildren() {
    echo "<tr>";
  }

  function endChildren() {
    echo "</tr>" . "\n";
  }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
    header("Location: /newSubmission.php");
    return("Error - database connection failed");
    exit();
}

if (isset($_POST['game'])) { 
    
    $array = [];
    $game = $_POST['game'];
    
    $stmt = $pdo->prepare("SELECT User_ID, Code FROM Submission WHERE GameID = :game");
    $stmt->bindParam(':game', $game, PDO::PARAM_INT);
    $stmt->execute();
    
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    
    if (empty($array)) {
        $_SESSION["Error3"] = "An error was found - the array is empty - please see fetch_code.php";
        return "Error - Username was not found";
    }
    
    try {
        $_SESSION["user_code"] = $array;
        return $array;
    } catch (Exception $e) {
        $_SESSION['Error3'] = "Error during running: " . $e->getMessage();
        return "Secret third error";
    }

}
?>