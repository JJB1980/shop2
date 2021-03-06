<?php

include_once("dependencies.php");

//use SqlUtils as sql;
//use RestUtils as rest;
//use Utils as ut;

$actMnu=0;

$conn = new DataDBConn();

$sql = "select * from Categories";
//$res1 = sql\dqry($sql);
$res1 = $conn->queryGet($sql);
if (!$res1)
	exit;
	
$response = array();
	

while ($r1 = DBConn::rowGet($res1)) {
        $cat1 = array();
        $cat1['CategoryCode'] = $r1['CategoryCode'];
        $cat1['CategoryDescription'] = $r1['CategoryDescription'];
        $cat1['SubCat1'] = array();
	$sql = "select * from SubCategories1 where CategoryCode = '".$r1['CategoryCode']."'";
       	//$res2 = sql\dqry($sql);
	$res2 = $conn->queryGet($sql);
        $count1 = 0;
	while ($r2 = DBConn::rowGet($res2)) {
            $cat2 = array();
            $cat2['SubCategory1Code'] = $r2['SubCategory1Code'];
            $cat2['SubCategory1Description'] = $r2['SubCategory1Description'];
	    $sql = "select * from SubCategories2 where SubCategory1Code = '".$r2['SubCategory1Code']."'";
            //$res3 = sql\dqry($sql);
	    $res3 = $conn->queryGet($sql);
            $cat2['SubCat2'] = array();
            $count2 = 0;
            while ($r3 = DBConn::rowGet($res3)) {
               $cat3 = array();
               $cat3['SubCategory2Code'] = $r3['SubCategory2Code'];
               $cat3['SubCategory2Description'] = $r3['SubCategory2Description'];
               array_push($cat2['SubCat2'],$cat3);
               $count2++;
            }
            $cat2['CatCount'] = $count2;
            array_push($cat1['SubCat1'],$cat2);
            $count1++;
            DBConn::freeRS($res3);	
	}
        $cat1['CatCount'] = $count1;
        array_push($response,$cat1);
	DBConn::freeRS($res2);
}

DBConn::freeRS($res1);

sendJSON(200,$response);

?>