<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "restUtils.php";

use SqlUtils as sql;
use RestUtils as rest;

$post = array();

$post["message"] = sql\getAccPar("ECom.Contact");

sql\closeConns();

rest\sendResponse(200,json_encode($post),'application/json');
//header('Content-Type: application/json');
//echo json_encode($post);

?>