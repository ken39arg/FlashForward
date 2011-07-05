/**
 * @constructor
 * @inherits ff.EventDispatcher
 */
ff.Player = function(url, target, rendererType, isDev, timeout) {
  var targetDom = document.getElementById(target);
  initDebug(
    timeout !== undefined ? timeout: window.DEV_TIMEOUT,
    isDev !== undefined ? isDev: window.IS_DEV
  );
  ff.EventDispatcher.call(this);
  this.startAt = null;
  this.frameAt = null;
  this.context = new ff.Context();
  this.context.setUrl(url);
  if (typeof rendererType === "object") {
    this.context.setRenderer(rendererType);
  } else if (rendererType === "canvas")  {
    this.context.setRenderer(renderer.canvas);
  } else {
    this.context.setRenderer(renderer.svg);
  }
  this.screenSize = {
    width: (targetDom.getAttribute('width')) ? targetDom.getAttribute('width'): 240,
    height: (targetDom.getAttribute('height')) ? targetDom.getAttribute('height'): 320
  };
  this.target = target;
  this.dom = document.createElementNS("", "div");
  this.dom.setAttributeNS("", "id", this.target);
  this.intervalId = null;
};

inherits(ff.Player, ff.EventDispatcher);

ff.Player.prototype.play = function() {
  loadContent(this.context.url, this, this.onLoad);
};

ff.Player.prototype.onLoad = function(request) {
  var obj = this;
  var data = eval("(" + request.responseText + ")");
  // setup context
  this.context.loadMeta(data.meta);
  this.context.loadStage(data.ctls);
  this.context.loadDictionary(data.dict);
  this.updateScreenSize();
  // resize
  window.onresize = function () {
    obj.updateScreenSize();
  };
  this.startAt = Date.now();
  this.loading();
};

ff.Player.prototype.setupScreen = function() {
  var oldDOM = document.getElementById(this.target);
  oldDOM.parentNode.replaceChild(this.dom, oldDOM);
  this.dom.appendChild(this.context.stage.dom);
};

ff.Player.prototype.loading = function() {
  var obj = this;
  if (this.context.isLoadComplete()) {
    console.log("load complete");
    this.frameAt = Date.now();
    if (this.intervalId === null) {
      this.setupScreen();
      this.intervalId = setInterval(function (){obj.next()}, this.context.interval);
    }
  } else {
    setTimeout(function (){obj.loading()}, this.context.interval);
  }
};

ff.Player.prototype.next = function () {
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
    clearInterval(this.intervalId);
    this.intervalId = null;
    console.info("Stop interval time "+DEV_TIMEOUT);
  }
};

ff.Player.prototype.updateScreenSize = function() {
  var screenSize = this.screenSize;
  if (document.documentElement && document.documentElement.clientWidth !== 0) {
    screenSize = { width: document.documentElement.clientWidth, height: document.documentElement.clientHeight };
  } else if (document.body) {
    screenSize = { width: document.body.clientWidth, height: document.body.clientHeight };
  }
  this.context.ratio = this.context.stage.rect.width() / screenSize.width;
  if (this.context.stage.rect.height() / this.context.ratio > screenSize.height) {
    this.context.ratio = this.context.stage.rect.height() / screenSize.height;
  }
  this.context.stage.updateSize();
};

