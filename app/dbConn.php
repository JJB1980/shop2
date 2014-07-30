<?php

// main database connection class.
class DBConn {

    protected $user = "";
    protected $password = "";
    protected $url = "";
    protected $database = "";

    public $connection = null;
    public $resultSet = null;
       
    function __construct(  /*...*/ ) { //$user,$password,$url,$database) {
        $args = func_get_args();
        if (count($args) === 0)
            return;
        $this->setConnection($args[0],$args[1],$args[2],$args[3]);
        //$this->setConnection($user,$password,$url,$database);
        if (isset($_SESSION["timezone"]))
            date_default_timezone_set($_SESSION["timezone"]);
        $this->connect();
    }

    function __destruct() {
        $this->disconnect();
    }
 
    public function setConnection($user,$password,$url,$database) {
        $this->user = $user;
        $this->password = $password;
        $this->url = $url;
        $this->database = $database;
    }
  
    public function connect() {
        $this->connection = mysqli_connect($this->url,$this->user,$this->password,$this->database);
        if (!mysqli_connect_errno()) {
            return true;
        } else {
            //throw mysqli_connect_error();
            $this->connection = false;
            return false;
        }
    }
 
    public function disconnect() {
        try {
            if ($this->connection)
                return (mysqli_close($this->connection) ? true : false);
            return true;
        } catch (Exception $e) {
            //echo $e->getMessage();
        }
    }
    
    public function query($sql) {
        $this->resultSet = $this->queryGet($sql);
        return ($this->resultSet ? true : false);
    }
 
    public function queryGet($sql) {
        try {
            return mysqli_query($this->connection,$sql);
        } catch(Exception $e) {
            trigger_error("SQL Error: ".$e->getMessage()." *** ".$sql);
        }
    }

    public function row() {
        return self::rowGet($this->resultSet);
    }

    public static function rowGet($resultSet) {
        if (!$resultSet)
            return false;
        return mysqli_fetch_array($resultSet);      
    }
 
    public function rowCount() {
        return self::rowCountGet($this->resultSet);
    }
    
    public static function rowCountGet($resultSet) {
        if (!$resultSet)
            return false;
        return mysqli_num_rows($resultSet);      
    }
   
    public function insertID() {
        return mysqli_insert_id($this->connection);
    }
    
    public function free() {
        return self::freeRS($this->resultSet);
    }
    
    public static function freeRS($rs) {
        return mysqli_free_result($rs);
    }

    public function val($sql,$fld) {
	$rs = $this->queryGet($sql);
	if (!$rs) {
                return "";
		//trigger_error("Error SQL: ".$sql);
		//return "";
	}
	$ret="";
        while($row = self::rowGet($rs)) {
                     $ret = $row[$fld];
        }
        if ($rs)
            self::freeRS($rs);
	return $ret;
    }
 
    public function exists($sql) {
           $rs = $this->queryGet($sql);
           if (!$rs)
                   return false;
           $exists=false;
           if (self::rowCountGet($rs) > 0) {
                   $exists = true;
           }
           self::freeRS($rs);
           return $exists;
   }

}

// client data connection utilities
class DataDBConn extends DBConn {

    function __construct() {
        
        parent::__construct($_SESSION['clientUser'],
                            $_SESSION['clientPassword'],
                            $_SESSION['dataLocation'],
                            $_SESSION['dataName']);
        
    }
  
    public function setAccPar($code,$value) {
            $dfltRCSql="select ID,SettingValue from AccountSettings where SettingCode='".$code."'";
            if (!$this->exists($dfltRCSql)) {
                    $csql="insert into AccountSettings (SettingCode,SettingValue) values ('".$code."','".$value."')";   
            } else {
                    $csql="update AccountSettings set SettingValue='".$value."' where SettingCode='".$code."'";   	
            }
            return $this->query($csql);
    }

    public function getAccPar($code) {
            $dfltRCSql="select ID,SettingValue from AccountSettings where SettingCode='".$code."'";
            return $this->val($dfltRCSql,"SettingValue");
    }

    public function setUsrPar($code,$value) {
	$dfltRCSql="select ID,SettingValue from UserSettings where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";
	if (!$this->exists($dfltRCSql)) {
		$csql="insert into UserSettings (SettingCode,SettingValue,UserID) values ('".$code."','".$value."',".$_SESSION['userID'].")";   
	} else {
		$csql="update UserSettings set SettingValue='".$value."' where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";   	
	}
	return $this->query($csql);
    }

    public function getUsrPar($code) {
            $dfltRCSql="select ID,SettingValue from UserSettings where UserID=".$_SESSION['userID']." and SettingCode='".$code."'";
            return $this->val($dfltRCSql,"SettingValue");
    }

}

// admin data connection utilities
class AdminDBConn extends DBConn {
    
    function __construct() {
        
        parent::__construct($_SESSION["adminUser"],
                            $_SESSION["adminPassword"],
                            $_SESSION["adminLoc"],
                            $_SESSION["adminData"]);
        
    }
    
    public function setCliPar($code,$value,$cli="") {
        if ($cli == "")
                $cli = $_SESSION['clientID'];	
        $dfltRCSql="select ID,SettingValue from ClientSettings where ClientID=".$cli." and SettingCode='".$code."'";
        if (!$this->exists($dfltRCSql)) {
                $csql="insert into ClientSettings (SettingCode,SettingValue,ClientID) values ('".$code."','".$value."',".$cli.")";   
        } else {
                $csql="update ClientSettings set SettingValue='".$value."' where ClientID=".$cli." and SettingCode='".$code."'";   	
        }
        return $this->query($csql);
    }
    
    public function getCliPar($code,$cli="") {
        if ($cli == "")
            $cli = $_SESSION['clientID'];
        $dfltRCSql="select ID,SettingValue from ClientSettings where ClientID=".$cli." and SettingCode='".$code."'";
        return $this->val($dfltRCSql,"SettingValue");  
    }

    public function getImageDir() {
        $imgServ = $this->val("select ImageServer from ClientData where ID = ".$_SESSION['clientID'],"ImageServer");   
        //$servType = aval("select ServerType from ImageServer where ID = ".$imgServ,"ServerType");   
        if ($_SESSION['ServerType'] == "linux") { //$servType == "linux")
             $imgLoc = $this->val("select LinuxShare from ImageServer where ID = ".$imgServ,"LinuxShare");
        } else {
             $imgLoc = $this->val("select WindowsShare from ImageServer where ID = ".$imgServ,"WindowsShare");
             //$imgLoc = aval("select ServerName from ImageServer where ID = ".$imgServ,"ServerName");
             $imgLoc = str_replace("/","\\",$imgLoc);
        }
        return $imgLoc;
    }

    public function getFileDir() {
        $serv = $this->val("select FileServer from ClientData where ID = ".$_SESSION['clientID'],"FileServer");   
       if ($_SESSION["ServerType"] == "linux")
            $loc = $this->val("select LinuxShare from FileServer where ID = ".$serv,"LinuxShare");
       else {
            $loc = $this->val("select WindowsShare from FileServer where ID = ".$serv,"WindowsShare");
                    //$loc = aval("select ServerName from FileServer where ID = ".$serv,"ServerName");
            $loc = str_replace("/","\\",$loc);
       }
       return $loc;
    }

}

?>