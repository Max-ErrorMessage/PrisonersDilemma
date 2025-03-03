<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$game_length = rand(200, 400);

$fetch_code_path = '/var/www/Mini_Games/Prisoners_Dilemma/fetch_code.php';

$_POST['game'] = 1;
$user_codes = [];

if (file_exists($fetch_code_path)) {
    $array = include($fetch_code_path);
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
    $user_id = $item['User_ID'];
    $empty_scores[$user_id] = 0;
}

$json_empty_scores = json_encode($empty_scores, JSON_PRETTY_PRINT);

file_put_contents('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/scores.json', $json_empty_scores);

foreach ($user_codes as $user_code_1) {
    $user_1 = $user_code_1["User_ID"];
    foreach ($user_codes as $user_code_2) {
        $user_2 = $user_code_2["User_ID"];
        if ($user_2 <= $user_1) {
            continue;
        }
        $arg1 = escapeshellarg($user_1);
        $arg2 = escapeshellarg($user_2);
        $arg3 = escapeshellarg(game_length);
        
        $command = "timeout 1 python3 /var/www/Mini_Games/Prisoners_Dilemma/simulate_2_players.py $arg1 $arg2 $arg3";        
    
        exec($command);
    }
}

include '/var/www/html/db.php';

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

$query = $pdo->query("SELECT COUNT(*) AS total_records FROM Submission WHERE Game_ID = 1");
$totalRecords = $query->fetch(PDO::FETCH_ASSOC)['total_records'];

foreach ($scores as $user_id => $score) {
    if (!is_numeric($user_id) || !is_numeric($score)) {
        continue;
    }

    $adjusted_points = $score / (($totalRecords - 1) * game_length);

    $stmt = $pdo->prepare("UPDATE Submission SET Points = :adjusted_points WHERE User_ID = :user_id AND Game_ID = 1");

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':adjusted_points', $adjusted_points, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $updated++;
    }
}

unlink('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.json');
unlink('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py');
// unlink($json_file);

echo "Scores successfully updated.\n";

?>
