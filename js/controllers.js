'use strict';

/* Controllers */
angular.module('StoreApp.controllers', ['ui.bootstrap']).

controller('loginController', function($scope, $rootScope, $location, storeServices) {

	//$scope.categoryList = [];
	$scope.loggedIn = false;
	$scope.client = "";
	
	$scope.doLogin = function (loginForm) {
		console.log(loginForm.email+"|"+loginForm.password);
	 	storeServices.loginServ(loginForm.email,loginForm.password,0,0,"").success(function (response) {
			$scope.loggedIn = storeServices.doLogin(response);
			console.log("loggedIn: "+$scope.loggedIn);
	 	});
	};

	$scope.doLogout = function () {
	 	storeServices.loginServ("","",0,1,"").success(function (response) {
			$scope.message = response.message;
			$scope.loggedIn = ! storeServices.doLogout(response);
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

controller('categoriesController', function($scope, $location, storeServices) {

	$scope.categoryList = [];
	
	$scope.loadCategories = function () {
	 	storeServices.getCategories().success(function (response) {
	 		$scope.categoryList = response;
	 	});
	};
	
	$scope.thisThingClicked = function (cat,subcat1) {
		//console.log("parse:"+cat+"|"+subcat1);
		$location.path("/categories/"+cat+"/"+subcat1);
		//$(window).trigger("click");
	};
	
	$scope.loadCategories();

}).

controller('accountController', function($scope, storeServices) {

	$scope.account = null;
	//$scope.currentPage = $route.current.templateUrl;
	
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
 
controller('stockItemController', function($scope, $stateParams, storeServices) {
	$scope.id = $stateParams.id;
	$scope.stockItem = null;
	$scope.myInterval = 3000;
	$scope.stockItem = null;
	
	storeServices.getStockItem($scope.id).success(function (response) {
		$scope.stockItem = response;
	
		// log the id for also viewed search.
		var refer = $.cookie("client-id");
		if (isNaN(refer))
			refer = "";
		var url = "app/logClient.php"; //?action=doit&ref="+refer;
		var params = { action: "doit", ref: refer };
		storeServices.serverGet(url,params).success(function (response) {
			var ref = response;
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
  
