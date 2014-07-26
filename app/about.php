<?php

include_once "dbConn.php";
include_once "restUtils.php";
session_start();

$post = array();

$conn = new DataDBConn();

$post["message"] = $conn->getAccPar("ECom.About");

sendJSON(200,$post);

?>