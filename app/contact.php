<?php

include_once "dbConn.php";
include_once "restUtils.php";
session_start();

//use RestUtils as rest;

$post = array();

$conn = new DataDBConn();

$post["message"] = $conn->getAccPar("ECom.Contact");

sendJSON(200,$post);

?>