<html>
   <head>
   	  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;500&display=swap" rel="stylesheet">
  
  <style>

    .button {
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
    font-weight: 100;
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
<?php

require('config.php');

session_start();
session_save_path("./"); //path on your server where you are storing session


	//file which has required functions
	require("functions.php");

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);
	$payment = $api->payment->fetch($_POST['razorpay_payment_id']);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
		
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
		$status="N";
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
	
{
	$status="Y";
	$_SESSION['accountingCurencyAmount']=($payment->amount)/100;
	$_SESSION['sellingCurrencyAmount']=($payment->amount)/100;
	echo "<br/><br/>Payment Status: <b>Success</b><br/><br/>";
	echo "Transaction ID: " . $_SESSION['transid'] . "<br/><br/><hr/>";
	echo "Amount: " . $_SESSION['accountingCurencyAmount'] . "<br/><br/>";
	// echo "Amount: " . $_SESSION['sellingCurrencyAmount'] . "<br/><br/>";
	echo "User ID: " . $_SESSION['userId'] . "<br/>";
	echo "User Type: " . $_SESSION['userType'] . "<br/>";
	
	$redirectUrl = $_SESSION['redirecturl'];  // redirectUrl received from foundation
		$transId = $_SESSION['transid'];		 //Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
		$sellingCurrencyAmount = $_SESSION['sellingCurrencyAmount'];
		$accountingCurrencyAmount = $_SESSION['accountingCurencyAmount'];
	//echo "<h4>Payment Status: " . $status . "<br/>";
	//echo $_SESSION['userType'];
	//echo		$_SESSION['price'];
	//echo		$_SESSION['userId'];
	//echo		$_SESSION['redirecturl'];
	//echo		$_SESSION['transid'];
	
	//echo $payment->amount;
	//echo		$_SESSION['accountingCurencyAmount'];
	//echo		$_SESSION['sellingCurrencyAmount'];
	//$_POST['razorpay_payment_id']
    /*$html = "<p>Your payment was successful</p>
             <p>Payment ID: {$payment->status}</p><br/>
			 <p>Payment ID: {$payment->method}</p><br/>
			 <p>Payment ID: {$payment->amount}</p><br/>
			 <p>Payment ID: {$payment->status}</p><br/>
			 <p>Payment ID: {$payment->status}</p><br/>";*/
}
else
{
	$status="N";
   $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}

srand((double)microtime()*1000000);
		$rkey = rand();


		$checksum =generateChecksum($transId,$sellingCurrencyAmount,$accountingCurrencyAmount,$status, $rkey,$key);

			echo "<br/> Click below Continue to the Website <br>";
//echo $html;
?>
      <br/>
<form name="f1" action="<?php echo $redirectUrl;?>">
		<input type="submit" class="button" value="Click here to Continue"><BR>
			<input type="hidden" name="transid" value="<?php echo $transId;?>">
		    <input type="hidden" name="status" value="<?php echo $status;?>">
			<input type="hidden" name="rkey" value="<?php echo $rkey;?>">
		    <input type="hidden" name="checksum" value="<?php echo $checksum;?>">
		    <input type="hidden" name="sellingamount" value="<?php echo $sellingCurrencyAmount;?>">
			<input type="hidden" name="accountingamount" value="<?php echo $accountingCurrencyAmount;?>">

			
		</form>
  </div>
</center>
</body>
</html>