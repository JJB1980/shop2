'use strict';

// Declare app level module which depends on filters, and services

var app = angular.module('StoreApp', [
  'StoreApp.controllers',
  'StoreApp.controllersSearch',
  'StoreApp.controllersCart',
  'StoreApp.services',
  'StoreApp.factories',
  'StoreApp.directives',
  'ngSanitize',
  'ui.bootstrap',
  'ui.router',
  'ngAnimate'
]);

app.config(function($stateProvider, $urlRouterProvider) {
  $urlRouterProvider.otherwise("/home/");
  $stateProvider
    .state('home', {
      url: "/home/",
      templateUrl: "partials/home.html",
      controller: "homeController"
    }).
    state("search", {url: "/search/",
	  templateUrl: "partials/search.html",
	  controller: "searchController"}).
    state("about", {url: "/about/",
	  templateUrl: "partials/about.html",
	  controller: "aboutController"}).
    state("contact", {url: "/contact/",
	  templateUrl: "partials/contact.html",
	  controller: "contactController"}).
    
    state("categories1", {url: "/categories/:cat",
	  templateUrl: "partials/categories.html",
	  controller: "searchController"}).
    state("categories2", {url: "/categories/:cat/:subcat1",
	  templateUrl: "partials/categories.html",
	  controller: "searchController"}).
    state("categories3", {url: "/categories/:cat/:subcat1/:subcat2",
	  templateUrl: "partials/categories.html",
	  controller: "searchController"}).

    state("login", {url: "/login/",
	  templateUrl: "partials/login.html",
	  controller: "loginController"}).
    state("logout", {url: "/logout/",
	  templateUrl: "partials/logout.html",
	  controller: "loginController"}).
    state("account", {url: "/account/",
	  templateUrl: "partials/account.html",
	  controller: "accountController",
	  auth: true }).

    state("cart", {url: "/cart/",
	  templateUrl: "partials/viewCart.html",
	  controller: "cartController"}).

    state("stockItem", {url: "/stockItem/:id",
	  templateUrl: "partials/stockItem.html",
	  controller: "stockItemController"});
    
});

app.run(function ($rootScope, $timeout, Session, API) {
  $rootScope.$on('$stateChangeStart', function (event, next) {
    console.log(next.templateUrl+"|"+next.auth+"|"+Session.isAuthenticated());
    //console.log(next);
    //console.log("menu_"+next.name);
    $timeout(function () {
      var el = $("#menu_"+next.name);
      if (el) {
	$("#myNavList li").removeClass("activeNav");
	el.addClass("activeNav");
      }
    },500);
    if (next.auth && !Session.isAuthenticated()) {
       event.preventDefault();
    }
  });
});

/*
app.config(['$routeProvider', function($routeProvider) {
  $routeProvider.
  	when("/", {templateUrl: "partials/home.html", controller: "homeController"}).
	when("/home/", {templateUrl: "partials/home.html", controller: "homeController"}).
	when("/search/", {templateUrl: "partials/search.html", controller: "searchController"}).
	when("/about/", {templateUrl: "partials/about.html", controller: "aboutController"}).
	when("/contact/", {templateUrl: "partials/contact.html", controller: "contactController"}).
	
	when("/categories/:cat", {templateUrl: "partials/categories.html", controller: "searchController"}).
	when("/categories/:cat/:subcat1", {templateUrl: "partials/categories.html", controller: "searchController"}).
	when("/categories/:cat/:subcat1/:subcat2", {templateUrl: "partials/categories.html", controller: "searchController"}).

	when("/login/", {templateUrl: "partials/login.html", controller: "loginController"}).
	when("/logout/", {templateUrl: "partials/logout.html", controller: "loginController"}).
	when("/account/", {templateUrl: "partials/account.html", controller: "accountController", auth: true }).

	when("/stockItem/:id", {templateUrl: "partials/stockItem.html", controller: "stockItemController"}).
	otherwise({redirectTo: '/'});
}]);
*/
