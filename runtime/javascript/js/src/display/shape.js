
/**
 * @constructor
 */
display.Shape = function () {
  display.Display.call(this);
};

inherits(display.Shape, display.Display);

display.Shape.prototype.getRenderer = function () {
  return this.context.renderer.Shape;
};

display.Shape.prototype.clone = function() {
  var obj = new display.Shape();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.rect = this.rect;
  obj.url = this.url;
  obj.type = this.type;
  obj.content = this.content;
  return obj;
};

