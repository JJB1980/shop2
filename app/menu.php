<?php

session_start();
include_once "restUtils.php";

use RestUtils as rest;

$menu = array();

$menu["header"] = $_SESSION['ClientName'];

$item = array();
$item["title"] = "Home";
$item["link"] = "/home/";
array_push($menu,$item);

$item = array();
$item["title"] = "Search";
$item["link"] = "/search/";
array_push($menu,$item);


$item = array();
$item["title"] = "About Us";
$item["link"] = "/about/";
array_push($menu,$item);

$item = array();
$item["title"] = "Contact";
$item["link"] = "/contact/";
array_push($menu,$item);

if (isset($_SESSION["shopUser"]) && $_SESSION["shopUser"] != "") {
	$item = array();
	$item["title"] = "Account";
	$item["link"] = "/account/";
	array_push($menu,$item);

	$item = array();
	$item["title"] = "Logout";
	$item["link"] = "/logout/";
	array_push($menu,$item);
} else {
	$item = array();
	$item["title"] = "Login";
	$item["link"] = "/login/";
	array_push($menu,$item);
}

$item = array();
$item["title"] = "View Cart";
$item["link"] = "/cart/";
array_push($menu,$item);

rest\sendJSON(200,$menu);

?>