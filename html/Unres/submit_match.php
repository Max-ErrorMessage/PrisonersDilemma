<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);

    $command = "python3 /var/www/Unres-Meta/elo/update_elo.py $winner_id $loser_id";
    $output = shell_exec($command);

    header("Location: Leaderboard.php");
    exit();
}
?>