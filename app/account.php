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

?>