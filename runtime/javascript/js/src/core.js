/* 開発用止める時間 */
var DEV_TIMEOUT = 0;

/* local functions */
var console = window.console;
var initDebug = function(timeout, isDev) {
  if (timeout !== undefined ) {
    DEV_TIMEOUT = timeout;
  }
  if (isDev !== true || !console) {
    console = {
      log: function(){},
      debug: function(){},
      info: function(){},
      warn: function(){},
      error: function(){}
    };
  }
};

var loadContent = function (url, obj, callback, argments) {
  var complete = false;
  var request = new XMLHttpRequest();
  if (argments == null) {
    argments = [];
  }
  request.onreadystatechange = function () {
    if (!complete && request.readyState === 4) {
      complete = true;
      argments.unshift(request);
      callback.apply(obj, argments);
    }
  };
  request.open("GET", url, true);
  request.setRequestHeader("Content-Type" , "application/x-www-form-urlencoded");
  request.send(null);
};

/**
 * @param {Object} origin Original object
 * @return {Object}
 */
var copyObject = function (origin) {
  var n = {};
  for (var name in origin) {
    n[name] = origin[name];
  }
  return n;
};

/**
 * @param {Function} childCtor Child class.
 * @param {Function} parentCtor Parent class.
 */
var inherits = function(childCtor, parentCtor) {
  /** @constructor */
  function tempCtor() {};
  tempCtor.prototype = parentCtor.prototype;
  childCtor.superClass_ = parentCtor.prototype;
  childCtor.prototype = new tempCtor();
  childCtor.prototype.constructor = childCtor;
};

