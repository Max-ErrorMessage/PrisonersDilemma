<?php

/*
Help file for accessing the database.
This file is called by a variety of other files as a shorthand for defining the necessary variables and initialising
the connection

Authors: James Aris, Max Worby
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//commented these out for security reasons
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$host = "localhost";
$dbname = "Twokie";
$username = "TheUnresEloBot";
$password = "unt1m31y-MALFUNCTION";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
