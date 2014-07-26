<?php

//session_cache_limiter('private');
//session_cache_expire(10);
//register_shutdown_function('shutdown');

session_start();
//session_destroy();
include_once "sqlUtils.php";
include_once "dbConn.php";

use SqlUtils as sql;

$GLOBALS['DEBUG']=0;

if (!isset($_SESSION['adminUser'])) {
	//$adminLoc="192.168.2.2";
	//$adminLoc="192.168.2.4";
	//$adminLoc="localhost";
	//$adminUser="root";
	//$adminPassword="";
	/*
	$adminUser="jjbsw_root";
	$adminPassword="fender71";
	$adminData="jjbswcom_admin";
	*/
	$adminUser="ADMINSQLUSR";
	$adminPassword="rty654";
	$adminData="adminData";
	$adminLoc="localhost";
	
	$_SESSION["adminUser"] = $adminUser;
	$_SESSION["adminPassword"] = $adminPassword;
	$_SESSION["adminLoc"] = $adminLoc;
	$_SESSION["adminData"] = $adminData;
}

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
$_SESSION['DIR']=$dir;

$svrRoot="/".$GLOBALS['location']."/";

$GLOBALS['serverRoot']=$svrRoot;

$GLOBALS['baseUrl']="https://";
//$GLOBALS['baseUrl']="http://";

$GLOBALS['serverName']=$GLOBALS['baseUrl'].$_SERVER['SERVER_NAME'].$svrRoot;

// connect to databases.
//$GLOBALS['ADB'] = new dbConn($adminUser,$adminPassword,$adminLoc,$adminData);
$GLOBALS['acon'] = sql\connect($_SESSION["adminLoc"],
			       $_SESSION["adminUser"],
			       $_SESSION["adminPassword"],
			       $_SESSION["adminData"]);
$GLOBALS['dcon'] = null;
//$GLOBALS['DDB'] = null;

	// Check connection
if (sql\connectErrNo()) {
  	$err = "Failed to connect to MySQL: " .sql\connectError();
  	die("error");
}

if (!isset($_SESSION['ServerType'])) {
	$_SESSION['ServerType'] = sql\aval("select ServerType from AppServer where IPAddress='{$servName}' or ServerName='{$servName}' and Status='shop.live'","ServerType");
	//echo $_SESSION['ServerType'];
}

if (!isset($_SESSION['clientID']) && isset($_REQUEST['client'])) {
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
		$_SESSION['clientPassword']=$row['SQLPassword'];
		$_SESSION['dataName']=$row['DatabaseName'];
	}
}

if(isset($_SESSION['clientUser'])) {
	$GLOBALS['dcon']=sql\connect($_SESSION['dataLocation'],
				     $_SESSION['clientUser'],
				     $_SESSION['clientPassword'],
				     $_SESSION['dataName']);
	//$GLOBALS['DDB'] = new DBConn($_SESSION['clientUser'],$_SESSION['password'],$_SESSION['dataLocation'],$_SESSION['dataName']);
} else {
	//no client supplied...
	header('Location: https://www.google.com');
	exit;
}
	// Check connection
if (sql\connectErrNo()) {
  	$err = "Failed to connect to MySQL: " . sql\connectError();
  	die("error");
}

// set timezone
if (isset($_SESSION['clientID'])) {
	$tz = sql\getCliPar("default.Timezone");
	if ($tz != "") {
		date_default_timezone_set($tz);
		
	} else {
		date_default_timezone_set("Australia/Brisbane");
	}
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