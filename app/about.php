<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";
include_once "restUtils.php";

$post = array();

$post["message"] = getAccPar("ECom.About");

closeConns();

sendResponse(200,json_encode($post),'application/json');
//header('Content-Type: application/json');
//echo json_encode($post);

?>