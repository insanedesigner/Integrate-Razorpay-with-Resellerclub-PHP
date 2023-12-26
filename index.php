<?php
    session_start();
    require("functions.php");   //file which has required functions
    require("config.php");
?>      
    
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <!--<link rel="stylesheet" href="https://yegor256.github.io/tacit/tacit.min.css"/>-->
    <script language="JavaScript">
        function successClicked()
        {
            document.paymentpage.submit();
        }
        function failClicked()
        {
            document.paymentpage.status.value = "N";
            document.paymentpage.submit();
        }
        function pendingClicked()
        {
            document.paymentpage.status.value = "P";
            document.paymentpage.submit();
        }
</script>
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
  <?php
        
        
        //This filter removes data that is potentially harmful for your application. It is used to strip tags and remove or encode unwanted characters.
        $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
        
        //Below are the  parameters which will be passed from foundation as http GET request
        $paymentTypeId = $_GET["paymenttypeid"];  //payment type id
        $transId = $_GET["transid"];               //This refers to a unique transaction ID which we generate for each transaction
        $userId = $_GET["userid"];               //userid of the user who is trying to make the payment
        $userType = $_GET["usertype"];             //This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
        $transactionType = $_GET["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)

        $invoiceIds = $_GET["invoiceids"];         //comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
        $debitNoteIds = $_GET["debitnoteids"];     //comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"

        $description = $_GET["description"];
        
        $sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
        $accountingCurrencyAmount = $_GET["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency

        $redirectUrl = $_GET["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him

                        
        $checksum = $_GET["checksum"];   //checksum for validation

    echo "Type: <b>" . $transactionType . "</b><br><hr/>";
    echo "Invoice ID: <b>" . (int)$invoiceIds . "</b><br><br>";
    echo "Transaction ID: <b>" . $transId . "</b><br><br>";
    echo "Selling Currency:<b> INR</b><br><br>";    
    echo "Invoice Amount <b>Rs." . (int)$accountingCurrencyAmount . "</b><br>";
         
        if(verifyChecksum($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum))
        {
            $_SESSION['userType']=$userType;
            //$_SESSION['price']=$price;
            $_SESSION['userId']=$userId;
            $_SESSION['redirecturl']=$redirectUrl;
            $_SESSION['transid']=$transId;
            $_SESSION['accountingCurencyAmount']=$accountingCurrencyAmount;
            $_SESSION['sellingCurrencyAmount']=$sellingCurrencyAmount;

?>

<br/>
    <form id="checkout-selection" method="session" >
        <input type="checkbox" name="checkout" value="automatic" required> I agree to the above mentioned information and would like to continue<br>
        <!-- <input type="radio" name="checkout" value="orders">Manual Checkout Demo--><br>
        <input type="submit" class="button" value="Continue">
    </form>
    
    <br/>
    <?php   
    echo "<small>This transaction is a " . $transactionType . ". For the invoice number #" . $invoiceIds . " for an amount of Rs." .$accountingCurrencyAmount. " on the transaction id: " .$transId. "<br/>Payment Type ID" .$paymentTypeId. ". | For any Questions or help Call or WhatsApp +919562640880/ Mail: contact@easydoma.in</small> ";
    ?>     
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) 
        {
            var form = $('#checkout-selection');
            var radio = $('input[name="checkout"]');
            var choice = '';

            radio.change(function(e) 
            {
                choice = this.value;
                if (choice === 'orders') 
                {
                    form.attr('action', 'pay.php?checkout=manual');
                } 
                else 
                {
                    form.attr('action', 'pay.php?checkout=automatic');
                }
            });
        });
    </script>
<?php

        }
        else
        {

            echo "Checksum mismatch !";         

        }
    ?>
  </div>
    </center>
</body>
</html>