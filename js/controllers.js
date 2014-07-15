'use strict';

/* Controllers */
angular.module('StoreApp.controllers', ['ui.bootstrap']).

controller('categoriesController', function($scope, storeServices) {

	$scope.categoryList = [];
	
	$scope.loadCategories = function () {
	 	storeServices.getCategories().success(function (response) {
	 		$scope.categoryList = response;
	 	});
	};
	
	$scope.thisThingClicked = function (cat,subcat1) {
		//alert(1);
		window.location = "#/categories/"+cat+"/"+subcat1;
	};
	
	$scope.loadCategories();

}).

controller('menuController', function($scope, storeServices) {

	$scope.menuList = [];
	//$scope.currentPage = $route.current.templateUrl;
	//alert($route.current.templateUrl);
	
	$scope.loadMenu = function () {
	 	storeServices.getMenu().success(function (response) {
	 		$scope.menuList = response;
	 	});
	};
	
	$scope.loadMenu();

}).

controller('homeController', function($scope, $sce, storeServices) {

	$scope.homeDetails = [];	
	$scope.pageClass = "page-home";
	
	$scope.loadHome = function () {
	 	storeServices.getHome().success(function (response) {
	 		$scope.homeDetails = response;
	 	});
	};

	$scope.returnHomeMessage = function () {
		return $sce.trustAsHtml($scope.homeDetails.message);
	}
	
	$scope.loadHome();

}).

controller('aboutController', function($scope, $sce, storeServices) {

	$scope.aboutDetails = [];	
	
	$scope.loadAbout = function () {
	 	storeServices.getAbout().success(function (response) {
	 		$scope.aboutDetails = response;
	 	});
	};

	$scope.returnAboutMessage = function () {
		return $sce.trustAsHtml($scope.aboutDetails.message);
	}
	
	$scope.loadAbout();

}).

controller('contactController', function($scope, $sce, storeServices) {

	$scope.contactDetails = [];	
	
	$scope.loadContact = function () {
	 	storeServices.getContact().success(function (response) {
	 		$scope.contactDetails = response;
	 	});
	};

	$scope.returnContactMessage = function () {
		return $sce.trustAsHtml($scope.contactDetails.message);
	}
	
	$scope.loadContact();

}).
 
controller('stockItemController', function($scope, $routeParams, storeServices) {
	$scope.id = $routeParams.id;
	$scope.stockItem = null;
	$scope.myInterval = 3000;

	storeServices.getStockItem($scope.id).success(function (response) {
		$scope.stockItem = response;
	
		// log the id for also viewed search.
		var ref = $.cookie("client-id");
		if (isNaN(ref))
			ref = "";
		var url = "app/logClient.php?action=doit&ref="+ref;
		ref = serverGet(url);	
		$.cookie("client-id", ref, { expires: 1 });

	});
  
	$scope.GetCarouselActive = function (index) {
	
		if (index == 0)
			return "active";
		else 
			return "";
	};

	 
});
  
