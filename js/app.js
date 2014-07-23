'use strict';

// Declare app level module which depends on filters, and services

var app = angular.module('StoreApp', [
  'StoreApp.controllers',
  'StoreApp.controllersSearch',
  'StoreApp.services',
  'StoreApp.directives',
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ngAnimate'
]);

app.config(['$routeProvider', function($routeProvider) {
  $routeProvider.
  	when("/", {templateUrl: "partials/home.html", controller: "homeController"}).
	when("/home/", {templateUrl: "partials/home.html", controller: "homeController"}).
	when("/search/", {templateUrl: "partials/search.html", controller: "searchController"}).
	
	when("/categories/:cat", {templateUrl: "partials/categories.html", controller: "searchController"}).
	when("/categories/:cat/:subcat1", {templateUrl: "partials/categories.html", controller: "searchController"}).
	when("/categories/:cat/:subcat1/:subcat2", {templateUrl: "partials/categories.html", controller: "searchController"}).

	when("/about/", {templateUrl: "partials/about.html", controller: "aboutController"}).
	when("/contact/", {templateUrl: "partials/contact.html", controller: "contactController"}).
	when("/login/", {templateUrl: "partials/login.html", controller: "loginController"}).
	when("/logout/", {templateUrl: "partials/logout.html", controller: "loginController"}).

	when("/stockItem/:id", {templateUrl: "partials/stockItem.html", controller: "stockItemController"}).
	otherwise({redirectTo: '/'});
}]);
