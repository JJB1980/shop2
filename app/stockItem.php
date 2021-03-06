<?php

include_once("dependencies.php");

//use RestUtils as rest;
//use Utils as ut;

$id = xs('id');

if ($id == "") {
	badRequest();
	exit;	
}

$conn = new DataDBConn();
$aconn = new AdminDBConn();

$sql = "select * from Inventory where ID = ".$id;

$conn->query($sql);

$cli = $_SESSION['clientID'];
$imgServ = $aconn->val("select ImageServer from ClientData where ID = ".$cli,"ImageServer");   
$imgFolder = $aconn->val("select ImageFolder from ClientData where ID = ".$cli,"ImageFolder");   
$imgUrl = $aconn->val("select ServerURL from ImageServer where ID = ".$imgServ,"ServerURL");
$imgLoc = $aconn->getImageDir(); //sqlAVal("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");

//$response["StockItem"] = array();
$r = $conn->row();
$response = array();
$response["ID"] = $r["ID"];
$response["Name"] = $r["Name"];
$response["Description"] = $r["Description"];
$response["Price"] = $r["Price"];
$response["AvailableItems"] = $r["AvailableItems"];	
$response["StoreCode"] = $r["StoreCode"];	
$mf = $conn->val("select ManufacturerName from Manufacturers where ManufacturerCode='".$r['Manufacturer']."'","ManufacturerName");
$response["Manufacturer"] = $mf;
$sz = $conn->val("select SizeName from Sizes where SizeCode='".$r['Size']."'","SizeName");	
$response["Size"] = $sz;
$response["Colour"] = $r["Colour"];
$response["Weight"] = $r["Weight"];

$conn->free();

$response["Images"] = array();	

$isql = "select * from InventoryImage where InventoryID = ".$id." order by ImageNo asc";
$conn->query($isql); $i=0;

while ($ir = $conn->row()) {
	$i++;
	$file = $imgLoc . $_SESSION['DIR'] . $imgFolder . $_SESSION['DIR'] .  $ir['FileName'];
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
		
		$image["imgSrc"] = $img; // ."?" .rand(1000,10000000);
		$image["imgHeight"] = $newHeight;
		$image["imgWidth"] = $newWidth;
		/*
		if ($i === 1) {
			list($width, $height, $type, $attr) = getimagesize($file); 
			$aspect = $width / $height;
			$newWidth = 65;
			$newHeight = $newWidth / $aspect;		
			$response["imgSrc"] = $img; // ."?" .rand(1000,10000000);
			$response["imgHeight"] = $newHeight;
			$response["imgWidth"] = $newWidth;
		}
		*/
		array_push($response["Images"],$image);				 
	}
}	


//array_push($response["Stock"],$response);

$conn->free();

sendJSON(200,$response);

?>