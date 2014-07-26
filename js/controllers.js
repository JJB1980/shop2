'use strict';

/* Controllers */
angular.module('StoreApp.controllers', ['ui.bootstrap']).

controller('loginController', function($scope, $rootScope, storeServices) {

	//$scope.categoryList = [];
	$scope.loggedIn = false;
	$scope.client = "";
	
	$scope.doLogin = function () {
		console.log($scope.email+"|"+$scope.password);
	 	storeServices.loginServ($scope.email,$scope.password,0,0,"").success(function (response) {
			/*
	 		$scope.system.message = response.message;
			     */
			if (response.status === "ok") {
				$scope.loggedIn = true;
				$rootScope.$broadcast('UPDATE_MENU');
				$.cookie("customer-token-"+response.client, response.token, { expires: 100 });
				$.cookie("customer-id-"+response.client, response.ID, { expires: 100 });
				$rootScope.clientID = response.client;
				window.location = "#/home";
			} else {
				alert(response.message);
			}
	 	});
	};

	$scope.doLogout = function () {
	 	storeServices.loginServ("","",0,1,"").success(function (response) {
			$scope.message = response.message;
			if (response.status === "ok") {
				$scope.loggedIn = false;
				$rootScope.$broadcast('UPDATE_MENU');
				$.cookie("customer-token-"+response.client, "");
				$.cookie("customer-id-"+response.client, "");
			}
	 	});
	};

	$scope.autoLogin = function () {
		if ($scope.loggedIn) {
			return;
		}
	 	storeServices.getClientID().success(function (response) {
			token = $.cookie("customer-token-"+response.client);
			if (token === "" || token === undefined) {
				return;
			}
			storeServices.loginServ("","",1,0,token).success(function (response) {
				if (response.status === "ok") {
					$scope.loggedIn = true;
					$rootScope.$broadcast('UPDATE_MENU');
				}
			});		
		});
	};

	$scope.$on('AUTO_LOGIN', function() {
		console.log("AUTO_LOGIN");
		$scope.autoLogin();
	});

	
}).

controller('categoriesController', function($scope, storeServices) {

	$scope.categoryList = [];
	
	$scope.loadCategories = function () {
	 	storeServices.getCategories().success(function (response) {
	 		$scope.categoryList = response;
	 	});
	};
	
	$scope.thisThingClicked = function (cat,subcat1) {
		//console.log("parse:"+cat+"|"+subcat1);
		window.location = "#/categories/"+cat+"/"+subcat1;
		//$(window).trigger("click");
	};
	
	$scope.loadCategories();

}).

controller('menuController', function($scope, storeServices) {

	$scope.menuList = [];
	//$scope.currentPage = $route.current.templateUrl;
	
	$scope.loadMenu = function () {
	 	storeServices.getMenu().success(function (response) {
	 		$scope.menuList = response;
	 	});
	};

	$scope.$on('UPDATE_MENU', function() {
	      $scope.loadMenu();
	});

	$scope.loadMenu();

}).

controller('homeController', function($scope, $sce, $rootScope, storeServices) {

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

	$scope.autoLogin = function () {
		$rootScope.$broadcast('AUTO_LOGIN');
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
	$scope.stockItem = null;
	
	storeServices.getStockItem($scope.id).success(function (response) {
		$scope.stockItem = response;
	
		// log the id for also viewed search.
		var ref = $.cookie("client-id");
		if (isNaN(ref))
			ref = "";
		var url = "app/logClient.php?action=doit&ref="+ref;
		storeServices.serverGet(url).success(function (response) {
			ref = response;
			$.cookie("client-id", ref, { expires: 1 });
		});
		

	});
  
	$scope.GetCarouselActive = function (index) {
	
		if (index === 0)
			return "active";
		else 
			return "";
	};

	 
});
  
