'use strict';

/* Controllers */
angular.module('StoreApp.controllersCart', ['ui.bootstrap']).

controller('cartController', function($scope, storeServices, Session, CartAPI, API) {

    $scope.cart = {};
    $scope.cart.items = [];
    $scope.cart.itemsInCart = 0;
    $scope.cart.valueOfCart = 0;
    
    $scope.emptyCart = function () {
        if ($scope.cart.itemsInCart === 0) {
            return;
        }
        if (!confirm("Empty Cart?")) {
            return;
        }
        console.log("Empty Cart");
        CartAPI.empty();
        $scope.updateCart();
    }

    $scope.updateCart = function () {
        $scope.cart = CartAPI.myCart();
        console.log("Cart changed.");
        console.log($scope.cart);
    };

    $scope.checkOut = function () {
        if ($scope.cart.itemsInCart === 0) {
            return;
        }
        if (!confirm("Proceed to checkout?")) {
            return;
        }
        console.log("checkout");
        var items = [];
        for (var i = 0; i < $scope.cart.items.length; i++) {
            var item = {};
            item.ID = $scope.cart.items[i].ID;
            item.Price = $scope.cart.items[i].Price;
            item.qty = $scope.cart.items[i].qty;
            item.ExcludeGST = $scope.cart.items[i].ExcludeGST;
            items.push(item);
        }
        storeServices.account("saveCart",Session.customerID(),"",API.toJsonUri(items)).success(function (response) {
            CartAPI.empty();
            $scope.cart = CartAPI.myCart();
            alert(response.message);
        }).error(function (response) {
            alert(response.message);
        });
    };

    $scope.updateItem = function (item) {
        var qty = CartAPI.getQty("cartItems"+item.ID);
        if (qty < 0) {
                return;
        }
        CartAPI.add(item,qty);
        $scope.updateCart();
    };
    
    $scope.$on("CART_CHANGED", function () {
        $scope.updateCart();
    });
  
});
