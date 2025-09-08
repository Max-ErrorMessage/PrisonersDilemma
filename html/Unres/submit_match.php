<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);



    $command = "cd /var/www/Unres-Meta/elo && /var/www/Unres-Meta/elo/venv/bin/python update_elo.py $winner_id $loser_id 2>&1";

    $output2 = shell_exec($command);
    //file_put_contents("/var/www/html/Unres/php_python_debug.log", date('c') . " - CMD: $command\nOUTPUT:\n" . $output . "\n", FILE_APPEND);

    header("Location: Leaderboard.php");
    exit();
}
?>