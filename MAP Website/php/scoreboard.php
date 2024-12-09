<?php
session_start();

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat beállítása
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_mega";

// Kapcsolódás az adatbázishoz
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}

// Kijelentkezés kezelése
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.html");
    exit();
}

// Felhasználók lekérdezése coin szerint csökkenő sorrendben
$sql = "SELECT username, coin FROM users ORDER BY coin DESC";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAP - Scoreboard</title>
    <link rel="stylesheet" href="scoreboard.css">
    <link rel="shortcut icon" href="../images/Mega action.ico" type="image/x-icon">
</head>
<body>

    <!-- Preloader -->
    <div id="preloader" class="preloader">
            <div class="title">Betöltés: Scoreboard</div>
            <div class="cube"></div>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>

        <div class="scoreboard-container">
        <h2>Scoreboard</h2>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Felhasználónév</th>
                    <th>Érmék száma</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    $rank = 1;
                    while ($row = $result->fetch_assoc()) {
                        $class = '';
                        if ($rank == 1) $class = 'gold';
                        elseif ($rank == 2) $class = 'silver';
                        elseif ($rank == 3) $class = 'bronze';

                        echo "<tr class='" . $class . "'><td>" . htmlspecialchars($row['username']) . "</td><td>" . htmlspecialchars($row['coin']) . "</td></tr>";
                        $rank++;
                    }
                } else {
                    echo "<tr><td colspan='2'>Nincs elérhető adat</td></tr>";
                }
                ?>
            </table>
        </div>
        
            <form method="post" action="scoreboard.php">
                <button type="submit" name="logout" class="logout-button">Kijelentkezés</button>
            </form>
    </div>

    <script>

        document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => {
                    const preloader = document.getElementById("preloader");
                    const scoreboardContainer = document.querySelector(".scoreboard-container");
                    
                    preloader.style.opacity = "0"; 
                    preloader.style.transition = "opacity 0.5s ease";

                    setTimeout(() => {
                        preloader.style.display = "none";
                        document.body.classList.add("loaded");

                        scoreboardContainer.classList.add("animate");
                    }, 500);
                }, 3000);
            });

    </script>

</body>
</html>

<?php
$conn->close();
?>
