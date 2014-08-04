<?php

//session_cache_limiter('private');
//session_cache_expire(10);
//register_shutdown_function('shutdown');

session_start();
//session_destroy();

include_once "dbConn.php";

$GLOBALS['DEBUG']=0;

if (!isset($_REQUEST['client'])) {
  $_REQUEST['client'] = 1;
}

//$adminLoc="192.168.2.2";
//$adminLoc="192.168.2.4";
//$adminLoc="localhost";
//$adminUser="root";
//$adminPassword="";
$adminUser="ADMINSQLUSR";
$adminPassword="rty654";
$adminData="adminData";
$adminLoc="localhost";
$GLOBALS["_LOCAL"] = "true";
/*
$adminUser="jjbsw_root";
$adminPassword="fender71";
$adminData="jjbswcom_admin";
$GLOBALS["_LOCAL"] = "false";

*/

$_SESSION["adminUser"] = $adminUser;
$_SESSION["adminPassword"] = $adminPassword;
$_SESSION["adminLoc"] = $adminLoc;
$_SESSION["adminData"] = $adminData;


$dir="\\";
$_SESSION['DIR']=$dir;

// connect to databases.
$conn = new AdminDBConn($_SESSION["adminLoc"],
			       $_SESSION["adminUser"],
			       $_SESSION["adminPassword"],
			       $_SESSION["adminData"]);
	// Check connection
if (!$conn->connection) {
  	//$err = "Failed to connect to MySQL: " .connectError();
  	die("database connection error");
}

//if (!isset($_SESSION['clientID']) && isset($_REQUEST['client'])) {
if (isset($_REQUEST['client'])) {
	//echo "Client=".$_REQUEST['client'];
	$sql = "select a.ClientName,a.ImageFolder,c.ServerURL,a.DatabaseName,a.SQLUser,a.SQLPassword,b.IPAddress 
						from ClientData a, DataServer b, ImageServer c
						where a.ID=".$_REQUEST['client']." and a.DataLocation=b.ID and a.ImageServer = c.ID";
	//echo $sql;
	$res = $conn->query($sql);
	$row = $conn->row();
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
	$conn->free();
}

if(!isset($_SESSION['clientUser'])) {

	//no client supplied...
	header('Location: http://jjbsw.com');
	exit;
}

// set timezone
if (isset($_SESSION['clientID'])) {
	$tz = $conn->getCliPar("default.Timezone");
	if ($tz === "") 
		$tz = "Australia/Brisbane";
} else {  
	$tz = "Australia/Brisbane";
}
$_SESSION["timezone"] = $tz;

?>