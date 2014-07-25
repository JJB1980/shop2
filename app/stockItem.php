<?php

	
include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";
include_once "restUtils.php";

use SqlUtils as sql;
use RestUtils as rest;
use Utils as ut;

$id = ut\xs('id');

if ($id == "")
	exit;	

$sql = "select * from Inventory where ID = ".$id;

$res = sql\dqry($sql);

$cli = $_SESSION['clientID'];
$imgServ = sql\aval("select ImageServer from ClientData where ID = ".$cli,"ImageServer");   
$imgFolder = sql\aval("select ImageFolder from ClientData where ID = ".$cli,"ImageFolder");   
$imgUrl = sql\aval("select ServerURL from ImageServer where ID = ".$imgServ,"ServerURL");
$imgLoc = ut\getImageDir(); //sqlAVal("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");

//$response["StockItem"] = array();
$r = sql\row($res);
$response = array();
$response["ID"] = $r["ID"];
$response["Name"] = $r["Name"];
$response["Description"] = $r["Description"];
$response["Price"] = $r["Price"];
$response["Available"] = $r["AvailableItems"];	
$response["Code"] = $r["StoreCode"];	
$mf = sql\dval("select ManufacturerName from Manufacturers where ManufacturerCode='".$r['Manufacturer']."'","ManufacturerName");
$response["Manufacturer"] = $mf;
$sz = sql\dval("select SizeName from Sizes where SizeCode='".$r['Size']."'","SizeName");	
$response["Size"] = $sz;
$response["Colour"] = $r["Colour"];
$response["Weight"] = $r["Weight"];

$response["Images"] = array();	

$isql = "select * from InventoryImage where InventoryID = ".$id." order by ImageNo asc";
$ires = sql\dqry($isql); $i=0;

while ($ir = sql\row($ires)) {
	$i++;
	$file = $imgLoc . $GLOBALS['DIR'] . $imgFolder . $GLOBALS['DIR'] .  $ir['FileName'];
	if (file_exists($file)) {
		$image = array();
		list($width, $height, $type, $attr) = getimagesize($file); 		
/*
		$aspect = $width / $height;
		$newWidth = 60;
		$newHeight = $newWidth / $aspect;
*/
		$aspect = $height / $width;
		$newHeight = 500;
		$newWidth = $newHeight / $aspect;
					
		$img = $imgUrl . "/" . $imgFolder . "/" . $ir['FileName'];		
		
		$image["imgSrc"] = $img ."?" .rand(1000,10000000);
		$image["imgHeight"] = $newHeight;
		$image["imgWidth"] = $newWidth;

		array_push($response["Images"],$image);				 
	}
}	


//array_push($response["Stock"],$response);

sql\free($res);

sql\closeConns();

rest\sendResponse(200,json_encode($response),'application/json');
//header('Content-Type: application/json');
//echo json_encode($response);

?>