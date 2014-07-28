'use strict';

/* Services */

angular.module('StoreApp.services', []).

service('Session', function () {
  this.create = function (sessionId, userId, clientID) {
    this.id = sessionId;
    this.userId = userId;
    this.clientID = clientID;
  };
  this.destroy = function () {
    this.id = null;
    this.userId = null;
    this.clientID = null;
  };
  this.isAuthenticated = function () {
      return !!this.userId;
  };
  return this;
});