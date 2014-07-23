<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";
include_once "restUtils.php";

$response = array();

if (xs('getClient') == 1) {
    $response['client'] = $_SESSION['clientID'];
} else if (xs('logout') == 1) {
    doLogout($response);
} else if (xs('autoLogin') == 1) {
    autoLogin($response);
} else {
    doLogin($response);
}

closeConns();

sendResponse(200,json_encode($response),'application/json');
//header('Content-Type: application/json');
//echo json_encode($response);

function autoLogin(&$response) {
	$id = xs("id");
	$tok = xs("tok");
	if ($id == "" || $tok == "") {
                $response['status'] = "error";
                $response['message'] = "No ID or Token.";
		//echo "<error>";
		return;
	}		
	$cliTok = sqlVal("select LoginToken from Clients where ID = ".$id,"LoginToken");
	if ($cliTok == $tok) {
		$_SESSION['shopUser'] = $id;		
                $response['status'] = "ok";
                $response['message'] = "Success.";
                $response['ID'] = $id;
                $response['client'] = $_SESSION['clientID'];
		//echo "<ok>".$id;
	} else {
                $response['status'] = "error";
                $response['message'] = "Token does not match.";
		//echo "<error>";
	}
}

function doLogin(&$response) {
	$sql = "select Password from Clients where Email='".xs('email')."'";
	$pw = sqlVal($sql,"Password");
	if ($pw == "") {
                $response['status'] = "error";
                $response['message'] = "Invalid Email.";
		//echo "<error>Invalid Email.";
		return;
	}
	if ($pw != xs('password')) {
                $response['status'] = "error";
                $response['message'] = "Invalid Password.";
		// "<error>Invalid Password";
		return;
	}
	$sql = "select ID from Clients where Email='".xs('email')."'";
	$id = sqlVal($sql,"ID");
	$_SESSION['shopUser'] = $id;
	$token = rand(100000,999999); //$_REQUEST['tok'];
	$sql = "update Clients set LoginToken = '".$token."' where ID = ".$id;
	runSQL($sql);
        $response['status'] = "ok";
        $response['message'] = "Success.";
        $response['ID'] = $id;
        $response['token'] = $token;
        $response['client'] = $_SESSION['clientID'];
	//echo "<ok>".$id;
}

function doLogout(&$response) {
	$id = $_SESSION['shopUser'];
	$_SESSION['shopUser'] = "";
	$sql = "update Clients set LoginToken = '' where ID = ".$id;
        $response['status'] = "ok";
        $response['message'] = "Logged Out.";
        $response['client'] = $_SESSION['clientID'];
	//echo "Logged Out.";
}

?>