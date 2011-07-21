// TODO load部分をLoaderてきな何かに移す

/**
 * @constructor
 */
ff.Context = function () {
  this.url = -1; // json's url
  this.baseurl = -1; // baseurl of other resources
  this.fps = -1; // fps
  this.interval = -1; // 1000 / fps // TODO: bad implementation because this is same as fps
  this.version = -1; // Flash version (maybe always 4)
  this.ratio = 20; // ratio between twips and pixels ( 1pixel == 20twips)
  this.dictionary = {}; // hash table for define objects(key is identifier)
  this.stage = -1; // Stage object
  this.renderer = {}; // rendering system. usually "svg", sometimes "canvas"
};

/** set json's url and baseurl */
ff.Context.prototype.setUrl = function (url) {
  this.url = url; // TODO: is this necessary?
  this.baseurl = url.slice(0,url.lastIndexOf("/"));
  // TODO: BUG: if there is no slash'/' such as "ABCDE" this code will return "ABCD" (because lastIndexOf returns -1)
};

/** set rendering system */
ff.Context.prototype.setRenderer = function(renderer) {
  this.renderer = renderer;
};

/** set metadata from json, and create Stage object */
ff.Context.prototype.loadMeta = function (meta) {
  this.fps = meta.fps; // requiring frame rate
  this.interval = 1000 / this.fps; // ms for setInterval TODO: this is not necessary
  this.version = meta.version; // flash version(4)
  this.stage = new display.Stage(null, this); // create stage
  this.stage.loadMeta(meta); // load meta for new stage
  console.log(this);
};

/** set define objects in dicrionary */
ff.Context.prototype.loadDictionary = function(defs) {
  var i = -1;
  var def;
  // create need-to-load list
  var loadList = [];
  for (i in defs) {
    def = defs[i];
    switch (def.type) {
      case "bitmap":
        this.addDisplayObject(def, new display.Bitmap); // Bitmap
        break;
      case "shape":
        this.addDisplayObject(def, new display.Shape); // Shape
        break;
      case "font":
        this.addDisplayObject(def, new display.Font); // Font
        break;
      case "text":
        this.addDisplayObject(def, new display.Text); // Text
        break;
      case "sprite":
        this.addDisplayObject(def, new display.Sprite); // Sprite
        break;
      default:
        console.warn("unknown type "+def.type);
        // load immediately
        loadContent(this.baseurl + "/" + defs[i].url, this, this.loadCallBack);
        break;
    }
    // if def has resource on web, and not exist in loadList
    if (def.url && -1 === loadList.indexOf(def.url)) {
      if (def.type === "bitmap") {
        // if object is bigmap, initialize tasks leaves on display object(maybe load from web)
        this.initBitmap_(def);
      } else {
        // add load list
        loadList.push(def.url);
      }
    }
  }
  for (i in loadList) {
    // load content from web
    loadContent(this.baseurl + "/" + loadList[i], this, this.loadCallBack);
  }
};

/** call from player on initializing */
ff.Context.prototype.loadStage = function(controls) {
  console.log("controls", controls);
  this.stage.setup("root", controls, this);
  this.stage.first();
  this.stage.loadControls(controls);
};

/** checking all define objects are successfully loaded */
ff.Context.prototype.isLoadComplete = function() {
  for (var id in this.dictionary) {
    if (this.dictionary[id].isLoaded === false) {
      console.log(id + " is not loaded ",this.dictionary[id]);
      return false;
    }
  }
  return true;
};

/** initialize display object by define object and put display object in dictionary */
ff.Context.prototype.addDisplayObject = function (def, displayObj) {
  displayObj.type = def.type;
  displayObj.url  = this.baseurl + "/" + def.url;
  displayObj.cid  = def.cid;
  this.dictionary[def.cid] = displayObj;
};

/** called after define object loaded */
ff.Context.prototype.loadCallBack = function(request) {
  var contentType = request.getResponseHeader("Content-Type");
  // resource is svg or json
  if (contentType.indexOf("svg") !== -1) {
    this.initSVG_(request.responseXML);
  } else {
    this.initObject_(eval("(" + request.responseText + ")"));
  }
};

/**
 * @private 
 * call DisplayObject's setup object with id and def
 */
ff.Context.prototype.setupDisplayObject_ = function (cid, content) {
  var displayObj = this.dictionary[cid];
  if (displayObj == undefined) {
    console.error("undefined: "+cid, content);
  }
  // set cid and this, and async-load content
  displayObj.setup(cid, content, this);
};

/**
 * @private 
 * just call setupDisplayObject with define
 */
ff.Context.prototype.initBitmap_ = function (obj) {
  this.setupDisplayObject_(obj.cid, obj);
};

/**
 * @private 
 * init svg object. argument "svg" is xml
 */
ff.Context.prototype.initSVG_ = function (svg) {
  var i, len = 0;
  var node;
  // enumrate defs
  var defs = svg.getElementsByTagName("defs");
  len = defs.item(0).childNodes.length;
  for (i = 0; i < len; ++i) {
    // find object id and call setup
    node = defs.item(0).childNodes.item(i);
    this.setupDisplayObject_(node.getAttributeNS(null, "id"), node);
  }
};

/**
 * @private 
 * called with evaled json
 */
ff.Context.prototype.initObject_ = function (objects) {
  var cid, obj;
  // {cid: {define}... }
  for (cid in objects) {
    obj = objects[cid];
    this.setupDisplayObject_(cid, obj);
  }
};

