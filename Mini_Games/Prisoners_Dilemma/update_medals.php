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
    foreach ($rows as $row){
        $i++; //sql statement to update medals
        $sql = "UPDATE Users
            SET :medal = :medal + 1
            WHERE Username = :username;";

        $stmt->bindParam(':username', $row['Username']);
        if ($i == 1){ //checks what type of medal it is
            $medal = "gold";
        }
        if ($i == 2){
            $medal = "silver";
        }
        if ($i == 3){
            $medal = "bronze";
        }
            $stmt->bindParam(':medal', $medal);
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
}

?>