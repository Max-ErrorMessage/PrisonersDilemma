<?php
/*
 * updates the results of a tournament match in the database
 *
 * Author: James Aris
 */
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	include "/var/www/html/Unres/db.php";
	$matchId  = (int)$_POST['matchId'];
	$winnerId = (int)$_POST['winnerId'];
	$loserId = (int)$_POST['loserId'];

	// Update the match result in the database
	$stmt = $conn->prepare("UPDATE tournament_matches SET winner_id = ?, loser_id = ? WHERE id = ?");
	$stmt->bind_param("iii", $winnerId, $loserId, $matchId);
	$stmt->execute();
?>