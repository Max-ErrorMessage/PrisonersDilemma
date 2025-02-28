<?php

include 'db.php'
session_start();
$filePath = '/var/www/Mini_Games/Prisoners_Dilemma/fetch_code.php';

$_POST['game'] = 1;
$user_codes = [];

if (file_exists($filePath)) {
    $array = include($filePath);
    if (isset($_SESSION["user_code"])) {
        $user_codes = $_SESSION["user_code"];
    } else if (isset($_SESSION["Error3"])) {
        var_dump($_SESSION["Error3"]);
        exit();
    } else {
        exit();
    }
} else {
    echo 'Error: fetch_code.php file does not exist at the specified path.';
}

$json_codes = json_encode($user_codes, JSON_PRETTY_PRINT);

file_put_contents("/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.json", $json_codes);

$output = exec("python3 /var/www/Mini_Games/Prisoners_Dilemma/define_user_codes.py");

$empty_scores = [];

foreach ($user_codes as $item) {
    $user_id = $item['UserID'];
    $empty_scores[$user_id] = 0;
}

$json_empty_scores = json_encode($empty_scores, JSON_PRETTY_PRINT);

file_put_contents('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/scores.json', $json_empty_scores);

foreach ($user_codes as $user_code_1) {
    $user_1 = $user_code_1["UserID"];
    foreach ($user_codes as $user_code_2) {
        $user_2 = $user_code_2["UserID"];
        if ($user_2 <= $user_1) {
            continue;
        }
        $arg1 = escapeshellarg($user_1);
        $arg2 = escapeshellarg($user_2);
        $arg3 = escapeshellarg(200);

        $command = "timeout 1 python3 /var/www/Mini_Games/Prisoners_Dilemma/simulate_2_players.py $arg1 $arg2 $arg3";

        $output = shell_exec($command);
        echo $output . "</p>";

    }
}


$json_file = '/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/scores.json';
if (!file_exists($json_file)) {
    die("Error: scores.json file not found.");
}

$json_data = file_get_contents($json_file);
$scores = json_decode($json_data, true);

if (!is_array($scores)) {
    die("Error: Invalid JSON format.");
}

$updated = 0;

foreach ($scores as $user_id => $score) {
    if (!is_numeric($user_id) || !is_numeric($score)) {
        continue;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Leaderboard WHERE UserID = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE Leaderboard SET Points = :score, GameID = 1 WHERE UserID = :user_id");
    } else {
        $stmt = $pdo->prepare("INSERT INTO Leaderboard (UserID, Points, GameID) VALUES (:user_id, :score, 1)");
    }

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':score', $score, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $updated++;
    }
}

?>