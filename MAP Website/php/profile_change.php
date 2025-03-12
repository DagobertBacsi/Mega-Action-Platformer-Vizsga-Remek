<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email'])) {
        $new_email = $_POST['email'];
        $sql = "UPDATE users SET email='$new_email' WHERE username='$username'";
        if ($conn->query($sql) === TRUE) {
            $message = "Az e-mail cím sikeresen frissítve!";
        } else {
            $message = "Hiba történt az e-mail frissítésekor: " . $conn->error;
        }
    }

    if (!empty($_POST['password'])) {
        $new_password = md5($_POST['password']);
        $sql = "UPDATE users SET pass='$new_password' WHERE username='$username'";
        if ($conn->query($sql) === TRUE) {
            $message = "A jelszó sikeresen frissítve!";
        } else {
            $message = "Hiba történt a jelszó frissítésekor: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAP - Fiók Módosítása</title>
    <link rel="stylesheet" href="profile_change.css">
</head>
<body>
    <div class="profile-change-container">
        <h2>Fiók Módosítása</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" action="profile_change.php">
            <div class="form-group">
                <label for="email">Új e-mail cím:</label>
                <input type="email" name="email" id="email" placeholder="Adja meg az új e-mail címét">
            </div>
            <div class="form-group">
                <label for="password">Új jelszó:</label>
                <input type="password" name="password" id="password" placeholder="Adja meg az új jelszót">
            </div>
            <button type="submit" class="save-button">Mentés</button>
        </form>
        <a href="scoreboard.php" class="back-button">Vissza a Scoreboardhoz</a>
    </div>
</body>
</html>
