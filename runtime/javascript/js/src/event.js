/**
 * @constructor
 */
ff.Event = function(type) {
  this.type   = type;
  this.target = null;
};
ff.Event.ENTER_FRAME = "enterFrame";

/**
 * @constructor
 */
ff.EventDispatcher = function () {
  this.eventListers = {};
};

ff.EventDispatcher.prototype.addEventListener = function(type, listener) {
  var _eventListers = this.eventListers[type];
  if (_eventListers === null) {
    this.eventListers[type] = [listener];
  } else if (_eventListers.indexOf(listener) === -1) {
    _eventListers.push(listener);
  }
};

ff.EventDispatcher.prototype.removeEventListner = function(type, listener) {
  var _eventListers = this.eventListers[type];
  if (_eventListers != null) {
    var i = _eventListers.indexOf(listener);
    if (i !== -1) {
      _eventListers.splice(i, 1);
    }
  }
};

ff.EventDispatcher.prototype.dispatchEvent = function(evt) {
  if (evt.target === null) {
    evt.target = this;
  }
  var _eventListers = this.eventListers[evt.type];
  if (_eventListers != null) {
    for (var i = 0, len = _eventListers.length; i < len; i++) {
      _eventListers[i].call(this, evt);
    }
  }
};

