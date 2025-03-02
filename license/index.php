<?php 
// Include the configuration file  
require_once 'config.php'; 
?>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&currency=<?php echo $currency; ?>"></script>
<link rel="stylesheet" href="paypal.css">

<div class="rgb-bg">
<div class="terms-container">
    <h3>💡 Általános Szerződési Feltételek (ÁSZF) 💡</h3>
    <textarea readonly class="terms-text">
                    📝 **Hatályos: 2024. március 2.**  

Kérjük, figyelmesen olvassa el az alábbi szerződési feltételeket,  
mielőtt PayPal-on keresztül fizetést hajt végre.  

A **PayPal gombra kattintással** és a tranzakció elindításával  
Ön elfogadja a jelen ÁSZF-et.  

-------------------------------------------------------  

1️⃣ **Fizetési folyamat**  
   A fizetés a **PayPal biztonságos rendszerén** keresztül történik.  
   A Vásárló **e-mailben kap visszaigazolást**.  

2️⃣ **Termékek és árak**  
   - A feltüntetett árak **tartalmazzák az adókat** (ha van).  
   - Az árak **véglegesek**.  

3️⃣ **Szállítás és teljesítés**  
   - 📥 **Digitális termékek**: azonnal elérhetővé válnak.  
   - 📦 **Fizikai termékek**: a szállítás a megrendeléstől függ.  

4️⃣ **Elállási jog**  
   - ❌ **Digitális termékek**: nincs visszatérítés.  
   - ✅ **Fizikai termékek**: 14 napon belül élhet az elállási jogával.  

5️⃣ **Adatkezelés és biztonság**  
   A Vásárló adatait az **Adatvédelmi Szabályzat** szerint kezeljük.  

-------------------------------------------------------  

🔹 **Ügyfélszolgálat**  
📧 **E-mail**: chetan@mapdevelopment.hu  
📞 **Telefon**: 06702587769  
    </textarea>
    
    <label class="checkbox-container">
        <input type="checkbox" id="acceptTerms"> Elfogadom az ÁSZF-et
        <span class="checkmark"></span>
    </label>
    
    <button id="acceptButton" disabled>✅ Elfogadom</button>
</div>


    <!-- Fizetési felület (kezdetben rejtve) -->
    <div id="paymentSection" class="hidden">
        <div class="panel">
            <div class="overlay hidden">
                <div class="overlay-content">
                    <img src="css/loading.gif" alt="Processing..."/>
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
</script>

<style>
/* RGB háttér */
.rgb-bg {
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(270deg, #ff0000, #00ff00, #0000ff);
    background-size: 600% 600%;
    animation: rgbAnimation 10s infinite alternate;
}

/* RGB animáció */
@keyframes rgbAnimation {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}


.terms-container {
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    width: 90%;
    max-width: 600px;
}


.terms-text {
    width: 100%;
    height: 350px;
    padding: 10px;
    background: #222;
    color: #0f0;
    border: 1px solid #0f0;
    resize: none;
}


#acceptButton {
    margin-top: 10px;
    padding: 10px 20px;
    background: yellow;
    color: black;
    cursor: pointer;
    border-radius: 5px;
}

.hidden {
    display: none;
}
</style>
