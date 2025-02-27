<?php
session_start();

$_SESSION['color'] = $_POST['color'];

echo 'Session variable updated.';
?>