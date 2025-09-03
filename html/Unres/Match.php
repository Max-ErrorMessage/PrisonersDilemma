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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-dark">
        <form action="submit_match.php" method="post">
            <div class="form-group">
                <label for="playerA">Player A:</label>
                <select class="form-control"  id="playerA" name="playerA">
                    <?php foreach ($decks as $deck): ?>
                        <option value="<?= htmlspecialchars($deck['id']) ?>">
                            <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="playerB">Player B:</label>
                <select class="form-control"  id="playerB" name="playerB">
                    <?php foreach ($decks as $deck): ?>
                        <option value="<?= htmlspecialchars($deck['id']) ?>">
                            <?= htmlspecialchars($deck['id']) ?> - <?= htmlspecialchars($deck['elo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><button class="btn btn-primary btn-block" type="submit">Submit</button></div>
        </form>
    </div>

</body>
</html>