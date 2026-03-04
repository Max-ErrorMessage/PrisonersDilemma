<?php
header("Content-Type: application/xml; charset=utf-8");
include "/var/www/html/Unres/db.php";

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>
  <loc>https://www.twokie.com/Unres/index.php</loc>
</url>
<url>
  <loc>https://www.twokie.com/Unres/Leaderboard.php</loc>
</url>
<url>
  <loc>https://www.twokie.com/Unres/cards.php</loc>
</url>
<url>
  <loc>https://www.twokie.com/Unres/PastTourneys.php</loc>
</url>

<?php
$result = mysqli_query($conn, "SELECT id FROM decks");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<url>";
    echo "<loc>https://www.twokie.com/Unres/deck.php?id=" . $row['id'] . "</loc>";
    echo "</url>";
}
?>

<?php
$result = mysqli_query($conn, "SELECT id FROM past_tournaments");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<url>";
    echo "<loc>https://www.twokie.com/Unres/TourneyHistory.php?id=" . $row['id'] . "</loc>";
    echo "</url>";
}
?>

</urlset>