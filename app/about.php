<?php

include_once("dependencies.php");

$post = array();

$conn = new DataDBConn();

$post["message"] = $conn->getAccPar("ECom.About");

sendJSON(200,$post);

?>