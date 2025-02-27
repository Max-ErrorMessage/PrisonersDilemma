<?php
session_start();
if(isset($_SESSION['name'])){
    $text = $_POST['text'];
	
    file_put_contents("chessLog.html", $text, LOCK_EX);
}
?>