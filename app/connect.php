<?php

//session_cache_limiter('private');
//session_cache_expire(10);
//register_shutdown_function('shutdown');

session_start();

include_once "sqlUtils.php";

use SqlUtils as sql;

$GLOBALS['DEBUG']=0;

//$adminLoc="192.168.2.2";
$adminLoc="localhost";
//$adminLoc="192.168.2.4";
//$adminLoc="localhost";

//$adminUser="root";
//$adminPassword="";


$adminUser="ADMINSQLUSR";
$adminPassword="rty654";
$adminData="adminData";

/*
$adminUser="jjbsw_root";
$adminPassword="fender71";
$adminData="jjbswcom_admin";
*/

$servName = $_SERVER['SERVER_NAME'];

$tmp = explode("/",$_SERVER['PHP_SELF']);
$GLOBALS['location'] = $tmp[1];
$GLOBALS['shoproot'] = "/shop/";

$dir="\\";
//$GLOBALS['_UPLOAD']="\\\\JOHNPC\\upload\\";
//$GLOBALS['_UPLOAD']="upload\\";
$GLOBALS['_UPLOAD']="C:\\wamp\\www\\".$GLOBALS['location']."\\temp\\";
if (isset($_SESSION['ServerType']) && $_SESSION['ServerType'] == "linux") {
	$dir="/";
	//$GLOBALS['_UPLOAD']="smb://JOHNPC/upload/";
	//$GLOBALS['_UPLOAD']="upload/";
	$GLOBALS['_UPLOAD']="/var/www/".$GLOBALS['location']."/temp/";
}
$GLOBALS['DIR']=$dir;

$svrRoot="/".$GLOBALS['location']."/";

$GLOBALS['serverRoot']=$svrRoot;

$GLOBALS['baseUrl']="https://";
//$GLOBALS['baseUrl']="http://";

$GLOBALS['serverName']=$GLOBALS['baseUrl'].$_SERVER['SERVER_NAME'].$svrRoot;

// connect to databases.
$GLOBALS['acon']=mysqli_connect($adminLoc,$adminUser,$adminPassword,$adminData);
$GLOBALS['dcon']="";

	// Check connection
if (mysqli_connect_errno()) {
  	$err = "Failed to connect to MySQL: " . mysqli_connect_error();
  	die("error");
}

if (!isset($_SESSION['ServerType'])) {
	$_SESSION['ServerType'] = sql\aval("select ServerType from AppServer where IPAddress='{$servName}' or ServerName='{$servName}' and Status='shop.live'","ServerType");
	//echo $_SESSION['ServerType'];
}

if (isset($_REQUEST['client'])) {
	//echo "Client=".$_REQUEST['client'];
	$sql = "select a.ClientName,a.ImageFolder,c.ServerURL,a.DatabaseName,a.SQLUser,a.SQLPassword,b.IPAddress 
						from ClientData a, DataServer b, ImageServer c
						where a.ID=".$_REQUEST['client']." and a.DataLocation=b.ID and a.ImageServer = c.ID";
	//echo $sql;
	$res = sql\aqry($sql);
	$row = sql\row($res);
	$_SESSION['clientID']=$_REQUEST['client'];
	if ($row) {
		$_SESSION['ClientName']=$row['ClientName'];
		$_SESSION['ImageFolder']=$row['ImageFolder'];
		$_SESSION['ImagesURL']=$row['ServerURL'];						
		
		$_SESSION['dataLocation']=$row['IPAddress'];
		$_SESSION['clientUser']=$row['SQLUser'];
		$_SESSION['password']=$row['SQLPassword'];
		$_SESSION['dataName']=$row['DatabaseName'];
	}
}

if(isset($_SESSION['clientUser'])) {
	$GLOBALS['dcon']=mysqli_connect($_SESSION['dataLocation'],$_SESSION['clientUser'],$_SESSION['password'],$_SESSION['dataName']);
} else {
	//no client supplied...
	header('Location: https://www.google.com');
	exit;
}
	// Check connection
if (mysqli_connect_errno()) {
  	$err = "Failed to connect to MySQL: " . mysqli_connect_error();
  	die("error");
}

// set timezone
if (isset($_SESSION['clientID'])) {
	$tz = sql\getCliPar("default.Timezone");
	if ($tz != "")
		date_default_timezone_set($tz);
	else
		date_default_timezone_set("Australia/Brisbane");
} else {  
	date_default_timezone_set("Australia/Brisbane");
}

$GLOBALS['RestrictedTables'] = "'UsersA','UserProfile','AppServer','DataServer','ImageServer','FileServer','ClientData'";
if (isset($_SESSION['clientID'])) {
	if (sql\getCliPar("module.Inventory") != "true")
		$GLOBALS['RestrictedTables'].=",'Inventory','Categories','Suppliers','Sizes','Manufacturers','SubCategories1','SubCategories2'";
	if (sql\getCliPar("module.CRM") != "true")
		$GLOBALS['RestrictedTables'].=",'Clients','ClientStatus'";
}

?>