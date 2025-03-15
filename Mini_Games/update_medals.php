<?php
include '/var/www/db.php';

$sql = "SELECT a.User_ID, s.Points
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
            $medal = "Gold";
        } elseif ($i == 2) {
            $medal = "Silver";
        } elseif ($i == 3) {
            $medal = "Bronze";
        } else {
            break; // Only process top 3
        }

        // Prepare the SQL statement
        $sql = "UPDATE Medals
                SET $medal = $medal + 1
                WHERE User_ID = :id";

        $stmt = $pdo->prepare($sql); // Prepare first
        $stmt->bindParam(':id', $row['User_ID']); // Then bind parameters
        $stmt->execute(); // Execute the query
    }
}



$sql = "SELECT a.User_ID, s.Points
        FROM Submission s
        INNER JOIN Accounts a ON s.User_ID = a.User_ID
        WHERE s.Submission_ID = (
            SELECT MAX(Submission_ID)
            FROM Submission
            WHERE User_ID = s.User_ID
            AND Game_ID = 2
        )
        AND s.Game_ID = 2
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
            $medal = "YaGo";
        } elseif ($i == 2) {
            $medal = "YaSi";
        } elseif ($i == 3) {
            $medal = "YaBr";
        } else {
            break; // Only process top 3
        }

        // Prepare the SQL statement
        $sql = "UPDATE Medals
                SET $medal = $medal + 1
                WHERE User_ID = :id";

        $stmt = $pdo->prepare($sql); // Prepare first
        $stmt->bindParam(':id', $row['User_ID']); // Then bind parameters
        $stmt->execute(); // Execute the query
    }
}


$sql = "SELECT a.User_ID, s.Points
        FROM Submission s
        INNER JOIN Accounts a ON s.User_ID = a.User_ID
        WHERE s.Submission_ID = (
            SELECT MAX(Submission_ID)
            FROM Submission
            WHERE User_ID = s.User_ID
            AND Game_ID = 3
        )
        AND s.Game_ID = 3
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
            $medal = "RPSGo";
        } elseif ($i == 2) {
            $medal = "RPSSi";
        } elseif ($i == 3) {
            $medal = "RPSBr";
        } else {
            break; // Only process top 3
        }

        // Prepare the SQL statement
        $sql = "UPDATE Medals
                SET $medal = $medal + 1
                WHERE User_ID = :id";

        $stmt = $pdo->prepare($sql); // Prepare first
        $stmt->bindParam(':id', $row['User_ID']); // Then bind parameters
        $stmt->execute(); // Execute the query
    }
}




?>
