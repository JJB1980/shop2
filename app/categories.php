<?php

include_once "connect.php";
include_once "sqlUtils.php";
include_once "utils.php";

$actMnu=0;

$sql = "select * from Categories";
$res1 = dqry($sql);

if (!$res1)
	exit;

$response = array();
	

while ($r1 = row($res1)) {
        $cat1 = array();
        $cat1['CategoryCode'] = $r1['CategoryCode'];
        $cat1['CategoryDescription'] = $r1['CategoryDescription'];
        $cat1['SubCat1'] = array();
       	$res2 = dqry("select * from SubCategories1 where CategoryCode = '".$r1['CategoryCode']."'");
        $count1 = 0;
	while ($r2 = row($res2)) {
            $cat2 = array();
            $cat2['SubCategory1Code'] = $r2['SubCategory1Code'];
            $cat2['SubCategory1Description'] = $r2['SubCategory1Description'];
            $res3 = dqry("select * from SubCategories2 where SubCategory1Code = '".$r2['SubCategory1Code']."'");
            $cat2['SubCat2'] = array();
            $count2 = 0;
            while ($r3 = row($res3)) {
               $cat3 = array();
               $cat3['SubCategory2Code'] = $r3['SubCategory2Code'];
               $cat3['SubCategory2Description'] = $r3['SubCategory2Description'];
               array_push($cat2['SubCat2'],$cat3);
               $count2++;
            }
            $cat2['CatCount'] = $count2;
            array_push($cat1['SubCat1'],$cat2);
            $count1++;
            free($res3);	
	}
        $cat1['CatCount'] = $count1;
        array_push($response,$cat1);
	free($res2);
}

free($res1);

header('Content-Type: application/json');
echo json_encode($response);

?>