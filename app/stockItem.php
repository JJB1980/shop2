<?php

	
include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";
include_once "restUtils.php";

$id = xs('id');

if ($id == "")
	exit;	

$sql = "select * from Inventory where ID = ".$id;

$res = dqry($sql);

$cli = $_SESSION['clientID'];
$imgServ = sqlAVal("select ImageServer from ClientData where ID = ".$cli,"ImageServer");   
$imgFolder = sqlAVal("select ImageFolder from ClientData where ID = ".$cli,"ImageFolder");   
$imgUrl = sqlAVal("select ServerURL from ImageServer where ID = ".$imgServ,"ServerURL");
$imgLoc = getImageDir(); //sqlAVal("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");

//$response["StockItem"] = array();
$r = row($res);
$post = array();
$post["ID"] = $r["ID"];
$post["Name"] = $r["Name"];
$post["Description"] = $r["Description"];
$post["Price"] = $r["Price"];
$post["Available"] = $r["AvailableItems"];	
$post["Code"] = $r["StoreCode"];	
$mf = sqlVal("select ManufacturerName from Manufacturers where ManufacturerCode='".$r['Manufacturer']."'","ManufacturerName");
$post["Manufacturer"] = $mf;
$sz = sqlVal("select SizeName from Sizes where SizeCode='".$r['Size']."'","SizeName");	
$post["Size"] = $sz;
$post["Colour"] = $r["Colour"];
$post["Weight"] = $r["Weight"];

$post["Images"] = array();	

$isql = "select * from InventoryImage where InventoryID = ".$id." order by ImageNo asc";
$ires = dqry($isql); $i=0;

while ($ir = row($ires)) {
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

		array_push($post["Images"],$image);				 
	}
}	


//array_push($response["Stock"],$post);

free($res);

closeConns();

sendResponse(200,json_encode($post),'application/json');
//header('Content-Type: application/json');
//echo json_encode($post);

?>