<?php

include_once("dependencies.php");

//use RestUtils as rest;

$post = array();

$conn = new DataDBConn();

$post["message"] = $conn->getAccPar("ECom.Home");

sendJSON(200,$post);

?>