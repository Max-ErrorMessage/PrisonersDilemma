<?php 
/*
 * clears the tournament table and then repopulates it with the top 128 decks by ELO
 *
 * Author: James Aris
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



include "/var/www/html/Unres/db.php";

// Fetch all decks
$stmt = $pdo->query('SELECT
        d.id as id,
        SUM(
                CASE cod.colour_id
                        WHEN 1 THEN 1
                        WHEN 2 THEN 2
                        WHEN 3 THEN 4
                        WHEN 4 THEN 8
                        WHEN 5 THEN 16
                        ELSE 0
                END
        ) AS colour,
        d.custom_id as cid,
        d.name as name,
        d.elo AS elo,
        t2.position_change
FROM colours_of_decks cod
RIGHT JOIN decks d ON d.id = cod.deck_id
LEFT join
(
    WITH matches_in_order AS (
        SELECT
            ec.*,
            ROW_NUMBER() OVER (PARTITION BY deck_id ORDER BY id DESC) AS rn
        FROM elo_changes ec
    ),
    elo_1_game_ago AS (
        SELECT
            deck_id,
            SUM(elo_change) AS elo_1_game_ago
        FROM matches_in_order
        WHERE rn > 1
        GROUP BY deck_id
    ),
    past_elo AS (
        SELECT
            deck_id,
            RANK() OVER (ORDER BY elo_1_game_ago DESC) AS position
        FROM elo_1_game_ago
    )
    SELECT
        d.id As deck,
        CASE
            WHEN RANK() OVER (ORDER BY d.elo DESC) = p.position THEN 0
            WHEN RANK() OVER (ORDER BY d.elo DESC) > p.position THEN -1
            ELSE 1
        END AS position_change
    FROM decks d
    JOIN past_elo p ON p.deck_id = d.id
    order by p.position desc
) t2 on d.id = t2.deck
GROUP BY id, position_change
ORDER BY elo DESC;');
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;

$targetSize = 128;
$currentSize = count($decks);

$n = 0;
for ($i = $currentSize; $i < $targetSize; $i++) {
    $n++;
    $decks[] = [
        'id' => 0,
        'colour' => 0,
        'cid' => 'Ukn0' . $n,
        'name' => 'Unknown deck',
        'elo' => 1000,
        'position_change' => 0
    ];
}


$pdo->beginTransaction();

$pdo->exec("TRUNCATE TABLE tournament");

$stmt = $pdo->prepare("
    INSERT INTO tournament (id, round, leftid, rightid)
    VALUES (:id, :round, :leftid, :rightid)
");
for ($i = 0; $i < 64; $i++) { // matches 0-63 (64) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 0, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', $decks[$i]['id'], PDO::PARAM_INT); // top 64 decks
    $stmt->bindValue(':rightid', $decks[127-$i]['id'], PDO::PARAM_INT); // bottom 64 decks
    $stmt->execute();
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 1 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-

for ($i = 64; $i < 96; $i++) { // matches 64-95 (32) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 1, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i - 64, PDO::PARAM_INT); // winners of matches 0-31
    $stmt->bindValue(':rightid', "W" . $i - 32, PDO::PARAM_INT); // winners of matches 32-63
    $stmt->execute();
}

for ($i = 96; $i < 128; $i++) { // matches 96-127 (32) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 1, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L" . $i - 96, PDO::PARAM_INT); // losers of matches 0-31
    $stmt->bindValue(':rightid', "L" . $i - 64, PDO::PARAM_INT); // losers of matches 32-63
    $stmt->execute();
}

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 2 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-

for ($i = 128; $i < 144; $i++) { // matches 128-143 (16) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 2, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-64, PDO::PARAM_INT); // winners of matches 64-79
    $stmt->bindValue(':rightid', "W" . $i-48, PDO::PARAM_INT); // winners of mtches 80-95
    $stmt->execute();
}

for ($i = 144; $i < 160; $i++) { // matches 144-159 (16) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 2, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L" . $i-80, PDO::PARAM_INT); // losers of matches 64-79
    $stmt->bindValue(':rightid', "L" . $i-64, PDO::PARAM_INT); // losers of matches 80-95
    $stmt->execute();
}

for ($i = 160; $i < 176; $i++) { // matches 160-175 (16) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 2, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-64, PDO::PARAM_INT); // winners of matches 96-111
    $stmt->bindValue(':rightid', "W" . $i-48, PDO::PARAM_INT); // winners of matches 112-127
    $stmt->execute();
}
//losers of matches 96-127 are eliminated

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 3 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-

for ($i = 176; $i < 184; $i++) { // matches 176-183 (8) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 3, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-48, PDO::PARAM_INT); // winners of matches 128-135
    $stmt->bindValue(':rightid', "W" . $i-40, PDO::PARAM_INT); // winners of matches 136-143
    $stmt->execute();
}

for ($i = 184; $i < 192; $i++) { // matches 184-191 (8) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 3, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L" . $i-56, PDO::PARAM_INT); // losers of matches 128-135
    $stmt->bindValue(':rightid', "L" . $i-48, PDO::PARAM_INT); // losers of matches 136-143
    $stmt->execute();
}

for ($i = 192; $i < 200; $i++) { // matches 192-199 (8) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 3, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-48, PDO::PARAM_INT); // winners of matches 144-151
    $stmt->bindValue(':rightid', "W" . $i-40, PDO::PARAM_INT); // winners of matches 152-159
    $stmt->execute();
}

for ($i = 200; $i < 208; $i++) { // matches 200-207 (8) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 3, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-40, PDO::PARAM_INT); // winners of matches 160-167
    $stmt->bindValue(':rightid', "W" . $i-32, PDO::PARAM_INT); // winners of matches 168-175
    $stmt->execute();
}

// losers of matches 144-175 are eliminated

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 4 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-

for ($i = 208; $i < 212; $i++) { // matches 208-211 (4) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 4, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-32, PDO::PARAM_INT); // winners of matches 176-179
    $stmt->bindValue(':rightid', "W" . $i-28, PDO::PARAM_INT); // winners of matches 180-183
    $stmt->execute();
}

for ($i = 212; $i < 216; $i++) { // matches 212-215 (4) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 4, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L" . $i-36, PDO::PARAM_INT); // losers of matches 176-179
    $stmt->bindValue(':rightid', "L" . $i-32, PDO::PARAM_INT); // losers of matches 180-183
    $stmt->execute();
}

for ($i = 216; $i < 220; $i++) { // matches 216-219 (4) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 4, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-32, PDO::PARAM_INT); // winners of matches 184-187
    $stmt->bindValue(':rightid', "W" . $i-28, PDO::PARAM_INT); // winners of matches 188-191
    $stmt->execute();
}

for ($i = 220; $i < 224; $i++) { // matches 220-223 (4) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 4, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-28, PDO::PARAM_INT); // winners of matches 192-195
    $stmt->bindValue(':rightid', "W" . $i-24, PDO::PARAM_INT); // winners of matches 196-199
    $stmt->execute();
}

for ($i = 224; $i < 228; $i++) { // matches 224-227 (4) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 4, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-24, PDO::PARAM_INT); // winners of matches 200-203
    $stmt->bindValue(':rightid', "W" . $i-20, PDO::PARAM_INT); // winners of matches 204-207
    $stmt->execute();
}

// losers of matches 184-207 are eliminated

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 5 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-

for ($i = 228; $i < 230; $i++) { // matches 228-229 (2) (0 losses)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-20, PDO::PARAM_INT); // winners of matches 208-209
    $stmt->bindValue(':rightid', "W" . $i-18, PDO::PARAM_INT); // winners of matches 210-211
    $stmt->execute();
}

for ($i = 230; $i < 232; $i++) { // matches 230-231 (2) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L" . $i-22, PDO::PARAM_INT); // losers of matches 208-209
    $stmt->bindValue(':rightid', "L" . $i-20, PDO::PARAM_INT); // losers of matches 210-211
    $stmt->execute();
}

for ($i = 232; $i < 234; $i++) { // matches 232-233 (2) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-20, PDO::PARAM_INT); // winners of matches 212-213
    $stmt->bindValue(':rightid', "W" . $i-18, PDO::PARAM_INT); // winners of matches 214-215
    $stmt->execute();
}

for ($i = 234; $i < 236; $i++) { // matches 234-235 (2) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-18, PDO::PARAM_INT); // winners of matches 216-217
    $stmt->bindValue(':rightid', "W" . $i-16, PDO::PARAM_INT); // winners of matches 218-219
    $stmt->execute();
}

for ($i = 236; $i < 238; $i++) { // matches 236-237 (2) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-16, PDO::PARAM_INT); // winners of matches 220-221
    $stmt->bindValue(':rightid', "W" . $i-14, PDO::PARAM_INT); // winners of matches 222-223
    $stmt->execute();
}

for ($i = 238; $i < 240; $i++) { // matches 238-239 (2) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 5, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W" . $i-14, PDO::PARAM_INT); // winners of matches 224-225
    $stmt->bindValue(':rightid', "W" . $i-12, PDO::PARAM_INT); // winners of matches 226-227
    $stmt->execute();
}

// losers of matches 212-227 are eliminated

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ROUND 6 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-


//using for loops for single matches is redundant but keeps the code style consistent and allows collapsing in editors

for ($i = 240; $i < 241; $i++) { // match 240 (1) (0 losses)  (half redundant match, both players are guaranteed spot in top 8)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W228", PDO::PARAM_INT); // winner of match 228
    $stmt->bindValue(':rightid', "W229", PDO::PARAM_INT); // winner of match 229
    $stmt->execute();
}

for ($i = 241; $i < 242; $i++) { // match 241 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "L228", PDO::PARAM_INT); // loser of match 228
    $stmt->bindValue(':rightid', "L229", PDO::PARAM_INT); // loser of match 229
    $stmt->execute();
}

for ($i = 242; $i < 243; $i++) { // match 242 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W230", PDO::PARAM_INT); // winner of match 230
    $stmt->bindValue(':rightid', "W231", PDO::PARAM_INT); // winner of match 231
    $stmt->execute();
}

for ($i = 243; $i < 244; $i++) { // match 243 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W232", PDO::PARAM_INT); // winner of match 232
    $stmt->bindValue(':rightid', "W233", PDO::PARAM_INT); // winner of match 233
    $stmt->execute();
}

for ($i = 244; $i < 245; $i++) { // match 244 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W234", PDO::PARAM_INT); // winner of match 234
    $stmt->bindValue(':rightid', "W235", PDO::PARAM_INT); // winner of match 235
    $stmt->execute();
}

for ($i = 245; $i < 246; $i++) { // match 245 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W236", PDO::PARAM_INT); // winner of match 236
    $stmt->bindValue(':rightid', "W237", PDO::PARAM_INT); // winner of match 237
    $stmt->execute();
}

for ($i = 246; $i < 247; $i++) { // match 246 (1) (1 loss)
    $stmt->bindValue(':id', $i, PDO::PARAM_INT);
    $stmt->bindValue(':round', 6, PDO::PARAM_INT);
    $stmt->bindValue(':leftid', "W238", PDO::PARAM_INT); // winner of match 238
    $stmt->bindValue(':rightid', "W239", PDO::PARAM_INT); // winner of match 239
    $stmt->execute();
}

// losers of matches 230-239 are eliminated










$stmt->execute();

$pdo->commit();
?>