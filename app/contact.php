<?php

include_once("dependencies.php");

//use RestUtils as rest;

$post = array();

$conn = new DataDBConn();

$post["message"] = $conn->getAccPar("ECom.Contact");

sendJSON(200,$post);

?>