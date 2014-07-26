<?php

include_once "dbConn.php";
include_once "utils.php";
include_once "restUtils.php";
session_start();

//use RestUtils as rest;
//use Utils as ut;

$conn = new DataDBConn();

$response = array();

if (xs('getClient') == 1) {
    $response['client'] = $_SESSION['clientID'];
} else if (xs('logout') == 1) {
    doLogout($response,$conn);
} else if (xs('autoLogin') == 1) {
    autoLogin($response,$conn);
} else {
    doLogin($response,$conn);
}

sendJSON(200,$response);

function autoLogin(&$response,&$conn) {
	$id = xs("id");
	$tok = xs("tok");
	if ($id == "" || $tok == "") {
                $response['status'] = "error";
                $response['message'] = "No ID or Token.";
		//echo "<error>";
		return;
	}		
	$cliTok = $conn->val("select LoginToken from Clients where ID = ".$id,"LoginToken");
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

function doLogin(&$response,&$conn) {
	$sql = "select Password from Clients where Email='".xs('email')."'";
	$pw = $conn->val($sql,"Password");
	if ($pw === "") {
                $response['status'] = "error";
                $response['message'] = "Invalid Email.";
		//echo "<error>Invalid Email.";
		return;
	}
	if ($pw !== xs('password')) {
                $response['status'] = "error";
                $response['message'] = "Invalid Password.";
		// "<error>Invalid Password";
		return;
	}
	$sql = "select ID from Clients where Email='".xs('email')."'";
	$id = $conn->val($sql,"ID");
	$_SESSION['shopUser'] = $id;
	$token = rand(100000,999999); //$_REQUEST['tok'];
	$sql = "update Clients set LoginToken = '".$token."' where ID = ".$id;
	$conn->query($sql);
        $response['status'] = "ok";
        $response['message'] = "Success.";
        $response['ID'] = $id;
        $response['token'] = $token;
        $response['client'] = $_SESSION['clientID'];
	//echo "<ok>".$id;
}

function doLogout(&$response,&$conn) {
	$id = $_SESSION['shopUser'];
	$_SESSION['shopUser'] = "";
	$sql = "update Clients set LoginToken = '' where ID = ".$id;
	$conn->query($sql);
        $response['status'] = "ok";
        $response['message'] = "Logged Out.";
        $response['client'] = $_SESSION['clientID'];
	//echo "Logged Out.";
}

?>