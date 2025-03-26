<?php 

/**
 * Fizetési nyugta generálása DOCX formátumban egy adott rendelési azonosító alapján.
 *
 * @author Kondor Milán, Kálmán Rómeó, Orsós Szabolcs
 */

/**
 * Ellenőrzi, hogy a `GET` kérésben szerepel-e a rendelési azonosító.
 *
 * @param string $_GET['order_id'] A rendelési azonosító az URL-ből.
 */

require_once 'config.php'; 
?>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&currency=<?php echo $currency; ?>"></script>
<link rel="stylesheet" href="paypal.css">
<title>MAP - Licensz</title>
<div class="rgb-bg">
<div class="terms-container">
    <h3>⚡ Általános Szerződési Feltételek (ÁSZF) - Licensz Vásárlás ⚡</h3>
    <textarea readonly class="terms-text" style="resize: none;">

✨ Hatályos: 2024. március 2.

Kérjük, figyelmesen olvassa el az alábbi szerződési feltételeket, mielőtt PayPal-on keresztül licenszet vásárol a játékhoz.

A PayPal gombra kattintással és a tranzakció elindításával Ön elfogadja a jelen ÁSZF-et.

1️⃣ Licensz vásárlási folyamat

A fizetés a PayPal biztonságos rendszerén keresztül történik.

A sikeres tranzakció után a vásárló egy licensz kulcsot kap, valamint egy online nyugtát, amely DOCX formátumban letölthető.

2️⃣ Licenszek és árak

A megvásárolható licenszek és azok árai a weboldalon kerülnek feltüntetésre.

A feltüntetett árak tartalmazzák az adókat (ha van).

Az árak véglegesek, utólagos módosításra nincs lehetőség.

3️⃣ Teljesítés és aktiválás

📥 Digitális licenszek: A vásárlás után azonnal aktiválásra kerülnek, és a vásárló megkapja az aktiválási kulcsot.

4️⃣ Elállási jog és visszatérítés

❌ Digitális licenszek esetén nincs lehetőség elállásra vagy visszatérítésre. A vásárlás végleges.

5️⃣ Adatkezelés és biztonság A vásárló adatait az Adatvédelmi Szabályzat szerint kezeljük, és harmadik fél számára nem adjuk ki.

🔍 További információ vagy kérdés esetén forduljon ügyfélszolgálatunkhoz:✉ E-mail: support@mapdevelopment.hu📞 Telefon: 06702587769

Köszönjük, hogy nálunk vásárolja meg licenszét! ✨  

    </textarea>
    
    <label class="checkbox-container">
        <input type="checkbox" id="acceptTerms" disabled>
        <span class="checkmark"></span>
        Elfogadom az ÁSZF-et
    </label><br>
    
    <button id="acceptButton" disabled>✅ Elfogadom</button>

</div>

    <!-- Fizetési felület (kezdetben rejtve) -->
    <div id="paymentSection" class="hidden">
        <div class="panel">
            <div class="hidden overlay">
                <div class="overlay-content">
                    <img src="loading.gif" alt="Feldolgozás..."/>
                </div>
            </div>

            <div class="panel-heading">
                <h3 class="panel-title">💰 <?php echo '$'.$itemPrice; ?> Fizetése PayPal Használatával 💰</h3>
                
                <!-- Termék Információ -->
                <p><b>🛍 Termék Neve:</b> <?php echo $itemName; ?></p>
                <p><b>💲 Ár:</b> <?php echo '$'.$itemPrice.' '.$currency; ?></p>
            </div>
            <div class="panel-body">
                <!-- Visszajelzés -->
                <div id="paymentResponse" class="hidden"></div>
                
                <!-- PayPal gomb -->
                <div id="paypal-button-container"></div>
            </div>
        </div>
    </div>
</div>

<script>
    
    document.addEventListener("DOMContentLoaded", function() {
        const acceptCheckbox = document.getElementById("acceptTerms");
        const acceptButton = document.getElementById("acceptButton");
        const paymentSection = document.getElementById("paymentSection");

        // Gomb engedélyezése ha a checkbox be van pipálva
        acceptCheckbox.addEventListener("change", function() {
            acceptButton.disabled = !this.checked;
        });

        // Fizetési felület megjelenítése az ÁSZF elfogadása után
        acceptButton.addEventListener("click", function() {
            paymentSection.classList.remove("hidden");
            document.querySelector(".terms-container").classList.add("hidden");
        });
    });

    paypal.Buttons({
        createOrder: (data, actions) => {
            return actions.order.create({
                "purchase_units": [{
                    "custom_id": "<?php echo $itemNumber; ?>",
                    "description": "<?php echo $itemName; ?>",
                    "amount": {
                        "currency_code": "<?php echo $currency; ?>",
                        "value": <?php echo $itemPrice; ?>
                    }
                }]
            });
        },
        onApprove: (data, actions) => {
            return actions.order.capture().then(function(orderData) {
                setProcessing(true);

                var postData = {paypal_order_check: 1, order_id: orderData.id};
                fetch('paypal_checkout_validate.php', {
                    method: 'POST',
                    headers: {'Accept': 'application/json'},
                    body: encodeFormData(postData)
                })
                .then((response) => response.json())
                .then((result) => {
                    if(result.status == 1){
                        window.location.href = "payment-status.php?checkout_ref_id="+result.ref_id;
                    } else {
                        const messageContainer = document.querySelector("#paymentResponse");
                        messageContainer.classList.remove("hidden");
                        messageContainer.textContent = result.msg;
                        setTimeout(() => messageContainer.classList.add("hidden"), 5000);
                    }
                    setProcessing(false);
                })
                .catch(error => console.log(error));
            });
        }
    }).render('#paypal-button-container');

    const encodeFormData = (data) => {
        var form_data = new FormData();
        for (var key in data) {
            form_data.append(key, data[key]);
        }
        return form_data;
    }

    const setProcessing = (isProcessing) => {
        document.querySelector(".overlay").classList.toggle("hidden", !isProcessing);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const termsText = document.querySelector(".terms-text");
        const acceptCheckbox = document.getElementById("acceptTerms");

        // Kezdetben letiltjuk a checkboxot
        acceptCheckbox.disabled = true;

        // Figyeljük, hogy a textarea végére görgettek-e
        termsText.addEventListener("scroll", function() {
            if (termsText.scrollTop + termsText.clientHeight >= termsText.scrollHeight - 5) {
                acceptCheckbox.disabled = false; // Engedélyezzük a checkboxot
            }
        });
    });

</script>

