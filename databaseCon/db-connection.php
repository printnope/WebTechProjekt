<?php
$server = "localhost";
$user   = "root";
$pass   = "";
$db     = "callabiketeam4";


$conn = new mysqli($server , $user , $pass , $db) or die ("Konnte Verbindung zur Datenbank nicht herstellen!");


return $conn;
?>
