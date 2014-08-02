<?php

include_once "dependencies.php";

$custID = xs("customerID");
$action = xs("action");

if ($custID === "") {
    badRequest("No customer ID.");
    exit;
}
if ($action === "") {
    badRequest("No action.");
    exit;
}

switch ($action) {
    case "retrieve":
        retrieve($custID);
        break;
    case "update":
        update($custID);
        break;
    case "cancelOrder":
        cancelOrder(xs("orderID"));
        break;
    case "saveCart":
        saveInvoice();
        break;
}

function update($id) {
    $conn = new DataDBConn();
    $json = rawUrlDecode(xs("json"));
    if ($json === "") {
        badRequest("No Data.");
	return;
    }
    $obj = json_decode($json);
    if (!$obj) {
	badRequest("Unable to decode.");
	return;
    }
    $sql = "update Clients set FirstName = '".$obj->FirstName."',
        Surname = '".$obj->Surname."',
        Email = '".$obj->Email."',
        Password = '".$obj->Password."',
        AddressLine1 = '".$obj->AddressLine1."',
        AddressLine2 = '".$obj->AddressLine2."',
        AddressCity = '".$obj->AddressCity."',
        AddressPostCode = '".$obj->AddressPostCode."',
        AddressState = '".$obj->AddressState."',
        AddressCountry = '".$obj->AddressCountry."',
        PhoneNumber1 = '".$obj->PhoneNumber1."',
        MobileNumber = '".$obj->MobileNumber."',
        EnableUpdates = '".($obj->EnableUpdates ? true : false)."'
        where ID = ".$id;
	
    if (!$conn->query($sql)) {
        badRequest("Failed to update.");
    }

    $response = array( "status" => "200" , "message" => "Account Updated", "sql" => $sql, "json" => $json);
    sendJSON(200,$response);
}

function retrieve($id) {
    
    $sql = "select * from Clients where ID = ".$id;
    $conn = new DataDBConn();
    $conn->query($sql);
    
    $response = array();
    
    $account = array();
    $obj = $conn->obj();
    $account['FirstName'] = $obj->FirstName;
    $account['Surname'] = $obj->Surname;
    $account['Email'] = $obj->Email;
    $account['Password'] = $obj->Password;
    $account['AddressLine1'] = $obj->AddressLine1;
    $account['AddressLine2'] = $obj->AddressLine2;
    $account['AddressCity'] = $obj->AddressCity;
    $account['AddressPostCode'] = $obj->AddressPostCode;
    $account['AddressState'] = $obj->AddressState;
    $account['AddressCountry'] = $obj->AddressCountry;
    $account['EnableUpdates'] = ($obj->EnableUpdates ? true : false);
    $account['PhoneNumber1'] = $obj->PhoneNumber1;
    $account['MobileNumber'] = $obj->MobileNumber;
    $conn->free();
   
    $response["Account"] = $account;
    $response["Invoices"] = array();
    
    $sql = "select * from InvoiceHeader where Online = 'true' and  ClientID = ".$id;
    $conn->query($sql);
    while ($obj = $conn->obj()) {
        $invoice = array();
        $invID = $obj->ID;
        $invoice["ID"] = $invID;
        $invoice["InvoiceDate"] = foDate($obj->InvoiceDate);
        $invoice["InvoiceTime"] = $obj->InvoiceTime;
        $invoice["Total"] = $obj->Total;
        $invoice["GST"] = $obj->GST;
        $invoice["Cancelable"] = (invoiceIsOpen($invID,$conn) === "" ? true : false);
        array_push($response["Invoices"],$invoice);
    }
    
    $conn->free();
    sendJSON(200,$response);
    
}

function cancelOrder($orderID) {
	if ($orderID === "") {
                badRequest("No Order ID.");
                return;
	}
        $conn = new DataDBConn();
	$user = $_SESSION['shopUser'];
	$orderUser = $conn->val("select ClientID from InvoiceHeader where ID = ".$orderID,"ClientID");
	if ($user !== $orderUser) {
                badRequest("Order not available for this user.");
                return;
	}
	$test = invoiceIsOpen($orderID,$conn);
	if ($test !== "") {
		badRequest($test);
		return;
	}
	$cancelStatus = $conn->getAccPar("ECom.Status.Cancelled");
	if ($cancelStatus === "") {
		badRequest("Unable to cancel order.");
		return;
	}
	$sql = "update InvoiceHeader set StatusCode = '".$cancelStatus."' where ID = ".$orderID;
	$conn->query($sql);
	$json = array("status" => "200", "message" => "Order Cancelled");
        sendJSON(200,$json);
	return;
}

