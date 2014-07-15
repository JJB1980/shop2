<?php

function nrows($result) {
	if (!$result)
		return 0;
	return mysqli_num_rows($result);
}

function row($result) {
	if (!$result)
		return false;
	return mysqli_fetch_array($result);
}

function free($result) {
	if (!$result)
		return;
	return mysqli_free_result($result);
}


function logx($value,$del=1) {
	//if ($del)		
	//	runASQL("delete from XLog");
	$csql="insert into XLog (LogValue) values ('".cvtchars($value)."')";   
	runASql($csql);
}

function checkUnique($id,$field,$value,$table,$type,$dataConn="d") {
	if ($value=="")
		return 1;
	$sql="select ID from ".$table." where ".$field."=";
	if ($type=="integer"||$type=="float")
		$sql.=$value;
	else 
		$sql.="'".$value."'";
	if ($dataConn=="d")
		$result=dqry($sql); 
	else 
		$result=aqry($sql);
	$ok=0;
	if ($result) {
		$row = row($result);
		$xid=$row["ID"];
		if ($xid==$id||$xid=="")
			$ok=1;
	}
	return $ok;
}

function userCombo($id,$all=1,$self=1,$allClients=0) {

	echo '<select class="combobox" id="'.$id.'">';
	
	if ($self)
   	 echo "<option value=".$_SESSION['userID'].">Myself</option>";
    
    if ($all)
    	echo '<option value="">All</option>';

	$sql="select * from UsersA";
	if (!$allClients)
		$sql.=" where ClientID=".$_SESSION['clientID'];
		
	$result = aqry($sql);
	
	while($row = row($result)) {
		echo "<option value='".$row['ID']."'>".$row['FirstName']." ".$row['LastName']."</option>";
	}	
	echo '</select>';
}

function setAccPar($code,$value) {
	$dfltRCSql="select ID,SettingValue from AccountSettings where SettingCode='".$code."'";
	if (!dExists($dfltRCSql)) {
		$csql="insert into AccountSettings (SettingCode,SettingValue) values ('".$code."','".$value."')";   
	} else {
		$csql="update AccountSettings set SettingValue='".$value."' where SettingCode='".$code."'";   	
	}
	return runSQL($csql);
}

function getAccPar($code) {
	$dfltRCSql="select ID,SettingValue from AccountSettings where SettingCode='".$code."'";
	return sqlVal($dfltRCSql,"SettingValue");
}

function setCliPar($code,$value,$cli="") {
	if ($cli == "")
		$cli = $_SESSION['clientID'];	
	$dfltRCSql="select ID,SettingValue from ClientSettings where ClientID=".$cli." and SettingCode='".$code."'";
	if (!aExists($dfltRCSql)) {
		$csql="insert into ClientSettings (SettingCode,SettingValue,ClientID) values ('".$code."','".$value."',".$cli.")";   
	} else {
		$csql="update ClientSettings set SettingValue='".$value."' where ClientID=".$cli." and SettingCode='".$code."'";   	
	}
	return runASQL($csql);
}

function getCliPar($code,$cli="") {
	if ($cli == "")
		$cli = $_SESSION['clientID'];
	$dfltRCSql="select ID,SettingValue from ClientSettings where ClientID=".$cli." and SettingCode='".$code."'";
	return sqlAVal($dfltRCSql,"SettingValue");
}

function setUsrPar($code,$value) {
	$dfltRCSql="select ID,SettingValue from UserSettings where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";
	if (!dExists($dfltRCSql)) {
		$csql="insert into UserSettings (SettingCode,SettingValue,UserID) values ('".$code."','".$value."',".$_SESSION['userID'].")";   
	} else {
		$csql="update UserSettings set SettingValue='".$value."' where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";   	
	}
	return runSQL($csql);
}

function getUsrPar($code) {
	$dfltRCSql="select ID,SettingValue from UserSettings where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";
	return sqlVal($dfltRCSql,"SettingValue");
}

function userName($usr) {
	$un="";
	$sql="select * from UsersA where ID=".$usr;
	$result = aqry($sql);
	if (!$result) {
		dbug($sql);		
		return "";
	}
	while($row = row($result)) {
		$un=$row['FirstName']." ".$row['LastName'];
	}
	free($result);
	return $un;
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

function auditLog($what,$table,$id,$upd) {
	$user=$_SESSION['userID'];
	$sql = "INSERT INTO AuditLog (AuditType,AuditValue,UserID,RecordID,TableName,AuditDate,AuditTime)";
	$sql.=" VALUES (\"".$what."\",\"".$upd."\",".$user.",".$id.",\"".$table."\",'".dateFI()."','".timeFI()."')";
	//echo $sql;	
	$res=runSQL($sql);
}

function dateLong() {
	return date("l, d F Y");
	$today = getdate();
	return $today['weekday'].", ".$today['mday']." ".$today['month']." ".$today['year'];
}

function timeFO() {
	//date("l, d F Y");
	return date('H:i:s');
}

function timeFI() {
	//date("l, d F Y");
	return date('H:i:s');
}

function dateFO() {
	//date("l, d F Y");
	//return date('H:i:s');
	$today = getdate();
	return $today['mday']."/".$today['mon']."/".$today['year'];
}

function dateFI() {
	return date("Y-m-d");
	$today = getdate();
	return $today['year']."-".$today['mon']."-".$today['mday'];
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

// retrieve a specific value from client data.
function sqlVal($sql,$fld) {
	$result = dqry($sql);
	if (!$result) {
		trigger_error("Error SQL: ".$sql);
		return "";
	}
	$ret="";
   while($row = row($result)) {
 	 	$ret = $row[$fld];
   }
	free($result);
	return $ret;
}

// retrieve a specific value from admin data.
function sqlAVal($sql,$fld) {
	$result = aqry($sql);
	if (!$result) {
		trigger_error("Error SQL: ".$sql);
		return "";
	}
	$ret="";
   while($row = row($result)) {
 	 	$ret = $row[$fld];
   }
	free($result);
	return $ret;
}

function aqry($sql) {
	return mysqli_query($GLOBALS['acon'],$sql);
}

function dqry($sql) {
	return mysqli_query($GLOBALS['dcon'],$sql);
}

// run sql ie  insert update
function runASQL($sql) {
	//$result = mysqli_query($GLOBALS['dcon'],$sql);
	$ret=true;
	if (!aqry($sql)) {

		trigger_error("Error SQL: ".$sql);
		return "";

		$ret = false;
  		//die('Error: ' . mysqli_error($GLOBALS['dcon']));
   }
	return $ret;
}

// run sql ie  insert update
function runSQL($sql) {
	//$result = mysqli_query($GLOBALS['dcon'],$sql);
	$ret=true;
	if (!dqry($sql)) {

		trigger_error("Error SQL: ".$sql);
		return "";

		$ret = false;
  		//die('Error: ' . mysqli_error($GLOBALS['dcon']));
   }
	return $ret;
}

function dExists($sql) {
	$result = dqry($sql);
	if (!$result)
		return false;
	$ret=false;
	if (nrows($result) > 0) {
		$ret = true;
	}
	free($result);
	return $ret;
}

function aExists($sql) {
	$result = aqry($sql);
	if (!$result)
		return false;
	$ret=false;
	if (nrows($result) > 0) {
		$ret = true;
	}
	free($result);
	return $ret;
}

?>