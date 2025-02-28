<?php 
// Include the configuration file 
require_once 'config.php'; 
 
// Include the database connection file 
include_once 'dbConnect.php'; 
 
// Include the PayPal API library 
require_once 'PaypalCheckout.class.php'; 
$paypal = new PaypalCheckout; 
 
$response = array('status' => 0, 'msg' => 'Transaction Failed!'); 
if(!empty($_POST['paypal_order_check']) && !empty($_POST['order_id'])){ 
    // Validate and get order details with PayPal API 
    try {  
        $order = $paypal->validate($_POST['order_id']); 
    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 
     
    if(!empty($order)){ 
        $order_id = $order['id']; 
        $intent = $order['intent']; 
        $order_status = $order['status']; 
        $order_time = date("Y-m-d H:i:s", strtotime($order['create_time'])); 
 
        if(!empty($order['purchase_units'][0])){ 
            $purchase_unit = $order['purchase_units'][0]; 
 
            $item_number = $purchase_unit['custom_id']; 
            $item_name = $purchase_unit['description']; 
             
            if(!empty($purchase_unit['amount'])){ 
                $currency_code = $purchase_unit['amount']['currency_code']; 
                $amount_value = $purchase_unit['amount']['value']; 
            } 
 
            if(!empty($purchase_unit['payments']['captures'][0])){ 
                $payment_capture = $purchase_unit['payments']['captures'][0]; 
                $transaction_id = $payment_capture['id']; 
                $payment_status = $payment_capture['status']; 
            } 
 
            if(!empty($purchase_unit['payee'])){ 
                $payee = $purchase_unit['payee']; 
                $payee_email_address = $payee['email_address']; 
                $merchant_id = $payee['merchant_id']; 
            } 
        } 
 
        $payment_source = ''; 
        if(!empty($order['payment_source'])){ 
            foreach($order['payment_source'] as $key=>$value){ 
                $payment_source = $key; 
            } 
        } 
 
        if(!empty($order['payer'])){ 
            $payer = $order['payer']; 
            $payer_id = $payer['payer_id']; 
            $payer_name = $payer['name']; 
            $payer_given_name = !empty($payer_name['given_name'])?$payer_name['given_name']:''; 
            $payer_surname = !empty($payer_name['surname'])?$payer_name['surname']:''; 
            $payer_full_name = trim($payer_given_name.' '.$payer_surname); 
            $payer_full_name = filter_var($payer_full_name, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH); 
 
            $payer_email_address = $payer['email_address']; 
            $payer_address = $payer['address']; 
            $payer_country_code = !empty($payer_address['country_code'])?$payer_address['country_code']:''; 
        } 
 
        if(!empty($order_id) && $order_status == 'COMPLETED'){ 
            // Check if any transaction data is exists already with the same TXN ID 
            $sqlQ = "SELECT id FROM transactions WHERE transaction_id = ?"; 
            $stmt = $db->prepare($sqlQ);  
            $stmt->bind_param("s", $transaction_id); 
            $stmt->execute(); 
            $stmt->bind_result($row_id); 
            $stmt->fetch(); 
             
            $payment_id = 0; 
            if(!empty($row_id)){ 
                $payment_id = $row_id; 
            }else{ 
                // Insert transaction data into the database 
                $sqlQ = "INSERT INTO transactions (item_number,item_name,item_price,item_price_currency,payer_id,payer_name,payer_email,payer_country,merchant_id,merchant_email,order_id,transaction_id,paid_amount,paid_amount_currency,payment_source,payment_status,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())"; 
                $stmt = $db->prepare($sqlQ); 
                $stmt->bind_param("ssdsssssssssdssss", $item_number, $item_name, $itemPrice, $currency, $payer_id, $payer_full_name, $payer_email_address, $payer_country_code, $merchant_id, $payee_email_address, $order_id, $transaction_id, $amount_value, $currency_code, $payment_source, $payment_status, $order_time); 
                $insert = $stmt->execute(); 
                 
                if($insert){ 
                    $payment_id = $stmt->insert_id; 
                } 
            } 
 
            if(!empty($payment_id)){ 
                $ref_id_enc = base64_encode($transaction_id); 
                $response = array('status' => 1, 'msg' => 'Transaction completed!', 'ref_id' => $ref_id_enc); 
            } 
        } 
    }else{ 
        $response['msg'] = $api_error; 
    } 
} 
echo json_encode($response); 
?>