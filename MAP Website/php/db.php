<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_mega";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}
?>
