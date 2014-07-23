'use strict';

/* Controllers */
angular.module('StoreApp.controllersSearch', ['ui.bootstrap']).

controller('searchController', function($scope, $routeParams, storeServices) {

    $scope.searchResults = null;
    $scope.currentPage = 1;
    $scope.pageSize = $.cookie("resultLimit");
    if ($scope.pageSize == "" || $scope.pageSize == undefined) {
        $scope.pageSize = 5;
        $.cookie("resultLimit", 5, { expires: 100 });
    }
    $scope.pageView = null;
    $scope.searchText = $.cookie("searchText");
    
    $scope.id = $routeParams.id;
    $scope.cat = $routeParams.cat;
    $scope.subcat1 = $routeParams.subcat1;
    $scope.subcat2 = $routeParams.subcat2;
    if ($scope.subcat2 == undefined)
        $scope.subcat2 = "";
     if ($scope.subcat1 == undefined)
        $scope.subcat1 = "";
       
    //$scope.searchType = 1;
 
    $scope.addToCart = function (id) {
        //alert(id);  
    };
 
    $scope.loadResults = function () {
        //alert($scope.searchType);
        switch(parseInt($scope.searchType)) {
            case 1:
                $scope.loadSearchResults();
                break;
            case 2:
                $scope.loadSpecialsResults();
                break;     
            case 3:
               // alert($scope.id);
                $scope.loadAlsoViewedResults();
                break;
             case 4:
               // alert($scope.id);
                $scope.loadCategoryResults();
                break;       
            default:
                //alert($scope.searchType);
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
        //alert($scope.id);
        storeServices.runSearch(3,"",$scope.pageSize,$scope.id,"","","").success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    $scope.loadCategoryResults = function () {
        //alert($scope.id);
        //alert($scope.cat+"|"+$scope.subcat1+"|"+$scope.subcat2);
        storeServices.runSearch(4,"",$scope.pageSize,"", $scope.cat, $scope.subcat1, $scope.subcat2).success(function (response) {
            $scope.searchResults = response;
            $scope.createPageView();
        });
    };

    
    $scope.pageChanged = function () {
        $scope.createPageView();
        //console.log('Page changed to: ' + $scope.currentPage);
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
        //alert(startRow+"|"+endRow);
        $scope.pageView = [];
        for (var i = (startRow - 1); i < endRow; i++) {
            //alert("ID: "+$scope.searchResults.Results[i].ID)
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
                //alert(limit);
                $.cookie("resultLimit", limit, { expires: 100 });
             $scope.createPageView();

        } catch (err) {
                //alert("saveFun: "+err);
        }
    };	

    $scope.isResultLimit = function (value) {
        try {
                var limit = $.cookie("resultLimit");
                if (value == limit)
                        return true;
                return false;
        } catch (err) {
                //alert("returnFunc: "+err);
                return "";
        }
    };	
  
    $scope.loadResults();

  
});
