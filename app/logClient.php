<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";

$action = "";

if (isset($_REQUEST['action']))
	$action = $_REQUEST['action'];
	
if ($action == "doit") {
	logClient();
}

if ($action == "logView") {
	logView();
}

function logView() {

	if (isset($_REQUEST['ref']))
		$ref = $_REQUEST['ref'];

	if (isset($_REQUEST['id']))
		$id = $_REQUEST['id'];	
	
	$ids = sqlVal("select InventoryItems from ShopClientLog where ID = ".$ref,"InventoryItems");
	
	if (strpos($ids,$id) !== false)	
		return;
		
	//if ($ids != "")
	//	$ids.=",";
	$ids.=$id.",";
	
	$sql = "update ShopClientLog set InventoryItems='".$ids."' where ID = ".$ref;	
	runSql($sql);
			
}

function logClient() {

	if (isset($_REQUEST['ref']))
		$ref = $_REQUEST['ref'];
		
	if ($ref == "") {
		$sql = "insert into ShopClientLog (LogDate,IPAddress) values ('".dateFI()."','".$_SERVER['REMOTE_ADDR']."')";	
		runSql($sql);
		$ref = mysqli_insert_id($GLOBALS['dcon']);
	} else {
		$sql = "update ShopClientLog set LogDate='".dateFI()."', IPAddress='".$_SERVER['REMOTE_ADDR']."' where ID = ".$ref;	
		runSql($sql);
	}
		
	echo $ref;
}

?>