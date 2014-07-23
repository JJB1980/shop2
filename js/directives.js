'use strict';

/* Directives */


angular.module('StoreApp.directives', []).

directive('appVersion', ['version', function(version) {
    return function(scope, elm, attrs) {
      elm.text(version);
    };
  }]).

directive('mySearchResults', function() {
  return {
    restrict: 'AE',
    scope: {
      searchType: "@"
    },
    controller: 'searchController',
    templateUrl: 'partials/searchResultsDirective.html'
  }
}).

directive('closeCats', function() {
  return {
    restrict: 'C',
    link: function(scope, element, attrs) {
        element.on('click',function () {
		$("#categories").hide("slide");
                $("#categoriesShow").show();
        });
    }
  }
}).

directive('showCats', function() {
  return {
    restrict: 'C',
    link: function(scope, element, attrs) {
        element.on('click',function () {
                $("#categoriesShow").hide();
		$("#categories").show("slide");
        });
    }
  }
}).

directive('navLink', function() {
  return {
    restrict: 'C',
    link: function(scope, element, attrs) {
        element.on('click',function () {
		$("#myNavList li").removeClass("activeNav");
		element.parent().addClass("activeNav");
        });
    }
  }
});

