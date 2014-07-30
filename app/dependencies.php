<?php
session_start();
include_once "restUtils.php";
if (!isset($_SESSION["clientPassword"])) {
    include_once "connect.php";
    //badRequest();
    //exit;
}
include_once "dbConn.php";
include_once "utils.php";
?>