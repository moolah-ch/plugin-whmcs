<?php

function moolahgateway_config() {
    $configarray = array(
        "FriendlyName" => array("Type" => "System", "Value" => "Moolah Gateway"),
        "apikey" => array("FriendlyName" => "Apikey", "Type" => "text", "Size" => "20",),
        "secretkey" => array("FriendlyName" => "Secret key", "Type" => "text", "Size" => "20",),
        "invoice_perfix" => array("FriendlyName" => "Invoiceperfix", "Type" => "text", "Size" => "20",),
        "Bitcoin" => array("FriendlyName" => "Bitcoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Litecoin" => array("FriendlyName" => "Litecoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Dogecoin" => array("FriendlyName" => "Dogecoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Vertcoin" => array("FriendlyName" => "Vertcoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Auroracoin" => array("FriendlyName" => "Auroracoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Mintcoin" => array("FriendlyName" => "Mintcoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Darkcoin" => array("FriendlyName" => "Darkcoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "Maxcoin" => array("FriendlyName" => "Maxcoin", "Type" => "text", "Size" => "20", "Description" => "GUID"),
        "testmode" => array("FriendlyName" => "Test Mode", "Type" => "yesno", "Description" => "Tick this to test",),
    );
    return $configarray;
}

function moolahgateway_link($params) {

    if ($_POST['select_guid'] != 'yes') {
        $code = '<form method="post" action="">
			Select Coin <select name="guid">';
		
		if($params['Bitcoin'] != '') { $code .= '<option value="' . $params['Bitcoin'] . '">Bitcoin</option>';
		if($params['Litecoin'] != '') { $code .= '<option value="' . $params['Litecoin'] . '">Litecoin</option>';
		if($params['Dogecoin'] != '') { $code .= '<option value="' . $params['Dogecoin'] . '">Dogecoin</option>';
		if($params['Vertcoin'] != '') { $code .= '<option value="' . $params['Vertcoin'] . '">Vertcoin</option>';
		if($params['Auroracoin'] != '') { $code .= '<option value="' . $params['Auroracoin'] . '">Auroracoin</option>';
		if($params['Mintcoin'] != '') { $code .= '<option value="' . $params['Mintcoin'] . '">Mintcoin</option>';
		if($params['Darkcoin'] != '') { $code .= '<option value="' . $params['Darkcoin'] . '">Darkcoin</option>';
		if($params['Maxcoin'] != '') { $code .= '<option value="' . $params['Maxcoin'] . '">Maxcoin</option>';
		
		$code .= '</select>
	<input type="hidden" name="select_guid" value="yes">
	<input type="submit"  name="submit" value="Submit"/>
	</form><style type="text/css"> .alert{ display:none; } 	.textcenter img{ display:none; } </style>';
        return $code;
    } else {
        # Gateway Specific Variables
        $gatewayusername = $params['username'];
        $gatewaytestmode = $params['testmode'];

        # Invoice Variables
        $invoiceid = $params['invoiceid'];
        $description = $params["description"];
        $amount = $params['amount']; # Format: ##.##
        $currency = $params['currency']; # Currency Code
        # Client Variables
        $firstname = $params['clientdetails']['firstname'];
        $lastname = $params['clientdetails']['lastname'];
        $email = $params['clientdetails']['email'];
        $address1 = $params['clientdetails']['address1'];
        $address2 = $params['clientdetails']['address2'];
        $city = $params['clientdetails']['city'];
        $state = $params['clientdetails']['state'];
        $postcode = $params['clientdetails']['postcode'];
        $country = $params['clientdetails']['country'];
        $phone = $params['clientdetails']['phonenumber'];

        # System Variables
        $companyname = $params['companyname'];
        $systemurl = $params['systemurl'];
        $currency = $params['currency'];
        # Enter your code submit to the gateway...

        global $smarty;
        #print_r($smarty);
        #print_r();exit;
        if ($smarty->_tpl_vars['filename'] == 'viewinvoice') {
            $R_url = $params['systemurl'] . '/viewinvoice.php?id=' . $invoiceid;
        } else {
            $R_url = $params['systemurl'] . '/cart.php?a=complete';
        }
        $R_url01 = $params['systemurl'] . '/modules/gateways/callback/moolahgateway.php?invoiceid=' . $invoiceid . '&amount=' . $amount;
        $moolah_adr = "https://moolah.io/api/pay";
        $query = 'product=&amount=' . $amount . '&currency=' . $currency . '&guid=' . $_POST['guid'] . '&return=' . $R_url . '&ipn=' . $R_url01 . '';
        $query_secret = $query . '&secret=' . $params['secretkey'];
        $hash = hash('sha256', $query_secret);
        $query .= '&hash=' . $hash;
        // Execute query.
        $result = file_get_contents($moolah_adr . '?' . $query);
        $response_data = json_decode($result);
        // echo '<pre>'; print_r($response_data); exit();
        $code = '<form method="post" action="' . $response_data->url . '">		
			<input type="submit" value="Pay Now" />
		</form>';
        return $code;
    }
}

?>