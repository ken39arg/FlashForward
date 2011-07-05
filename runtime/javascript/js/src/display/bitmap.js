
/**
 * @constructor
 */
display.Bitmap = function () {
  display.Display.call(this);
};

inherits(display.Bitmap, display.Display);

display.Bitmap.prototype.getRenderer = function () {
  return this.context.renderer.Bitmap;
};

display.Bitmap.prototype.clone = function() {
  var obj = new display.Bitmap();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.rect = this.rect;
  obj.url = this.url;
  obj.type = this.type;
  obj.content = this.content;
  return obj;
};

