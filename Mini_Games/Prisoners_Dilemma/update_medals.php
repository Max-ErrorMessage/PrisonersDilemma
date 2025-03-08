<?php
include '/var/www/db.php';

$sql = "SELECT a.Username, s.Points
        FROM Submission s
        INNER JOIN Accounts a ON s.User_ID = a.User_ID
        WHERE s.Submission_ID = (
            SELECT MAX(Submission_ID)
            FROM Submission
            WHERE User_ID = s.User_ID
            AND Game_ID = 1
        )
        AND s.Game_ID = 1
        ORDER BY s.Points DESC;
        LIMIT 3;"; //sql statement to find the top three players
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    $i = 0;
    foreach ($rows as $row) {
        $i++; // Determine medal type

        if ($i == 1) {
            $medal = "gold";
        } elseif ($i == 2) {
            $medal = "silver";
        } elseif ($i == 3) {
            $medal = "bronze";
        } else {
            break; // Only process top 3
        }

        // Prepare the SQL statement
        $sql = "UPDATE Users
                SET $medal = $medal + 1
                WHERE Username = :username";

        $stmt = $pdo->prepare($sql); // Prepare first
        $stmt->bindParam(':username', $row['Username']); // Then bind parameters
        $stmt->execute(); // Execute the query
    }
}
?>