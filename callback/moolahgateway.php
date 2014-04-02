<?php

//$file=__DIR__'gateway.log';
//file_put_contents($file,var_export($_REQUEST),1);
//file_put_contents(__DIR__'gateway.txt', $_REQUEST, 1);
# Required File Includes

include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "moolahgateway"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);

logTransaction($GATEWAY["name"], $_GET, "Successful"); # Save to Gateway Log: name, data array, status

if (!$GATEWAY["type"])
    die("Module Not Activated");# Checks gateway module is active before accepting callback
# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
$url_type = trim($_GET["url"]);
$status = trim($_GET["status"]);
$invoiceid = $_GET["invoiceid"];
$amount = $_GET["amount"];
$transid = $_GET["tx"];
$fee = '';
if ($transid == '' && $invoiceid != '') {
    header("location:" . $GATEWAY['systemurl'] . "/cart.php?a=complete");
}
$invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing
checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

if ($_GET['ipn_secret'] == $GATEWAY['secretkey']) {
    if ($status == 'complete') {
        # Successful
        addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
        //header("location:" . $GATEWAY['systemurl'] . "/cart.php?a=complete"); 			
    } else {
        logTransaction($GATEWAY["name"], $_REQUEST, "Error");
        //redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
        //header("location:" . $GATEWAY['systemurl'] . "/viewinvoice.php?id=" . $invoiceid . "");
    }
}
?>