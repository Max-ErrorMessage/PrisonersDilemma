<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);
    $name = ($_POST['name']);

    if (ctype_alnum($name)) {
        $name = escapeshellarg($name);
        $command = "cd /var/www/Unres-Meta/elo && /var/www/Unres-Meta/venv/bin/python3 /var/www/Unres-Meta/elo/update_elo.py $winner_id $loser_id $name";
        echo $command;
        $output2 = shell_exec($command);

        header("Location: Leaderboard.php");
        exit();
    } else {
        header("Location: Match.php?err=alnum");
        exit();
    }

}
?>