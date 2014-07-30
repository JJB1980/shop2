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
    $json = xs("json");
    if ($json === "") {
        badRequest("No Data.");
    }
    $obj = json_decode($json);
    $sql = "update Clients set FirstName = '".$obj->FirstName."',
        Surname = '".$obj->Surname."',
        Email = '".$obj->Email."',
        Password = '".$obj->Password."'
        where ID = ".$id;
    if (!$conn->query($sql)) {
        badRequest("Failed to update.");
    }
    $response = array( "status" => "200" , "message" => "Account Updated", "sql" => $sql);
    sendJSON(200,$response);
}

function retrieve($id) {
    
    $sql = "select * from Clients where ID = ".$id;
    $conn = new DataDBConn();
    $conn->query($sql);
    
    $response = array();
    
    $account = array();
    $row = $conn->row();
    $account['FirstName'] = $row['FirstName'];
    $account['Surname'] = $row['Surname'];
    $account['Email'] = $row['Email'];
    $account['Password'] = $row['Password'];
    $account['AddressLine1'] = $row['AddressLine1'];
    $account['AddressLine2'] = $row['AddressLine2'];
    $account['AddressCity'] = $row['AddressCity'];
    $account['AddressPostCode'] = $row['AddressPostCode'];
    $account['AddressState'] = $row['AddressState'];
    $account['AddressCountry'] = $row['AddressCountry'];
    $account['EnableUpdates'] = $row['EnableUpdates'];
    $conn->free();
   
    $response["Account"] = $account;
    $response["Invoices"] = array();
    
    $sql = "select * from InvoiceHeader where Online = 'true' and  ClientID = ".$id;
    $conn->query($sql);
    while ($row = $conn->row()) {
        $invoice = array();
        $invID = $row["ID"];
        $invoice["ID"] = $invID;
        $invoice["InvoiceDate"] = foDate($row["InvoiceDate"]);
        $invoice["InvoiceTime"] = $row["InvoiceTime"];
        $invoice["Total"] = $row["Total"];
        $invoice["GST"] = $row["GST"];
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