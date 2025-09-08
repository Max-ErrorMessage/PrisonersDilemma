<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);

    $command = "cd /var/www/Unres-Meta/elo && python3 update_elo.py $winner_id $loser_id";
    $output = shell_exec($command);
    file_put_contents("/var/www/html/Unres/php_python_debug.log", $output . PHP_EOL, FILE_APPEND);
    header("Location: Leaderboard.php");
    exit();
}
?>