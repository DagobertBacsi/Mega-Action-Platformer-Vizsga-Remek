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

$user_count_sql = "SELECT COUNT(*) AS total_users FROM users";
$total_users = $conn->query($user_count_sql)->fetch_assoc()['total_users'];

$coin_count_sql = "SELECT SUM(coin) AS total_coins FROM users";
$total_coins = $conn->query($coin_count_sql)->fetch_assoc()['total_coins'] ?? 0;

$alert_class = "success";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['coin'])) {
    $user_id = $_POST['user_id'];
    $new_coin_value = $_POST['coin'];

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
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ban_license'])) {
    $license_id = $_POST['license_id'];

    $ban_sql = "UPDATE Licensz SET banned = 1 WHERE id = ?";
    $stmt = $conn->prepare($ban_sql);
    $stmt->bind_param("i", $license_id);

    if ($stmt->execute()) {
        $message = "Licenszkulcs sikeresen bannolva!";
    } else {
        $message = "Hiba történt a bannolás során.";
        $alert_class = "error";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unban_license'])) {
    $license_id = $_POST['license_id'];

    $unban_sql = "UPDATE Licensz SET banned = 0 WHERE id = ?";
    $stmt = $conn->prepare($unban_sql);
    $stmt->bind_param("i", $license_id);

    if ($stmt->execute()) {
        $message = "Licenszkulcs sikeresen feloldva!";
    } else {
        $message = "Hiba történt a feloldás során.";
        $alert_class = "error";
    }
    $stmt->close();
}

$sql = "SELECT id, username, coin FROM users";
$result = $conn->query($sql);

$license_sql = "SELECT id, license_key, banned FROM Licensz";
$license_result = $conn->query($license_sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAP - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
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
        </div>
    <?php endif; ?>

    <h3>Felhasználók kezelése</h3>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>Felhasználónév</th>
                <th>Érmék száma</th>
                <th>Művelet</th>
                <th>Törlés</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['coin']); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="coin" value="<?php echo $row['coin']; ?>" required>
                            <button type="submit">Frissítés</button>
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_user">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <h3>Licenszkulcsok kezelése</h3>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>Licenszkulcs</th>
                <th>Bannolva</th>
                <th>Bannolás/Feloldás</th>
            </tr>
            <?php while ($row = $license_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['license_key']); ?></td>
                    <td><?php echo $row['banned'] ? '❌ Igen' : '✅ Nem'; ?></td>
                    <td>
                        <?php if (!$row['banned']): ?>
                            <form method="post">
                                <input type="hidden" name="license_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="ban_license" class="delete-button">Bannolás</button>
                            </form>
                        <?php else: ?>
                            <form method="post">
                                <input type="hidden" name="license_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="unban_license" class="update-button">Feloldás</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="logout-container">
        <form method="post">
            <button type="submit" name="logout" class="logout-button">Kijelentkezés</button>
        </form>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
