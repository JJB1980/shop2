<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";
include_once "restUtils.php";

use SqlUtils as sql;
use RestUtils as rest;
use Utils as ut;

$query = ut\xs('q');
$page = ut\xs('page');
$searchType = ut\xs('searchType'); //  1 = search with query, 2 = specials, 3 = also viewed, 4 = Categories

$cli = $_SESSION['clientID'];
$imgServ = sql\aval("select ImageServer from ClientData where ID = ".$cli,"ImageServer");   
$imgFolder = sql\aval("select ImageFolder from ClientData where ID = ".$cli,"ImageFolder");   
$imgUrl = sql\aval("select ServerURL from ImageServer where ID = ".$imgServ,"ServerURL");
//$imgLoc = aval("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");
$imgLoc = ut\getImageDir();

if ($searchType == 2) {
    $sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
        from Inventory
        where EComDisabled <> 'true' and Inactive <> 'true'  and
        ID in (select InventoryID from Specials where ExpiryDate > CURDATE()) limit 0,49";
} else if ($searchType == 3) {
    $sql = viewItems(ut\xs('id'));
} else if ($searchType == 4) {
    $sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
		    from Inventory
		    where EComDisabled <> 'true' and Inactive <> 'true'  and
		    Category='".ut\xs('cat')."'";   
    if (ut\xs('subcat1') != "")
	    $sql.=" and SubCategory1='".ut\xs('subcat1')."'";   
    if (ut\xs('subcat2') != "")
	    $sql.=" and SubCategory2='".ut\xs('subcat2')."'";
} else {
    $sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
        from Inventory 
        where EComDisabled <> 'true' and Inactive <> 'true'
        and ID in (select RecordID from SearchIndex where TableName='Inventory' and";
    $sql.=getSearchValues($query).") order by ID asc";
}

$res = sql\dqry($sql);

$response = array();
if ($searchType == 2) {
    $response["Meta"] = "Specials";
} else if ($searchType == 3) {
    $response["Meta"] = "Also Viewed";
} else if ($searchType == 4) {
    $response["Meta"] = "Category Search";
} else {
    $response["Meta"] = "Search for '".$query."'";
}
$nrows = sql\nrows($res);
$response["ResultsCount"] = $nrows;
$npages=floor($nrows/$page);
if (($nrows % $page) > 0) {
	$npages++;
}
if ($npages==0) {
	$npages++;
}
$response["PageCount"] = $npages;
$response["Results"] = array();
$count = 0;

while ($r = sql\row($res)) {
	$row = array();
	$count++;
	$row["Ord"] = $count;
	$row["ID"] = $r["ID"];
	$row["Name"] = $r["Name"];
	$row["Description"] = $r["Description"];
	$row["Price"] = $r["Price"];
	$row["Available"] = $r["AvailableItems"];	
	$row["Code"] = $r["StoreCode"];	
	
	$isql = "select * from InventoryImage where InventoryID = ". $r["ID"]." order by ImageNo asc";
	$imgRes = sql\dqry($isql);
	if (sql\nrows($imgRes)) {	
		$ir = sql\row($imgRes);
		$file = $imgLoc . $GLOBALS['DIR'] . $imgFolder . $GLOBALS['DIR'] . $ir['FileName'];	
		if (file_exists($file)) {
			list($width, $height, $type, $attr) = getimagesize($file); 
			$aspect = $width / $height;
			$newWidth = 65;
			$newHeight = $newWidth / $aspect;		
			$img = $imgUrl . "/" . $imgFolder . "/" . $ir['FileName'];			
			//echo "<img width='".$newWidth."' height='".$newHeight."' src='".$img."?".rand(1000,10000000)."'></img>";
			$row["imgSrc"] = $img ."?" .rand(1000,10000000);
			$row["imgHeight"] = $newHeight;
			$row["imgWidth"] = $newWidth;
		}	
	} else {
			$row["imgSrc"] = "";
			$row["imgHeight"] = "";
			$row["imgWidth"] = "";
	}
	sql\free($imgRes);
			
	array_push($response["Results"],$row);
}

sql\free($res);

sql\closeConns();

rest\sendResponse(200,json_encode($response),'application/json');
//header('Content-Type: application/json');
//echo json_encode($response);

function getSearchValues($q) {
	$vals=explode(" ",$q); $cnt=count($vals);
	$sql="";
	for ($i=0;$i<$cnt;$i++) {
		if ($i>0) {
			$sql.=" and";
		}
		$sql.=" IndexValue like '%".$vals[$i]."%'";
	}
	return $sql;
}


function viewItems($id) {
		
	//include_once "search.php";

	$sql = "select InventoryItems from ShopClientLog 
				where InventoryItems like '%".$id.",%' 
				order by LogDate desc
				limit 0, 100";
	//logx($sql);
	$res = sql\dqry($sql); 

	$x = ""; $cnt = 0; $test="";
	while ($row = sql\row($res)) {
		$dat = $row['InventoryItems'];
		$arr = explode(",", $dat);
		$n = count($arr);
		for ($i=0; $i < $n; $i++) {
			if ($arr[$i] != $id && $arr[$i] != "") {
				if (isset($x[$arr[$i]])) {
					$x[$arr[$i]] = $x[$arr[$i]]+1;
				} else {
					$x[$arr[$i]] = 1;
				}	
				$cnt++;	
				$test.=$arr[$i].",";		 
			}
		}
	}	
	//logx($test);
	if ($cnt == 0)
		return;

	$keys = array_keys($x);
	$n = count($keys); $y = "";
	for ($i = 0; $i < $n; $i++) {
		if (isset($y[$x[$keys[$i]]]))
			$y[$x[$keys[$i]]] .= ","  .$keys[$i];
		else
			$y[$x[$keys[$i]]] = $keys[$i];
	}

	$keys = array_keys($y);
	$n = count($keys); 
	if ($n > 15)
		$n = 15;
	$ids = "";	
	for ($i = 0; $i < $n; $i++) {
		if ($ids != "")
			$ids.=",";
		$ids.=$y[$keys[$i]];
	}

	//echo "<div id='viewItems'>";
	//echo "<p>Items also viewed. ";	
	
	$sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems 
			from Inventory 
			where EComDisabled <> 'true' and Inactive <> 'true'
			 and ID in (".$ids.") order by ID asc";	
	//logx($sql);
	//searchRun($sql);	
	return $sql;
	//echo "</div>";
}

?>