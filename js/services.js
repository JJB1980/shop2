'use strict';

/* Services */

angular.module('StoreApp.services', []).

factory('storeServices', function($http) {

  var storeAPI = {};

  storeAPI.getCategories = function() {
    return $http({
      method: 'GET', 
      url: 'app/categories.php'
    });
  }	

  storeAPI.getMenu = function() {
    return $http({
      method: 'GET', 
      url: 'app/menu.php'
    });
  }	

  storeAPI.getHome = function() {
    return $http({
      method: 'GET', 
      url: 'app/home.php'
    });
  }	

  storeAPI.getAbout = function() {
    return $http({
      method: 'GET', 
      url: 'app/about.php'
    });
  }
    
  storeAPI.getContact = function() {
    return $http({
      method: 'GET', 
      url: 'app/contact.php'
    });
  }
  
  storeAPI.runSearch = function (searchType, query, page, id, cat, subcat1, subcat2) {
    //alert(searchType+"|"+query+"|"+page+"|"+id+"|"+cat+"|"+subcat1+"|"+subcat2);
    return $http({
      method: 'GET', 
      url: 'app/search.php?q='+query+'&searchType='+searchType+'&page='+page+'&id='+id+'&cat='+cat+'&subcat1='+subcat1+'&subcat2='+subcat2
    });
  }	

  storeAPI.getStockItem = function(id) {
    return $http({
      method: 'GET', 
      url: 'app/stockItem.php?id='+id
    });
  }
 
  return storeAPI;
});