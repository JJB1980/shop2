'use strict';

/* Services */

angular.module('StoreApp.services', []).

service('Session', function () {
  this.create = function (sessionId, userId, clientID) {
    $.cookie("session-id", sessionId, { expires: 100 });
    $.cookie("customer-id", userId, { expires: 100 });
    $.cookie("client-id", clientID, { expires: 100 });
  };
  this.destroy = function () {
    $.cookie("session-id","");
    $.cookie("customer-id","");
    $.cookie("client-id","");
  };
  this.isAuthenticated = function () {
      return ($.cookie("customer-id") ? true : false);
  };
  this.setClient = function (clientID) {
    $.cookie("client-id", clientID, { expires: 100 });
  };
  this.getID = function () {
    var id = $.cookie("session-id");
    return (id ? id : "");
  };
  this.customerID = function() {
    var cid = $.cookie("customer-id");
    return (cid ? cid : "");
  };
  return this;
});