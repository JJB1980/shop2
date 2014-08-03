<?php

include_once("dependencies.php");

//use RestUtils as rest;

$menu = array();

$menu["header"] = $_SESSION['ClientName'];

$item = array();
$item["title"] = "Home";
$item["link"] = "/home/";
$item["id"] = "home";
array_push($menu,$item);

$item = array();
$item["title"] = "Search";
$item["link"] = "/search/";
$item["id"] = "search";
array_push($menu,$item);


$item = array();
$item["title"] = "About Us";
$item["link"] = "/about/";
$item["id"] = "about";
array_push($menu,$item);

$item = array();
$item["title"] = "Contact";
$item["link"] = "/contact/";
$item["id"] = "contact";
array_push($menu,$item);

if (isset($_SESSION["shopUser"]) && $_SESSION["shopUser"] != "") {
	$item = array();
	$item["title"] = "Account";
	$item["link"] = "/account/";
	$item["id"] = "account";
	array_push($menu,$item);

	$item = array();
	$item["title"] = "Logout";
	$item["link"] = "/logout/";
	$item["id"] = "logout";
	array_push($menu,$item);
} else {
	$item = array();
	$item["title"] = "Login";
	$item["link"] = "/login/";
	$item["id"] = "login";
	array_push($menu,$item);
}

$item = array();
$item["title"] = "View Cart";
$item["link"] = "/cart/";
$item["id"] = "cart";
array_push($menu,$item);

sendJSON(200,$menu);

?>