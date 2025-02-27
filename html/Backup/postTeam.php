<?php
session_start();
if(isset($_SESSION['name'])){
    $text = $_POST['text'];
	
	$text_message = "<div class='msgln'><b class='".$_SESSION['color']."'>".$_SESSION['name']."</b> has joined the ".stripslashes(htmlspecialchars($text))." team!<br></div>";
    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
}
?>