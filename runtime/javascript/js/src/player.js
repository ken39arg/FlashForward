/**
 * Player Opbject
 *
 * @param {string} url Url for swf file.
 * @param {string} target Target dom id.
 * @param {Object} rendererType Rendering type is Renderer Object or "svg" or "canvas".
 * @param {boolean} fixDisplay If you want to fix display then true.
 * @param {boolean} isDev If you want to develop mode then true.
 * @param {integer} timeout If you use isDev=true then stop player time.
 * @constructor
 * @inherits ff.EventDispatcher
 */
ff.Player = function(url, target, rendererType, fixDisplay, isDev, timeout) {
  var targetDom = document.getElementById(target);
  
  // initDebug: use default value if the value of timeout/isDev is not set
  initDebug(
    timeout !== undefined ? timeout: window.DEV_TIMEOUT,
    isDev !== undefined ? isDev: window.IS_DEV
  );
  
  // EventDispatcher is static class
  ff.EventDispatcher.call(this);
  /** startAt: the time when Player.loading() called */
  this.startAt = null;
  /** frameAt: maybe meaningless variable */
  this.frameAt = null;
  
  /** context: contains lots of information like url/baseurl, dictionary of define objects, fps, rootMovieClip, etc... */
  this.context = new ff.Context();
  this.context.setUrl(url); // JSON url
  if (typeof rendererType === "object") {
    this.context.setRenderer(rendererType); // unsupported
  } else if (rendererType === "canvas")  {
    this.context.setRenderer(renderer.canvas); // canvas
  } else {
    this.context.setRenderer(renderer.svg);
  }
  /** screenSize: {width, height} */
  this.screenSize = {
    width: (targetDom.getAttribute('width')) ? targetDom.getAttribute('width'): 240,
    height: (targetDom.getAttribute('height')) ? targetDom.getAttribute('height'): 320
  };
  /** target: the id(string) of target dom */
  this.target = target;
  /** dom: the root element (replacing existing dom by setupScreen function) */
  this.dom = document.createElementNS("", "div");
  this.dom.setAttributeNS("", "id", this.target);
  this.fixDisplay = !!fixDisplay;
  this.intervalId = null;
};

inherits(ff.Player, ff.EventDispatcher);

/** first called function after initialized the FF.Player class */
ff.Player.prototype.play = function() {
  loadContent(this.context.url, this, this.onLoad); // loadContent is in core.js
  // after get JSON, call ff.Player.onLoad with this on this
  // TODO: I think this function is meanless
};

/** called after load JSON */
ff.Player.prototype.onLoad = function(request) {
  var obj = this;
  var data = eval("(" + request.responseText + ")");
  // setup context
  this.context.loadMeta(data.meta); // meta data(version, framerates, etc...)
  this.context.loadStage(data.ctls); // sprite data
  this.context.loadDictionary(data.dict); // dictionary(define objects): async loading
  this.updateScreenSize();
  // resize
  if (!this.fixDisplay) {
    window.onresize = function () {
      obj.updateScreenSize();
    };
  }
  this.startAt = Date.now(); // set start tiem
  this.loading();
};

/** replace existing dom by new element */
ff.Player.prototype.setupScreen = function() {
  // TODO: is this function needed? it seems to be called only once
  var oldDOM = document.getElementById(this.target);
  oldDOM.parentNode.replaceChild(this.dom, oldDOM);
  this.dom.appendChild(this.context.stage.dom);
};

/** loading svg items */
ff.Player.prototype.loading = function() {
  var obj = this;
  // waiting for async loading(XHR)
  if (this.context.isLoadComplete()) {
    console.log("load complete");
    this.frameAt = Date.now(); // set first frame time
    if (this.intervalId === null) {
      this.setupScreen();
      this.intervalId = setInterval(function (){obj.next()}, this.context.interval); // wake up!
    }
  } else {
    setTimeout(function (){obj.loading()}, this.context.interval);
  }
};

/** main loop: most important function */
ff.Player.prototype.next = function () {
  // this function is called by setInterval (I think this is bad solusion. change with setTimeout)
  // TODO: why?????
  try {
    if (Date.now() - this.frameAt > 10) {
      this.context.stage.advance();
      this.context.stage.processActionQueue();
      this.context.stage.display();
    }
  } catch (e) {
    console.error(e);
  }
  this.frameAt = Date.now();

  if (0 < DEV_TIMEOUT && DEV_TIMEOUT < Date.now() - this.startAt) {
    // debug stop
    clearInterval(this.intervalId);
    this.intervalId = null;
    console.info("Stop interval time "+DEV_TIMEOUT);
  }
};

/** update stage element size using meta data */
ff.Player.prototype.updateScreenSize = function() {
  var screenSize = this.screenSize;
  if (!this.fixDisplay) {
    if (document.documentElement && document.documentElement.clientWidth !== 0) {
      screenSize = { width: document.documentElement.clientWidth, height: document.documentElement.clientHeight };
    } else if (document.body) {
      screenSize = { width: document.body.clientWidth, height: document.body.clientHeight };
    }
  }
  this.context.ratio = this.context.stage.rect.width() / screenSize.width;
  if (this.context.stage.rect.height() / this.context.ratio > screenSize.height) {
    this.context.ratio = this.context.stage.rect.height() / screenSize.height;
  }
  this.context.stage.updateSize();
};

