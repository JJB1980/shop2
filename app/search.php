<?php

include_once "dbConn.php";
include_once "utils.php";
include_once "restUtils.php";
session_start();

//use RestUtils as rest;
//use Utils as ut;

$aconn = new AdminDBConn();
$conn = new DataDBConn();

$query = xs('q');
$page = xs('page');
$searchType = xs('searchType'); //  1 = search with query, 2 = specials, 3 = also viewed, 4 = Categories

//$vals = [ 1 => "a" , 2 => "b" , 3 => "c" , 4 => "d" ];
//if (!array_key_exists($searchType,$vals)) {
if ($searchType <= 0 || $searchType > 4) { 
    badRequest();
    exit;
}

$cli = $_SESSION['clientID'];
$imgServ = $aconn->val("select ImageServer from ClientData where ID = ".$cli,"ImageServer");   
$imgFolder = $aconn->val("select ImageFolder from ClientData where ID = ".$cli,"ImageFolder");   
$imgUrl = $aconn->val("select ServerURL from ImageServer where ID = ".$imgServ,"ServerURL");
//$imgLoc = aval("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");
$imgLoc = $aconn->getImageDir();

$response = array();

switch ($searchType) {
    case 2:
	$response["Meta"] = "Specials";
	$sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
	    from Inventory
	    where EComDisabled <> 'true' and Inactive <> 'true'  and
	    ID in (select InventoryID from Specials where ExpiryDate > CURDATE()) limit 0,49";
	break;
    case 3:
	$response["Meta"] = "Also Viewed";
	$sql = viewItems(xs('id'),$conn);
	break;
    case 4:
	$response["Meta"] = "Category Search";
	$sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
			from Inventory
			where EComDisabled <> 'true' and Inactive <> 'true'  and
			Category='".xs('cat')."'";   
	if (xs('subcat1') != "")
		$sql.=" and SubCategory1='".xs('subcat1')."'";   
	if (xs('subcat2') != "")
		$sql.=" and SubCategory2='".xs('subcat2')."'";
	break;
    default:
	$response["Meta"] = "Search for '".$query."'";
	$sql = "select StoreCode,ID,Name,Description,Price,Manufacturer,ExcludeGST,AvailableItems
	    from Inventory 
	    where EComDisabled <> 'true' and Inactive <> 'true'
	    and ID in (select RecordID from SearchIndex where TableName='Inventory' and";
	$sql.=getSearchValues($query).") order by ID asc";
}

$conn->query($sql);

$nrows = $conn->rowCount();
$response["ResultsCount"] = $nrows;
if ($page === "" || $page === 0)
    $page = 5;
$npages=floor($nrows/$page);
if (($nrows % $page) > 0) {
	$npages++;
}
if ($npages===0) {
	$npages++;
}
$response["PageCount"] = $npages;
$response["Results"] = array();
$count = 0;

while ($r = $conn->row()) {
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
	$imgRes = $conn->queryGet($isql);
	if (DBConn::rowCountGet($imgRes)) {	
		$ir = DBConn::rowGet($imgRes);
		$file = $imgLoc . $_SESSION['DIR'] . $imgFolder . $_SESSION['DIR'] . $ir['FileName'];	
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
	DBConn::freeRS($imgRes);
			
	array_push($response["Results"],$row);
}

$conn->free();

sendJSON(200,$response);

// return individual search values from query string.
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

// return sql for also viewed items search.
function viewItems($id,&$conn) {
		
	//include_once "search.php";

	$sql = "select InventoryItems from ShopClientLog 
				where InventoryItems like '%".$id.",%' 
				order by LogDate desc
				limit 0, 100";
	//logx($sql);
	$res = $conn->query($sql); 

	$countByID = ""; $cnt = 0; $test="";
	while ($row = $conn->row($res)) {
		$dat = $row['InventoryItems'];
		$arr = explode(",", $dat);
		$n = count($arr);
		for ($i=0; $i < $n; $i++) {
			if ($arr[$i] !== $id && $arr[$i] !== "") {
				if (isset($countByID[$arr[$i]])) {
					$countByID[$arr[$i]]++;
				} else {
					$countByID[$arr[$i]] = 1;
				}	
				$cnt++;	
				$test.=$arr[$i].",";		 
			}
		}
	}
	
	$conn->free();
	
	//logx($test);
	if ($cnt === 0)
		return "";

	$keys = array_keys($countByID);
	$n = count($keys); $sortByCount = "";
	for ($i = 0; $i < $n; $i++) {
		if (isset($sortByCount[$countByID[$keys[$i]]]))
			$sortByCount[$countByID[$keys[$i]]] .= ","  .$keys[$i];
		else
			$sortByCount[$countByID[$keys[$i]]] = $keys[$i];
	}

	$keys = array_keys($sortByCount);
	$n = count($keys);
	$limit = 15;
	if ($n > $limit)
		$n = $limit;
	$ids = ""; $idCount = 0;
	for ($i = 0; $i < $n; $i++) {
		if ($ids != "")
			$ids.=",";
		$ids.=$sortByCount[$keys[$i]];
		if (count(explode(",",$ids)) >= $limit)
		    break;
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