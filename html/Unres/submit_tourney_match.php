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
	$sql = "
		UPDATE tournament
		SET winnerid = :winnerid,
			loserid  = :loserid
		WHERE id = :id
	";

	$stmt = $pdo->prepare($sql);

	$stmt->execute([
		':winnerid' => $winnerId,
		':loserid'  => $loserId,
		':id'       => $matchId
	]);
?>