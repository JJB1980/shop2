'use strict';

/* Services */

angular.module('StoreApp.services', []).

service('API', function () {
  this.toJsonUri = function (jsonObj) {
    return encodeURIComponent(angular.toJson(jsonObj));
  }
  return this;
}).

service('CartAPI', function ($rootScope, $timeout, Session) {
  this.initCart = function() {
    var cart = this.getCart();
    if (cart) {
      this.cart = cart;
      this.updateCart();
    } else {
      this.cart = {};
      this.cart.items = [];
    }
  };
  this.add = function (id,qty,price) {
    var item = {}, index = -1;
    for (var i = 0; i < this.cart.items.length; i++) {
      if (this.cart.items[i].id === id) {
        item = this.cart.items[i];
        item.qty = qty;
        index = i;
      }
    }
    if (index < 0) {
      item.id = id;
      item.qty = qty;
      item.price = price;
      index = this.cart.items.length;
    }
    this.cart.items[index] = item;
    this.setCart(this.cart);
    this.updateCart();
    console.log(this.cart);
  };
  this.empty = function () {
    this.cart = {};
    this.cart.items = [];
    this.setCart(this.cart);
    this.updateCart();
    console.log(this.cart);
  };
  this.updateCart = function () {
    var items = 0, value = 0;
    for (var i = 0; i < this.cart.items.length; i++) {
      var item = this.cart.items[i], qty = parseInt(item.qty);
      items += qty;
      value +=  (parseFloat(item.price) * qty);
    }
    this.cart.itemsInCart = items;
    this.cart.valueOfCart = value;
    console.log("Value of cart: "+value);
    $rootScope.$broadcast("CART_CHANGED");
  }
  this.myCart = function () {
    return this.cart;
  };
  this.setCart = function (cart) {
    $.cookie("cart-contents-"+Session.getClient(), angular.toJson(cart), { expires: 100 });
  };
  this.getCart = function () {
    var cart = $.cookie("cart-contents-"+Session.getClient());
    return (cart !== "" ? angular.fromJson(cart) : null)
  };
  this.setGST = function (gst) {
    this.GST = gst;
  };
  this.getGST = function () {
    return this.GST;
  };
  return this;
}).

service('Session', function () {
  this.create = function (sessionId, userId) {
    $.cookie("session-id-"+this.ClientID, sessionId, { expires: 100 });
    $.cookie("customer-id-"+this.ClientID, userId, { expires: 100 });
  };
  this.destroy = function () {
    $.cookie("session-id-"+this.ClientID,"");
    $.cookie("customer-id-"+this.ClientID,"");
  };
  this.isAuthenticated = function () {
      return (this.customerID() ? true : false);
  };
  this.setClient = function (clientID) {
    this.ClientID = clientID;
  };
  this.getClient = function () {
    return this.ClientID;
  };
  this.getID = function () {
    var id = $.cookie("session-id-"+this.ClientID);
    return (id ? id : null);
  };
  this.customerID = function() {
    var cid = $.cookie("customer-id-"+this.ClientID);
    return (cid ? cid : null);
  };
  
  return this;
});