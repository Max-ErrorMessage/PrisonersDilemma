<?php
/*
 * Allows users to either sign up and create a new account or log in to an existing account
 *
 * Author: James Aris
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "/var/www/unresdb.php";

// Fetch all decks
$stmt = $pdo->query("SELECT id, elo FROM decks");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.75">
<title>Unrestricted Vintage Matchups</title>
<link rel="icon" href="/t.ico" type="image/x-icon">
<meta name="description" content="Create bots to compete in fun minigames! :)">
</head>
<body>
<canvas id="particleCanvas"></canvas>

<div id="form-cont">
    <form action="submit_match.php" method="post">
        <label for="playerA">Player A:</label>
        <select id="playerA" name="playerA">
            <?php foreach ($decks as $deck): ?>
                <option value="<?= htmlspecialchars($deck['id']) ?>">
                    <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="playerB">Player B:</label>
        <select id="playerB" name="playerB">
            <?php foreach ($decks as $deck): ?>
                <option value="<?= htmlspecialchars($deck['id']) ?>">
                    <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Submit Match">
    </form>
</div>

</body>
</html>