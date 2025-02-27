<?php
session_start();
if(isset($_SESSION['name'])){
	$text_message = "<div class='msgln'><b class='user-name ".$_SESSION['color']."'>".$_SESSION['name']."</b><strong> has won!!</strong><br></div>";
    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
}
?>