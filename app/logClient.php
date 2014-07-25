<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";

use SqlUtils as sql;
use Utils as ut;

$action = "";

$action = ut\xs['action'];
	
if ($action == "doit") {
	logClient();
}

if ($action == "logView") {
	logView();
}

sql\closeConns();

function logView() {

	if (isset($_REQUEST['ref']))
		$ref = $_REQUEST['ref'];

	if (isset($_REQUEST['id']))
		$id = $_REQUEST['id'];	
	
	$ids = sql\dval("select InventoryItems from ShopClientLog where ID = ".$ref,"InventoryItems");
	
	if (strpos($ids,$id) !== false)	
		return;
		
	//if ($ids != "")
	//	$ids.=",";
	$ids.=$id.",";
	
	$sql = "update ShopClientLog set InventoryItems='".$ids."' where ID = ".$ref;	
	sql\dSQL($sql);
			
}

function logClient() {

	$ref = ut\xs['ref'];
		
	if ($ref == "") {
		$sql = "insert into ShopClientLog (LogDate,IPAddress) values ('".sql\dateFI()."','".$_SERVER['REMOTE_ADDR']."')";	
		sql\dSQL($sql);
		$ref = sql\dInsID();
	} else {
		$sql = "update ShopClientLog set LogDate='".sql\dateFI()."', IPAddress='".$_SERVER['REMOTE_ADDR']."' where ID = ".$ref;	
		sql\dSQL($sql);
	}
		
	echo $ref;
}

?>