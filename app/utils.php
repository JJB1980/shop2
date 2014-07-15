<?php


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
	$imgServ = sqlAVal("select ImageServer from ClientData where ID = ".$_SESSION['clientID'],"ImageServer");   
	//$servType = sqlAVal("select ServerType from ImageServer where ID = ".$imgServ,"ServerType");   
   if ($_SESSION['ServerType'] == "linux") { //$servType == "linux")
   	$imgLoc = sqlAVal("select LinuxShare from ImageServer where ID = ".$imgServ,"LinuxShare");
   } else {
   	$imgLoc = sqlAVal("select WindowsShare from ImageServer where ID = ".$imgServ,"WindowsShare");
   	//$imgLoc = sqlAVal("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");
   	$imgLoc = str_replace("/","\\",$imgLoc);
   }
   return $imgLoc;
}

function getFileDir() {
	$serv = sqlAVal("select FileServer from ClientData where ID = ".$_SESSION['clientID'],"FileServer");   
   if ($_SESSION["ServerType"] == "linux")
   	$loc = sqlAVal("select LinuxShare from FileServer where ID = ".$serv,"LinuxShare");
   else {
   	$loc = sqlAVal("select WindowsShare from FileServer where ID = ".$serv,"WindowsShare");
		//$loc = sqlAVal("select ServerName from FileServer where ID = ".$serv,"ServerName");
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