<html>
  <head>
     <style>

    input.razorpay-payment-button {
  background-color: #04AA6D; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
    .bbbody {
    width: 70%;
    background: #ededed;
    padding: 30px 10px;
    border-radius: 15px;
    box-shadow: -1px 3px 8px 0px #0000002b;
    font-weight: 200;
    font-family: 'Roboto', sans-serif;
}
    .hide{
    display:none;}
  </style>
  </head>
<body>
<center>
    <div class="bbbody">
  <img src="https://cdnassets.com/ui/resellerdata/1200000_1229999/1222876/supersite2/supersite/themes/EliteGreen-EasyDomain/images/logo.gif">
<br/><br/>
<br/><br/><br/><br/>
<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();
session_save_path("./"); //path on your server where you are storing session


	//file which has required functions
	require("functions.php");

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
$orderData = [
    'receipt'         => $_SESSION['transId'],
    'amount'          => $_SESSION['accountingCurencyAmount'] * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$checkout = 'automatic';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "Easy Domain",
    "description"       => $_SESSION['userType'],
    "image"             => "https://cdnassets.com/ui/resellerdata/1200000_1229999/1222876/supersite2/supersite/themes/EliteGreen-EasyDomain/images/logo.gif",
    "prefill"           => [
    "name"              => $_SESSION['sellingCurrencyAmount'],
    "email"             => "",
    "contact"           => "",
    ],
    "notes"             => [
    "address"           => "",
    "merchant_order_id" => $_SESSION['transId'],
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");
$status = "N";
$redirectUrl = $_SESSION['redirecturl'];  // redirectUrl received from foundation
		$transId = $_SESSION['transid'];		 //Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
		$sellingCurrencyAmount = $_SESSION['sellingCurrencyAmount'];
		$accountingCurrencyAmount = $_SESSION['accountingCurencyAmount'];
		
srand((double)microtime()*1000000);
		$rkey = rand();


		$checksum =generateChecksum($transId,$sellingCurrencyAmount,$accountingCurrencyAmount,$status, $rkey,$key);

			echo " <br>";



?>

<br/>
<form name="f1" action="<?php echo $redirectUrl;?>">
		<input type="submit" class="buttonback" value="Click here go back to Cart"><BR>
			<input type="hidden" name="transid" value="<?php echo $transId;?>">
		    <input type="hidden" name="status" value="<?php echo $status;?>">
			<input type="hidden" name="rkey" value="<?php echo $rkey;?>">
		    <input type="hidden" name="checksum" value="<?php echo $checksum;?>">
		    <input type="hidden" name="sellingamount" value="<?php echo $sellingCurrencyAmount;?>">
			<input type="hidden" name="accountingamount" value="<?php echo $accountingCurrencyAmount;?>">

			
		</form>
<div>
</center>
</body>
</html>