'use strict';

/* Services */

angular.module('StoreApp.services', []).

factory('storeServices', function($http) {

  var storeAPI = {};

  storeAPI.loginServ = function (email,password,autoLogin,logout,token) {
    return $http({
      method: 'POST', 
      url: 'app/login.php',
      params: {
        email: email,
        password: password,
        autoLogin: autoLogin,
        logout: logout,
        token: token
      }
    });
  }	

  storeAPI.serverGet = function(urlString) {
    return $http({
      method: 'GET', 
      url: urlString
    });
  }	

  storeAPI.getClientID = function() {
    return $http({
      method: 'GET', 
      url: 'app/login.php',
      params: {
        getClient: 1
      }
    });
  }	

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
    console.log("runSearch="+searchType+"|"+query+"|"+page+"|"+id+"|"+cat+"|"+subcat1+"|"+subcat2);
    //url: 'app/search.php?q='+query+'&searchType='+searchType+'&page='+page+'&id='+id+'&cat='+cat+'&subcat1='+subcat1+'&subcat2='+subcat2
    return $http({
      method: 'GET', 
      url: 'app/search.php',
      params: {
        q: query,
        searchType: searchType,
        page: page,
        id: id,
        cat: cat,
        subcat1: subcat1,
        subcat2: subcat2
      }
    });
  }	

  storeAPI.getStockItem = function(idIn) {
    return $http({
      method: 'GET', 
      url: 'app/stockItem.php',
      params: {
        id: idIn
      }
    });
  }
 
  return storeAPI;
});