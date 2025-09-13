<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $winner_id = escapeshellarg($_POST['winner']);
    $loser_id = escapeshellarg($_POST['loser']);
    $name = ($_POST['name']);

    if (ctype_alnum($name)) {
        $name = escapeshellarg($name);
        $command = "cd /var/www/Unres-Meta/elo && /var/www/Unres-Meta/elo/venv/bin/python update_elo.py $winner_id $loser_id $name 2>&1";
        $output2 = shell_exec($command);

        header("Location: Leaderboard.php");
        exit();
    } else {
        header("Location: Match.php?err=anum");
        exit();
    }

}
?>