<?php

//namespace Utils;

require_once "sqlUtils.php";
//use SqlUtils as sql;

function xs($name) {
	$temp = "";
	if (isset($_GET[$name])) {
	  $temp = $_GET[$name];
	} else if (isset($POST[$name])) {
	  $temp = $_POST[$name];
	}
	return $temp;
}

function rv($name) {
	$val = "";
	if (isset($_REQUEST[$name])) {
		$val = $_REQUEST[$name];
	}
	return $val;
}

function nf($num) {
	if ($num == 0)
		return 0;
	return number_format($num, 2);
}

function cvtchars($instr) {
	//$remove = array("\\", "|");
	$outstr = str_replace("\\", "", $instr);
	$outstr=str_replace("'","\'",$outstr);
	$outstr=str_replace('"','\"',$outstr);
	//$outstr=str_replace('\\','\\"',$outstr);
	return $outstr;
}

function utf8_urldecode($str) {
        return html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)), null, 'UTF-8');
}

function dateLong() {
	return date("l, d F Y");
}

function timeFO() {
	return date('H:i:s');
}

function timeFI() {
	return date('H:i:s');
}

function dateFO() {
	$today = getdate();
	return $today['mday']."/".$today['mon']."/".$today['year'];
}

function dateFI() {
	return date("Y-m-d");
}

// format output date
function foDate($inVal) {
	if ($inVal=="")
		return "";
	$o=explode("-",$inVal);
	return $o[2]."/".$o[1]."/".$o[0];
}

// format input date
function fiDate($inVal) {
	if ($inVal=="")
		return "";
	$o=explode("/",$inVal);
	return $o[2]."-".$o[1]."-".$o[0];
}

function moveULFile($from,$to,$share=0) { 
/*
	if ($_SESSION['ServerType'] == "linux" && $share) {
		require_once ('smbclient.php');
		$smbc = new smbclient ('//johnpc/', 'john', '24seven');
		
	} else {

	}
*/
		return move_uploaded_file($from,$to);
}

function getImageDir() {
	$imgServ = aval("select ImageServer from ClientData where ID = ".$_SESSION['clientID'],"ImageServer");   
	//$servType = aval("select ServerType from ImageServer where ID = ".$imgServ,"ServerType");   
   if ($_SESSION['ServerType'] == "linux") { //$servType == "linux")
   	$imgLoc = aval("select LinuxShare from ImageServer where ID = ".$imgServ,"LinuxShare");
   } else {
   	$imgLoc = aval("select WindowsShare from ImageServer where ID = ".$imgServ,"WindowsShare");
   	//$imgLoc = aval("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");
   	$imgLoc = str_replace("/","\\",$imgLoc);
   }
   return $imgLoc;
}

function getFileDir() {
	$serv = aval("select FileServer from ClientData where ID = ".$_SESSION['clientID'],"FileServer");   
   if ($_SESSION["ServerType"] == "linux")
   	$loc = aval("select LinuxShare from FileServer where ID = ".$serv,"LinuxShare");
   else {
   	$loc = aval("select WindowsShare from FileServer where ID = ".$serv,"WindowsShare");
		//$loc = aval("select ServerName from FileServer where ID = ".$serv,"ServerName");
   	$loc = str_replace("/","\\",$loc);
   }
   return $loc;
}

function nerfFile($fid) {

	$sql = "select * from UploadFile where ID = ".$fid;	
	$results = aqry($sql);
	$ok = 0;
	while($row = row($results)) {	
		$file = $row['FileName'];
		$fileType = $row['FileType'];
		$fileFolder = $row['FileLocation'];
		$status = $row['Status'];
		if ($status != "deleted") {
			//$dir = "c:\\wamp\\www\\Prj\\upload\\";
			$dir=str_replace("/","\\",$fileFolder) . "\\";
			if (unlink($dir.$file.".".$fileType)) {
				$dsql = "update UploadFile set Status='deleted' where FileName='".$file."'";
				runASql($dsql);
				$ok=1;
			}
		}
	}

	free($results);
	return $ok;
}

?>