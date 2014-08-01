<?php

include_once "dependencies.php";

$conn = new DataDBConn();
$aconn = new AdminDBConn();

$response = array();

$gst = $aconn->getCliPar("GST.Component");
$response["GST"] = ($gst ? $gst : 0);
$response["ClientID"] = $_SESSION["clientID"];

sendJSON(200,$response);

?>