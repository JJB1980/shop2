<?php

if (!isset($_REQUEST['client'])) {
  $_REQUEST['client'] = 1;
}

include_once "app/connect.php";

?>

<!DOCTYPE html>
<!--[if  IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html lang="en" class="no-js"> 
<!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
<?php 
if (isset($_SESSION['ClientName']))
  echo $_SESSION['ClientName'];
?> - Store  
  </title>  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!--
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<link rel="stylesheet" href="jquery-ui-1.10.4.custom/css/smoothness/jquery-ui-1.10.4.custom.css"/>
-->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css"/>

  <link rel="stylesheet" href="css/app.css"/>
  
</head>

<body id="" data-ng-app="StoreApp" >

<!--
<div data-my-navigation-bar=""></div>
-->

<div data-ng-controller="menuController" id="myNavBar" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			  <span class="sr-only">Toggle navigation</span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			</button>
		  <a class="navbar-brand" href="#">{{menuList.header}}</a>
		</div>
		<div class="collapse navbar-collapse">
		  <ul id="myNavList" class="nav navbar-nav">
		
			<li data-ng-class="" data-ng-repeat="menuItem in menuList">
				<a class="nav-link" href="#{{menuItem.link}}">
				  {{menuItem.title}}</a>
			</li>          
		  
		  </ul>
		  
		</div><!--/.nav-collapse -->
	</div>
</div>

<div id="categoriesShow" class="catContainer" >
	<span class="show-cats toggleCats" tooltip-placement="bottom" tooltip="Show Categories">&gt;</span>
</div>

<div id="categories" class="catContainer" data-ng-controller="categoriesController">
	<span class="close-cats toggleCats" tooltip-placement="bottom" tooltip="Hide Categories">&lt;</span>
	<accordion class="navAccordion" close-others="true">
		<accordion-group data-ng-repeat="cat0 in categoryList" heading="{{cat0.CategoryDescription}}" >
			<ul class="noIco">
				<li data-ng-repeat="cat1 in cat0.SubCat1">
					<span class="dropdown" on-toggle="toggled(open)">
					<span class="dropdown-toggle">
					<a data-ng-click="thisThingClicked(cat0.CategoryCode,cat1.SubCategory1Code)" href="" >
					  {{cat1.SubCategory1Description}}
					</a>
					</span>
					<ul data-ng-show="cat1.CatCount > 0" class="dropdown-menu">
					  <li data-ng-repeat="cat2 in cat1.SubCat2">
					    <a href="#/categories/{{cat0.CategoryCode}}/{{cat1.SubCategory1Code}}/{{cat2.SubCategory2Code}}">
						{{cat2.SubCategory2Description}}</a>
					  </li>
					</ul>
				      </span>
				</li>
			</ul>
		</accordion-group>
	</accordion>
</div>
    
<div id="content" >
<div data-ui-view="" id="contentView"  ></div>
</div>

<footer>
 <p>&copy; JJBSW 2014</p>
</footer>



<!--

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 

<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular-route.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular-sanitize.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular-animate.min.js"></script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="js/ui-bootstrap-tpls-0.11.0.min.js"></script>  

<script src="bower_components/angular-ui/build/angular-ui.js"></script>

-->

<script src="bower_components/jquery/dist/jquery.js"></script>

<script src="bower_components/angular/angular.js"></script>
<script src="bower_components/angular-route/angular-route.js"></script>
<script src="bower_components/angular-sanitize/angular-sanitize.js"></script>
<script src="bower_components/angular-animate/angular-animate.js"></script>
<script src="bower_components/angular-ui-router/release/angular-ui-router.js"></script>


<script src="bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>

<script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>

<script src="bower_components/angularjs-modal-service/src/createDialog.js"></script>

      <!--
<script src="jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script> 
<script src="js/combobox.js"></script>	

-->
<script src="js/cookies.js"></script>

<script src="js/my.js"></script>  

<script src="js/app.js"></script>
<script src="js/services.js"></script>
<script src="js/factory.js"></script>
<script src="js/controllers.js"></script>
<script src="js/controllersSearch.js"></script>
<script src="js/controllersCart.js"></script>
<script src="js/filters.js"></script>
<script src="js/directives.js"></script>

  
</body>
</html>
