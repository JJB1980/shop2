<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";

$post = array();

$post["message"] = getAccPar("ECom.About");

header('Content-Type: application/json');
echo json_encode($post);

?>