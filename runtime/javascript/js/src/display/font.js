
/**
 * @constructor
 */
display.Font = function () {
  display.Display.call(this);
};

inherits(display.Font, display.Display);

display.Font.prototype.getRenderer = function () {
  return this.context.renderer.Font;
};

display.Font.prototype.clone = function() {
  var obj = new display.Font();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.rect = this.rect;
  obj.url = this.url;
  obj.type = this.type;
  return obj;
};

