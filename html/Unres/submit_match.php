<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);



    $command = "cd /var/www/Unres-Meta/elo && python3 update_elo.py $winner_id $loser_id";
    $output = shell_exec($command);
    file_put_contents("/var/www/html/Unres/php_python_debug.log", $output . PHP_EOL, FILE_APPEND);

    $command = "cd /var/www/Unres-Meta/elo && /usr/bin/python3 update-elo.py $winner_id $loser_id 2>&1";
    $output = shell_exec($command);
    file_put_contents("/tmp/php_python_debug.log", date('c') . " - CMD: $command\nOUTPUT:\n" . $output . "\n", FILE_APPEND);

    header("Location: Leaderboard.php");
    exit();
}
?>