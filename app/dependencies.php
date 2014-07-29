<?php
session_start();
include_once "restUtils.php";
if (!isset($_SESSION["clientPassword"])) {
    if (!isset($_REQUEST['client'])) {
      $_REQUEST['client'] = 1;
    }
    include_once "connect.php";
    //badRequest();
    //exit;
}
include_once "dbConn.php";
include_once "utils.php";
?>