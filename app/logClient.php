<?php

include_once("dependencies.php");

//use Utils as ut;

$conn = new DataDBConn();

$action = "";

$action = xs('action');
	
if ($action == "doit") {
	logClient($conn);
}

if ($action == "logView") {
	logView($conn);
}


function logView(&$conn) {

	if (isset($_REQUEST['ref']))
		$ref = $_REQUEST['ref'];

	if (isset($_REQUEST['id']))
		$id = $_REQUEST['id'];	
	
	$ids = $conn->val("select InventoryItems from ShopClientLog where ID = ".$ref,"InventoryItems");
	
	if (strpos($ids,$id) !== false)	
		return;
		
	//if ($ids != "")
	//	$ids.=",";
	$ids.=$id.",";
	
	$sql = "update ShopClientLog set InventoryItems='".$ids."' where ID = ".$ref;	
	$conn->query($sql);
			
}

function logClient(&$conn) {

	$ref = xs('ref');
		
	if ($ref == "") {
		$sql = "insert into ShopClientLog (LogDate,IPAddress) values ('".dateFI()."','".$_SERVER['REMOTE_ADDR']."')";	
		$conn->query($sql);
		$ref = $conn->insertID();
	} else {
		$sql = "update ShopClientLog set LogDate='".dateFI()."', IPAddress='".$_SERVER['REMOTE_ADDR']."' where ID = ".$ref;	
		$conn->query($sql);
	}
		
	echo $ref;
}

?>