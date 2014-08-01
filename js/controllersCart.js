'use strict';

/* Controllers */
angular.module('StoreApp.controllersCart', ['ui.bootstrap']).

controller('cartController', function($scope, storeServices, Session, CartAPI) {

    $scope.cart = {};
    $scope.cart.itemsInCart = 0;
    $scope.cart.valueOfCart = 0;
    
    $scope.emptyCart = function () {
        console.log("Empty Cart");
        CartAPI.empty();
    }

    $scope.$on("CART_CHANGED", function () {
        $scope.cart = CartAPI.myCart();
    });
  
});
