'user strict';

/* Factories */

angular.module('StoreApp.factories', []).

factory('storeServices', function($http, $rootScope, $location, Session) {

  var storeAPI = {};

    storeAPI.node = function () {
        return "http://localhost:3000/";
    };
  
    storeAPI.initApplication = function () {
        if (window._LOCAL) {
            url = this.node()+"appinit?callback=JSON_CALLBACK";
            console.log(url);
            return $http.jsonp(url);
        } else {
            return $http({
               method: 'GET', 
               url: 'app/application.php'
             });
        }
    };
      
    storeAPI.autoLogin = function () {
            if (Session.customerID() !== "") {
                    return;
            }
            var token = Session.getID();
            if (token === "" || token === undefined) {
                    return;
            }
            this.loginServ("","",1,0,token).success(function (response) {
                    if (response.status === "ok") {
                            $rootScope.$broadcast('UPDATE_MENU');
                    }
            });		
    };
    
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
  };
  
  storeAPI.account = function(action,customerID,orderID,jsonString) {
    return $http({
      method: 'POST', 
      url: 'app/account.php',
      params: {
        action: action,
        customerID: customerID,
        orderID: orderID,
        json: jsonString
      }
    });
  };

  storeAPI.serverGet = function(urlString,paramsObj) {
    return $http({
      method: 'GET', 
      url: urlString,
      params: paramsObj
    });
  };	

  storeAPI.getClientID = function() {
    return $http({
      method: 'GET', 
      url: 'app/login.php',
      params: {
        getClient: 1
      }
    });
  };	

  storeAPI.getCategories = function() {
    if (window._LOCAL) {
        url = this.node()+"categories?callback=JSON_CALLBACK";
        console.log(url);
        return $http.jsonp(url);
    } else {
        return $http({
          method: 'GET', 
          url: 'app/categories.php'
        });
    }
  };

  storeAPI.getMenu = function() {
    return $http({
      method: 'GET', 
      url: 'app/menu.php'
    });
  };	

  storeAPI.getHome = function() {
    if (window._LOCAL) {
        url = this.node()+"home?callback=JSON_CALLBACK";
        console.log(url);
        return $http.jsonp(url);
    } else {
        return $http({
          method: 'GET', 
          url: 'app/home.php'
        });
    }
  };	

  storeAPI.getAbout = function() {
    if (window._LOCAL) {
        url = this.node()+"about?callback=JSON_CALLBACK";
        console.log(url);
        return $http.jsonp(url);
    } else {
        return $http({
          method: 'GET', 
          url: 'app/about.php'
        });
    }
  };
    
  storeAPI.getContact = function() {
    if (window._LOCAL) {
        url = storeAPI.node()+"contact?callback=JSON_CALLBACK";
        console.log(url);
        return $http.jsonp(url);
    } else {
        return $http({
          method: 'GET', 
          url: 'app/contact.php'
        });
    }
  };
  
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
  };	

  storeAPI.getStockItem = function(idIn) {
    if (window._LOCAL) {
        url = this.node()+"stockItem/"+idIn+"/?callback=JSON_CALLBACK";
        console.log(url);
        return $http.jsonp(url);
    } else {
        return $http({
            method: 'GET', 
            url: 'app/stockItem.php',
            params: {
              id: idIn
            }
        });
    }
  };
 
  return storeAPI;
});