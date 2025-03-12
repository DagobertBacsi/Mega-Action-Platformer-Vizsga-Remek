<?php

/**
 * Adatbázis kapcsolat létrehozása MySQLi használatával.
 *
 * @var string $servername A szerver neve (általában localhost).
 * @var string $db_username Az adatbázis felhasználóneve.
 * @var string $db_password Az adatbázis jelszava.
 * @var string $dbname Az adatbázis neve.
 * @var mysqli $conn Az adatbáziskapcsolat objektuma.
 * @author Kondor Milán, Kálmán Rómeó, Orsós Szabolcs
 */

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_mega";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}
?>
