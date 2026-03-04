<?php
header("Content-Type: application/xml; charset=utf-8");
include "/var/www/html/Unres/db.php";

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>
  <loc>https://www.twokie.com/Unres/index.php</loc>
  <lastmod><?= date("Y-m-d", filemtime("index.php")) ?></lastmod>
</url>
<url>
  <loc>https://www.twokie.com/Unres/Leaderboard.php</loc>
  <lastmod><?= date("Y-m-d", filemtime("Leaderboard.php")) ?></lastmod>
</url>
<url>
  <loc>https://www.twokie.com/Unres/cards.php</loc>
  <lastmod><?= date("Y-m-d", filemtime("cards.php")) ?></lastmod>
</url>
<url>
  <loc>https://www.twokie.com/Unres/PastTourneys.php</loc>
  <lastmod><?= date("Y-m-d", filemtime("PastTourneys.php")) ?></lastmod>
</url>

<?php

$stmt = $pdo->query("SELECT id FROM decks");
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($decks as $row){
    echo "<url>";
    echo "<loc>https://www.twokie.com/Unres/deck.php?id=" . $row['id'] . "</loc>";
    echo "<lastmod>" . date("Y-m-d", filemtime("deck.php")) . "</lastmod>";
    echo "</url>";
}

$stmt = $pdo->query("SELECT * FROM past_tournaments");
$trnys = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($trnys as $row){
    echo "<url>";
    echo "<loc>https://www.twokie.com/Unres/TourneyHistory.php?id=" . $row['id'] . "</loc>";
    echo "<lastmod>" . $row['tourney_date'] . "</lastmod>";
    echo "</url>";
}
?>

</urlset>