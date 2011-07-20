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

/** XHR function. after complete, call callback function with obj on this with argments */
var loadContent = function (url, obj, callback, args) {
  var complete = false; // TODO: is this really needed?
  var request = new XMLHttpRequest();
  if (args == null) {
    args = [];
  }
  request.onreadystatechange = function () {
    if (!complete && request.readyState === 4) {
      complete = true;
      args.unshift(request); // insert "request" at the top of args
      callback.apply(obj, args);
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

