<?php

session_start();
$filePath = '/var/www/Mini_Games/Yahtzee/fetch_code.php';

$_POST['game'] = 2;
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

file_put_contents("/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/user_codes.json", $json_codes);

$output = exec("python3 /var/www/Mini_Games/Yahtzee/define_user_codes.py");

$empty_scores = [];

foreach ($user_codes as $item) {
    $user_id = $item['UserID'];
    $empty_scores[$user_id] = 0;
}

$json_empty_scores = json_encode($empty_scores, JSON_PRETTY_PRINT);

file_put_contents('/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json', $json_empty_scores);

foreach ($user_codes as $user_code_1) {
    $user = $user_code_1["UserID"];
    

    $arg1 = escapeshellarg($user_1);
    $arg2 = escapeshellarg(200);
    
    $command = "timeout 1 python3 /var/www/Mini_Games/Yahtzee/simulate_2_players.py $arg1 $arg2";
    
    $output = shell_exec($command);
    echo $output . "</p>";
        
    }
}

include '/var/www/html/db.php';

$json_file = '/home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json';
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
        continue; // Skip invalid data
    }
    $stmt = $pdo->prepare("UPDATE Submissions SET Points = :score WHERE User_ID = :user_id and Game_ID = 2");

    // Bind parameters and execute update
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':score', $score, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $updated++;
    }
}

?>