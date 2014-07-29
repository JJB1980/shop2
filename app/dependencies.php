<?php
session_start();
include_once "restUtils.php";
if (!isset($_SESSION["clientPassword"])) {
    //header('Location: shop2');
    badRequest();
    exit;
}
include_once "dbConn.php";
include_once "utils.php";
?>