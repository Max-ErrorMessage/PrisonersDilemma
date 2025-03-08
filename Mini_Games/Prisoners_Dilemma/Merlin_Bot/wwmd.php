<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
} else {
    die("Access Denied");
}
?>