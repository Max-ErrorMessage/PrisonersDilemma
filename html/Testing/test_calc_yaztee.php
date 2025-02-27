<?php

session_start();
$filePath = '/home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/fetch_code.php';

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

file_put_contents("/home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/Computer_Generated_Files/user_codes.json", $json_codes);

$output = exec("python3 /home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/define_user_codes.py");

$empty_scores = [];

foreach ($user_codes as $item) {
    $user_id = $item['UserID'];
    $empty_scores[$user_id] = 0;
}

$json_empty_scores = json_encode($empty_scores, JSON_PRETTY_PRINT);

file_put_contents('/home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json', $json_empty_scores);

var_dump($user_codes);

foreach ($user_codes as $user_code) {
    
    $user = $user_code["UserID"];
    

    $arg1 = escapeshellarg($user);
    $arg2 = escapeshellarg(200);
    
    $command = "timeout 1 python3 /home/u753770036/domains/twokie.com/Mini_Games/Yahtzee/simulate_2_players.py $arg1 $arg2";
    
    $output = shell_exec($command);
    echo $output . "</p>";
        
}

echo "<p>ahhhhh</p>"
?>