function invoiceIsOpen($invoiceID,&$conn) {
	$newStatus = $conn->getAccPar("ECom.Status.New");
	$statusCode = $conn->val("select StatusCode from InvoiceHeader where ID = ".$invoiceID,"StatusCode");
	if ($statusCode != $newStatus) {
		return $invoiceID."<error>Invalid order status.";
	}

	if ($conn->val("select DisableInvoiceModification from InvoiceStatus where StatusCode = '".$statusCode."'","DisableInvoiceModification") == "true") {
		return $invoiceID."<error>Unable to modify order.";
	}

	$test = $conn->getAccPar("ECom.DisableOrderCancelPaymentReceived");
	if ($conn->val("select PaymentReceived from InvoiceHeader where ID = ".$invoiceID,"PaymentReceived") != "no" && $test == "true") {
		return $invoiceID."<error>Payment Already Received";
	}
	return "";
}

function saveInvoice() {

    $json = rawUrlDecode(xs("json"));
    $inv = json_decode($json);

    $conn = new DataDBConn();
    $aconn = new AdminDBConn();
    
    $invoiceID = xs("invID");
    $shopUser = xs("customerID");
       
    if ($shopUser == "") {
	    badRequest("Not Logged In");
	    return;
    }
    
    if ($shopUser != $_SESSION['shopUser']) {
	    badRequest("Invalid User");
	    return;
    }

    $newStatus = $conn->getAccPar("ECom.Status.New");
	    
    if ($invoiceID == 0)
	    $invoiceID = createNew($newStatus,$shopUser,$conn);
    
    if ($invoiceID == 0)	{
	    badRequest("Unable to create invoice.");
	    return;
    }

    $test = invoiceIsOpen($invoiceID,$conn);
    if ($test !== "") {
	    badRequest($test);
	    return;
    }

    $sql = "delete from InvoiceLine where InvoiceHeader = ".$invoiceID;
    $conn->query($sql);

    $GST = $aconn->getCliPar("GST.component");
    $lineCount = count($inv);
    $totalAmount = 0; $totalGST = 0;
    $notation = "Order #".$invoiceID." for $";
    for ($i = 0; $i < $lineCount; $i++) {
	//$invCode = $inv[$i]->Code;
	$invID = $inv[$i]->ID; //$conn->val("select ID from Inventory where StoreCode = '".$invCode."'","ID");
	if ($invID != "" && $inv[$i]->qty > 0) {
	    $qty = $inv[$i]->qty;
	    $price = $inv[$i]->Price;
	    $total = $qty * $price;
	    $totGST = 0; 
	    $discount = applyDiscount($invID,$conn);			
	    if ($inv[$i]->ExcludeGST != "true" && $GST != "") {
		$totGST = $total * $GST;
	    }
	    $sql = "insert into InvoiceLine (InventoryID,InvoiceHeader,Quantity,Total,UnitPrice,GSTTotal,Discount) VALUES ";
	    $sql.="(".$invID.",".$invoiceID.",".$qty.",".nf($total).",".$price.",".nf($totGST).",".$discount.")";
	    $conn->query($sql);
	    $totalAmount += $total;
	    $totalGST += $totGST;
	}
    }

    $post = (float)$conn->val("select Amount from Postage where PostageCode = (select PostageCode from InvoiceHeader where ID = '".$invoiceID."')","Amount");

    $sql = "update InvoiceHeader set Total = ".nf($totalAmount).", GST = ".nf($totalGST)." where ID = ".$invoiceID;
    $conn->query($sql);
    
    $notAmt = $totalAmount+$post;
    $notation.=$notAmt;
    sendInvoiceEmail($notation,$shopUser,$invoiceID,$conn);	

    $response = array();
    $response["status"] = "ok";
    $response["message"] = "Checkout Complete. #".$invoiceID;
    sendJSON(200,$response);
}

function sendInvoiceEmail($msg,$custID,$invID,&$conn) {
	return;
	$msg = wordwrap($msg,70);
	$to = $conn->val("select Email from Clients where ID = ".$custID,"Email");
	//$to = "jjbowden1980@hotmail.com";
	$headers = "From: ".$conn->getAccPar("ECom.StoreEmail");
	// send email
	mail($to,"Invoice #".$invID,$msg,$headers);
}

function applyDiscount($invID,&$conn) {
	$discount = 0;
	$pct = $conn->val("select Percentage from Specials where InventoryID = ".$invID,"Percentage");
	if ($pct != "") {
		$price = $conn->val("select Price from Inventory where ID = ".$invID,"Price");
		$discount = $price * $pct;
		$discount = nf($discount);
	}
	return $discount;
}

function createNew($newStatus,$client,&$conn) {
	$post = xs("postage");
	$sql = "insert into InvoiceHeader (InvoiceDate,InvoiceTime,Viewed,StatusCode,ClientID,PaymentReceived,Online,PostageCode) 
										VALUES ('".dateFI()."','".timeFI()."','no','".$newStatus."',".$client.",'no','true','".$post."')";
	if ($conn->query($sql)) {
		$id = $conn->insertID();
	}
	return $id;
}

?>