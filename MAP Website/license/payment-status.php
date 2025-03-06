<?php 
require_once 'config.php'; 
require_once 'dbConnect.php'; 
 
$payment_ref_id = $statusMsg = ''; 
$status = 'error'; 
$license_key = ''; 
 
if(!empty($_GET['checkout_ref_id'])) { 
    $payment_txn_id  = base64_decode($_GET['checkout_ref_id']); 
     
    $sqlQ = "SELECT id, payer_id, payer_name, payer_email, payer_country, order_id, transaction_id, 
                    paid_amount, paid_amount_currency, payment_source, payment_status, created 
             FROM transactions WHERE transaction_id = ?"; 
    $stmt = $db->prepare($sqlQ);  
    $stmt->bind_param("s", $payment_txn_id); 
    $stmt->execute(); 
    $stmt->store_result(); 
 
    if($stmt->num_rows > 0) { 
        $stmt->bind_result($payment_ref_id, $payer_id, $payer_name, $payer_email, $payer_country, 
                           $order_id, $transaction_id, $paid_amount, $paid_amount_currency, 
                           $payment_source, $payment_status, $created); 
        $stmt->fetch(); 
        
        if ($payment_status === 'COMPLETED') { 
            $status = 'success'; 
            $statusMsg = 'A fizetés sikeres volt!'; 
            
            $checkQuery = "SELECT license_key FROM Licensz WHERE order_id = ?";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bind_param("s", $order_id);
            $checkStmt->execute();
            $checkStmt->store_result();
            
            if ($checkStmt->num_rows > 0) {
                $checkStmt->bind_result($license_key);
                $checkStmt->fetch();
            } else {
                function generateLicenseKey($length = 24) { 
                    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, $length); 
                } 
                
                $license_key = generateLicenseKey(); 
                
                $insertQuery = "INSERT INTO Licensz (payer_id, order_id, license_key, created_at) VALUES (?, ?, ?, NOW())"; 
                $insertStmt = $db->prepare($insertQuery); 
                $insertStmt->bind_param("sss", $payer_id, $order_id, $license_key); 
                $insertStmt->execute(); 
            }
        } else { 
            $statusMsg = "A tranzakció sikertelen!"; 
        } 
    } else { 
        $statusMsg = "A tranzakció nem található!"; 
    } 
} else { 
    header("Location: index.php"); 
    exit; 
} 
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fizetési Státusz</title>
    <link rel="stylesheet" href="payment-status.css">
    <style>
        .copy-btn, .save-btn {
            background-color: #0070ba;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 10px;
        }
        .copy-btn:hover, .save-btn:hover {
            background-color: #005f9e;
        }
    </style>
</head>
<body>

<?php if(!empty($payment_ref_id)){ ?>
    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>
    
    <h4>Fizetési Információ</h4>
    <p><b>Hivatkozási szám:</b> <?php echo $payment_ref_id; ?></p>
    <p><b>Rendelési azonosító:</b> <?php echo $order_id; ?></p>
    <p><b>Tranzakciós azonosító:</b> <?php echo $transaction_id; ?></p>
    <p><b>Fizetett összeg:</b> <?php echo $paid_amount.' '.$paid_amount_currency; ?></p>
    <p><b>Fizetési státusz:</b> <?php echo $payment_status; ?></p>
    <p><b>Dátum:</b> <?php echo $created; ?></p>
    
    <h4>Vásárló adatai</h4>
    <p><b>Azonosító:</b> <?php echo $payer_id; ?></p>
    <p><b>Név:</b> <?php echo $payer_name; ?></p>
    <p><b>Email:</b> <?php echo $payer_email; ?></p>
    <p><b>Ország:</b> <?php echo $payer_country; ?></p>
    
    <?php if (!empty($license_key)) { ?>
        <h4>Licensz Kulcs</h4>
        <p id="licenseKey"><b><?php echo $license_key; ?></b></p>
        <button class="copy-btn" onclick="copyLicenseKey()">Másolás</button>
        <p style="color: red; font-weight: bold;">Mindenképp mentsd el a licensz kulcsot!</p>
        <script>
            function copyLicenseKey() {
                var copyText = document.getElementById("licenseKey").innerText;
                navigator.clipboard.writeText(copyText).then(() => {
                    alert("Licensz kulcs másolva!");
                });
            }
        </script>
    <?php } ?>

    <form action="generate-docx.php" method="GET">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <button type="submit" class="save-btn">Mentés DOCX-ként</button>
    </form>

    <button class="save-btn back-btn">Vissza a főoldalra</button>

    <script>
        document.querySelector(".back-btn").addEventListener("click", function(event) {
        event.preventDefault();
        const confirmed = confirm("Biztosan vissza szeretne lépni a főoldalra? Elmentette a licensz kulcsot?");
        if (confirmed) {
        window.location.href = "../index.html";
        }
    });
    </script>

<?php } else { ?>
    <h1 class="error">A fizetés sikertelen!</h1>
    <p class="error"><?php echo $statusMsg; ?></p>
<?php } ?>

</body>
</html>
