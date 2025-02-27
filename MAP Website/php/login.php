<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_mega";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username === 'admin' && $password === 'adminjelszo') {
        $_SESSION['username'] = $username;
        header("Location: admin.php");
        exit();
    } else {
        $hashed_password = md5($password);

        $sql = "SELECT * FROM users WHERE username = '$username' AND pass = '$hashed_password'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $_SESSION['username'] = $username;
            header("Location: scoreboard.php");
            exit();
        } else {
            $error = "Hibás felhasználónév vagy jelszó!";
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
    <title>MAP - Bejelentkezés</title>
    <link rel="stylesheet" href="login.css">
    <link rel="shortcut icon" href="../images/Mega action.ico" type="image/x-icon">
</head>
<body>
        <div id="preloader" class="preloader">
        <div class="title">Betöltés: Bejelentkezés</div>
        <div class="cube"></div>
        <div class="progress-bar">
            <div class="progress"></div>
        </div>
    </div>
    <div class="login-container">
            <h2>Bejelentkezés</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Felhasználónév" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Jelszó" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
                </div>
                <button type="submit" class="login-button">Bejelentkezés</button>
            </form>
        </div>

        <a href="../index.html" class="back-button">Vissza a főlapra</a>

    <script>

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById("password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const preloader = document.getElementById("preloader");
            
            preloader.style.opacity = "0"; 
            preloader.style.transition = "opacity 0.5s ease";

            setTimeout(() => {
                preloader.style.display = "none";
                document.body.classList.add("loaded");
            }, 500);
        }, 3000);
    });

    </script>
    
</body>
</html>