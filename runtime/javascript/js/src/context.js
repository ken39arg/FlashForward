// TODO load部分をLoaderてきな何かに移す

/**
 * @constructor
 */
ff.Context = function () {
  this.url = -1;
  this.baseurl = -1;
  this.fps = -1;
  this.interval = -1;
  this.version = -1;
  this.ratio = 20;
  this.dictionary = {};
  this.stage = -1;
  this.renderer = {};
};

ff.Context.prototype.setUrl = function (url) {
  this.url = url;
  this.baseurl = url.slice(0,url.lastIndexOf("/"));
};

ff.Context.prototype.setRenderer = function(renderer) {
  this.renderer = renderer;
};

ff.Context.prototype.loadMeta = function (meta) {
  this.fps = meta.fps;
  this.interval = 1000 / this.fps;
  this.version = meta.version;
  this.stage = new display.Stage(null, this);
  this.stage.loadMeta(meta);
  console.log(this);
};

ff.Context.prototype.loadDictionary = function(defs) {
  var i = -1;
  var def;
  var loadList = [];
  for (i in defs) {
    def = defs[i];
    switch (def.type) {
      case "bitmap":
        this.addDisplayObject(def, new display.Bitmap);
        break;
      case "shape":
        this.addDisplayObject(def, new display.Shape);
        break;
      case "font":
        this.addDisplayObject(def, new display.Font);
        break;
      case "text":
        this.addDisplayObject(def, new display.Text);
        break;
      case "sprite":
        this.addDisplayObject(def, new display.Sprite);
        break;
      default:
        console.warn("unknown type "+def.type);
        loadContent(this.baseurl + "/" + defs[i].url, this, this.loadCallBack);
        break;
    }
    if (def.url && -1 === loadList.indexOf(def.url)) {
      if (def.type === "bitmap") {
        this.initBitmap_(def);
      } else {
        loadList.push(def.url);
      }
    }
  }
  for (i in loadList) {
    loadContent(this.baseurl + "/" + loadList[i], this, this.loadCallBack);
  }
};

ff.Context.prototype.loadStage = function(controls) {
  this.stage.setup("root", controls, this);
  this.stage.first();
  this.stage.loadControls(controls);
};

ff.Context.prototype.isLoadComplete = function() {
  for (var id in this.dictionary) {
    if (this.dictionary[id].isLoaded === false) {
      console.log(id + " is not loaded ",this.dictionary[id]);
      return false;
    }
  }
  return true;
};

ff.Context.prototype.addDisplayObject = function (def, displayObj) {
  displayObj.type = def.type;
  displayObj.url  = this.baseurl + "/" + def.url;
  displayObj.cid  = def.cid;
  this.dictionary[def.cid] = displayObj;
};

ff.Context.prototype.loadCallBack = function(request) {
  var contentType = request.getResponseHeader("Content-Type");
  if (contentType.indexOf("svg") !== -1) {
    this.initSVG_(request.responseXML);
  } else {
    this.initObject_(eval("(" + request.responseText + ")"));
  }
};

/**
 * @private 
 */
ff.Context.prototype.setupDisplayObject_ = function (cid, content) {
  var displayObj = this.dictionary[cid];
  if (displayObj == undefined) {
    console.error("undefined: "+cid, content);
  }
  displayObj.setup(cid, content, this);
};

/**
 * @private 
 */
ff.Context.prototype.initBitmap_ = function (obj) {
  this.setupDisplayObject_(obj.cid, obj);
};

/**
 * @private 
 */
ff.Context.prototype.initSVG_ = function (svg) {
  var i, len = 0;
  var node;
  var defs = svg.getElementsByTagName("defs");
  len = defs.item(0).childNodes.length;
  for (i = 0; i < len; ++i) {
    node = defs.item(0).childNodes.item(i);
    this.setupDisplayObject_(node.getAttributeNS(null, "id"), node);
  }
};

/**
 * @private 
 */
ff.Context.prototype.initObject_ = function (objects) {
  var cid, obj;
  for (cid in objects) {
    obj = objects[cid];
    this.setupDisplayObject_(cid, obj);
  }
};

