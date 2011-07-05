
/**
 * @constructor
 */
display.Display = function() {
  ff.EventDispatcher.call(this);
  this.parent = null;
  this.id = null;
  this.name = null;
  this.depth = 0;
  this.transform = null;
  this.rect = null;
  this.visible = null;
  this.node = null;
  this.isUpdate = false;
  this.clipDepth = 0;
  this.clipId =null
  this.isAppended = false;
  this.locked = false;
  this.isLoaded = false;
};

inherits(display.Display, ff.EventDispatcher);

display.Display.prototype.getRenderer = function () {
  return this.context.renderer.Display;
};

display.Display.prototype.setup = function(cid, content, context) {
  this.cid = cid;
  this.context = context;
  this.loadContent(content);
  return this;
};

display.Display.prototype.clone = function() {
  var obj = new display.Display();
  obj.cid = this.cid;
  obj.url = this.url;
  obj.type = this.type;
  obj.context = this.context;
  return obj;
};

display.Display.prototype.remove = function () {
  this.depth = 0;
  this.node = null;
  this.parent = null;
  this.name = null;
  this.rect = null;
  this.context = null;
  this.transform = null;
  this.cid = null;
  this.id = null;
  this.visible = null;
  this.isUpdate = false;
  this.isAppended = false;
  this.locked = false;
};

display.Display.prototype.loadContent = function(content) {
  this.getRenderer().load(this, content);
};

display.Display.prototype.advance = function() {
};

display.Display.prototype.processActionQueue = function() {
};

display.Display.prototype.display = function() {
  if (this.getRenderer().preRender(this)) {
    this.getRenderer().render(this);
  }
  this.isUpdate = false;
};

display.Display.prototype.added = function () {
};

display.Display.prototype.first = function() {
  this.getRenderer().first(this);
};

display.Display.prototype.updateTransform = function(matrix, cxform) {
  if (this.locked === true) {
    return;
  }
  if (!this.transform) {
    this.transform = {matrix:null,cxform:null};
  }
  if (matrix) {
    matrix = new geom.Matrix(matrix);
    if (matrix != this.transform.matrix) {
      this.transform.matrix = matrix;
      this.isUpdate = true;
    }
  }
  if (cxform) {
    cxform = new geom.Cxform(cxform);
    if (cxform != this.transform.cxform) {
      this.transform.cxform = cxform;
      this.isUpdate = true;
    }
  }
};

display.Display.prototype.buildId = function() {
  this.id = (this.parent) ? this.parent.id : "";
  this.id += "-" + this.cid + "-" +this.depth;
};

display.Display.prototype.getRect = function() {
  return this.rect;
};

display.Display.prototype.getProperty = function(prop) {
  switch (parseInt(prop)) {
    case 0://"_X":
      return this.transform.matrix.get_x() / 20;
    case 1://"_Y":
      return this.transform.matrix.get_y() / 20;
    case 2://"_xscale":
      return this.transform.matrix.get_xscale() * 100;
    case 3://"_yscale":
      return this.transform.matrix.get_yscale() * 100;
    case 4: //"_currentframe":
      return this.currentFrame + 1;
    case 5: //"_totalframes":
      return this.frameCount;
    case 6: //"_alpha":
      return this.transform.cxform.get_alpha();
    case 7: //"_visible":
      return (this.visible === "hidden") ? false: true;
    case 8: //"_width":
      var r = new geom.Rect
      r.addTransformd(this.getRect(), this.transform.matrix);
      return r.width() / 20;
    case 9: //"_height":
      var r = new geom.Rect
      r.addTransformd(this.getRect(), this.transform.matrix);
      return r.height() / 20;
    case 10: //"_rotation":
      return this.transform.matrix.get_rotation();
    //case 11: //"_target":
    //case 12: //"_framesloaded":
      break;
    case 13: //"_name":
      return this.name;
    //case 14: //"_droptarget":
    //case 15: //"_url":
    //case 16: //"_highquality":
    //case 17: //"_focusrect":
    //case 18: //"_soundbuftime":
    //case 19: //"_quality":
    //case 20: //"_xmouse":
    //case 21: //"_ymouse":
    default:
      break;
  }
  console.warn("get undefined property '"+prop+"'");
  return null;
};

display.Display.prototype.setProperty = function(prop, value) {
  this.isUpdate = true;
  this.locked = true;
  switch (parseInt(prop)) {
    case 0: //"_X":
      return this.transform.matrix.set_x(value * 20);
    case 1: //"_Y":
      return this.transform.matrix.set_y(value * 20);
    case 2: //"_xscale":
      return this.transform.matrix.set_xscale(value / 100);
    case 3: //"_yscale":
      return this.transform.matrix.set_yscale(value / 100);
    case 6: //"_alpha":
      return this.transform.cxform.set_alpha(value);
    case 7: //"_visible":
      return this.visible = (value != 0) ? "visible":"hidden";
    case 10: //"_rotation":
      return this.transform.matrix.set_rotation(value);
    //case 11: //"_target":
    //case 12: //"_framesloaded":
    case 13: //"_name":
      return this.name = value;
    //case 14: //"_droptarget":
    //case 15: //"_url":
    //case 16: //"_highquality":
    //case 17: //"_focusrect":
    //case 18: //"_soundbuftime":
    //case 19: //"_quality":
    //case 20: //"_xmouse":
    //case 21: //"_ymouse":
    default:
      break;
  }
  console.warn("set undefined property '"+prop+"' to '"+value+"'");
};

