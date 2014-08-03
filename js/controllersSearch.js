'use strict';

/* Controllers */
angular.module('StoreApp.controllersSearch', ['ui.bootstrap']).

controller('searchController', function($scope, $stateParams, storeServices, CartAPI, Session, API) {

    $scope.searchResults = null;
    $scope.currentPage = 1;
    $scope.pageSize = $.cookie("resultLimit");
    if ($scope.pageSize === "" || $scope.pageSize === undefined) {
        $scope.pageSize = 5;
        $.cookie("resultLimit", 5, { expires: 100 });
    }
    $scope.pageView = null;
    $scope.searchText = $.cookie("searchText");
    
    $scope.id = $stateParams.id;
    $scope.cat = $stateParams.cat;
    $scope.subcat1 = $stateParams.subcat1;
    $scope.subcat2 = $stateParams.subcat2;
    if ($scope.subcat2 === undefined)
        $scope.subcat2 = "";
     if ($scope.subcat1 === undefined)
        $scope.subcat1 = "";
       
    //$scope.searchType = 1;
 
    $scope.addToCart = function (item) { //id,price,code,descr,gst,avail) {
        var qty = CartAPI.getQty("cartItems"+item.ID);
        if (qty < 0) {
                return;
        }
        console.log("add to cart: "+item.ID+","+qty);
        CartAPI.add(item,qty); //id,qty,price,code,descr,gst,avail);
    };
 
    $scope.loadResults = function () {
        switch(parseInt($scope.searchType)) {
            case 1:
                $scope.loadSearchResults();
                break;
            case 2:
                $scope.loadSpecialsResults();
                break;     
            case 3:
                $scope.loadAlsoViewedResults();
                break;
             case 4:
                $scope.loadCategoryResults();
                break;       
            default:

        }
    };

    
    $scope.loadSearchResults = function () {
        $.cookie("searchText", $scope.searchText, { expires: 100 });
        storeServices.runSearch(1,$scope.searchText,$scope.pageSize,"","","","").success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    $scope.loadSpecialsResults = function () {
        storeServices.runSearch(2,"",$scope.pageSize,"","","","").success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    $scope.loadAlsoViewedResults = function () {
        storeServices.runSearch(3,"",$scope.pageSize,$scope.id,"","","").success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    $scope.loadCategoryResults = function () {
        storeServices.runSearch(4,"",$scope.pageSize,"", $scope.cat, $scope.subcat1, $scope.subcat2).success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    
    $scope.pageChanged = function () {
        $scope.createPageView();
        console.log('Page changed to: ' + $scope.currentPage);
    };

    $scope.createPageView = function () {
        if ($scope.currentPage == 1) {
            var startRow = 1;
            var endRow = $scope.pageSize;
        } else {
            var startRow = (($scope.pageSize * $scope.currentPage) - $scope.pageSize) + 1;
            var endRow = startRow + $scope.pageSize;
        }
        if (endRow > $scope.searchResults.ResultsCount) {
            endRow = $scope.searchResults.ResultsCount;
        }
        $scope.pageView = [];
        for (var i = (startRow - 1); i < endRow; i++) {
            $scope.pageView.push($scope.searchResults.Results[i]);
        }
    };

 
    $scope.resultOptions = [
        { option: "5", value: "5" },	
        { option: "10", value: "10" },
        { option: "20", value: "20" },
        { option: "50", value: "50" }
    ];
    
    $scope.saveResultLimit = function () {
        try {
            var limit = $scope.pageSize;
            $.cookie("resultLimit", limit, { expires: 100 });
            $scope.createPageView();

        } catch (err) {
        }
    };	

    $scope.isResultLimit = function (value) {
        try {
                var limit = $.cookie("resultLimit");
                if (value == limit)
                        return true;
                return false;
        } catch (err) {
                return "";
        }
    };	
  
    $scope.loadResults();

  
});
