<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.html");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_mega";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolat sikertelen: " . $conn->connect_error);
}

// Count total users and total coins
$user_count_sql = "SELECT COUNT(*) AS total_users FROM users";
$total_users = $conn->query($user_count_sql)->fetch_assoc()['total_users'];

$coin_count_sql = "SELECT SUM(coin) AS total_coins FROM users";
$total_coins = $conn->query($coin_count_sql)->fetch_assoc()['total_coins'] ?? 0;

$alert_class = "success";

// Coin update logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['coin'])) {
    $user_id = $_POST['user_id'];
    $new_coin_value = $_POST['coin'];

    $check_sql = "SELECT coin FROM users WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_coin_value);
    $stmt->fetch();
    $stmt->close();

    if ($current_coin_value != $new_coin_value) {
        $update_sql = "UPDATE users SET coin = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $new_coin_value, $user_id);

        if ($stmt->execute()) {
            $message = "Érmék száma sikeresen frissítve!";
        } else {
            $message = "Hiba történt a frissítés során.";
            $alert_class = "error";
        }

        $stmt->close();
    } else {
        $message = "Az új érmék száma nem lehet ugyan az!";
        $alert_class = "error";
    }
}

// User deletion logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "Felhasználó sikeresen törölve!";
    } else {
        $message = "Hiba történt a törlés során.";
        $alert_class = "error";
    }

    $stmt->close();
}

$sql = "SELECT id, username, coin FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAP - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../images/Mega action.ico" type="image/x-icon">
</head>
<body>
<div class="admin-container">
    <h2>Admin Panel</h2>

    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h3>Regisztrált Felhasználók</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Összes Érme</h3>
            <p><?php echo $total_coins; ?></p>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert-message <?php echo $alert_class; ?>">
            <?php echo $message; ?>
            <span class="alert-message-close" onclick="closeAlert()">×</span>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Felhasználónév</th>
                <th>Érmék száma</th>
                <th>Művelet</th>
                <th>Törlés</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['coin']) . "</td>";
                    echo "<td>
                            <form method='post' action='admin.php'>
                                <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                                <input type='number' name='coin' value='" . $row['coin'] . "' required>
                                <button type='submit' class='update-button'>Frissítés</button>
                            </form>
                          </td>";
                    echo "<td>
                            <form method='post' action='admin.php'>
                                <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                                <button type='submit' name='delete_user' class='delete-button'>Felhasználó Törlése</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nincs elérhető adat</td></tr>";
            }
            ?>
        </table>
    </div>

    <form method="post" action="admin.php">
        <button type="submit" name="logout" class="logout-button">Kijelentkezés</button>
    </form>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        const alertMessage = document.querySelector('.alert-message');
        if (alertMessage) {
            setTimeout(function () {
                alertMessage.classList.add('auto-hide');
                alertMessage.addEventListener('animationend', function () {
                    alertMessage.remove();
                });
            }, 8000);
        }
    });

    function closeAlert() {
        const alertMessage = document.querySelector('.alert-message');
        if (alertMessage) {
            alertMessage.classList.add('auto-hide');
            alertMessage.addEventListener('animationend', function () {
                alertMessage.remove();
            });
        }
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
