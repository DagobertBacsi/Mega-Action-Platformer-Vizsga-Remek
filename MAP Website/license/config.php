<?php 
 
// Termék adatai 
$itemNumber = "MAP12345"; 
$itemName = "Mega Action Platformer licensz"; 
$itemPrice = 1;  
$currency = "USD"; 
 
/* PayPal REST API konfiguráció 

 */ 
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
define('PAYPAL_SANDBOX_CLIENT_ID', 'AR4no0PwlYrsZ2dBgT7jO9EfjXzt5Ij2EKetGe3m5lfodbZ2I8FcQngdD8JD415Dgq676qeCdWNyj9JQ'); 
define('PAYPAL_SANDBOX_CLIENT_SECRET', 'EPkpGkWlqaKQAA0hVNsnIc2YIruMhrbkTtGaqiFKY9XM8czMO-IwmJL3PBmhU1-YVs9QkrdXIFvwRXIl'); 
define('PAYPAL_PROD_CLIENT_ID', ''); 
define('PAYPAL_PROD_CLIENT_SECRET', ''); 
  
// Database configuration  
define('DB_HOST', 'localhost');  
define('DB_USERNAME', 'root');  
define('DB_PASSWORD', '');  
define('DB_NAME', 'db_mega'); 
 
?>