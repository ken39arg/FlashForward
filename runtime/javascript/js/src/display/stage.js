
/**
 * @constructor
 */
display.Stage = function () {
  display.DisplayContainer.call(this);
  this.id = "root";
}

inherits(display.Stage, display.DisplayContainer);

display.Stage.prototype.getRenderer = function () {
  return this.context.renderer.Stage;
};

display.Stage.prototype.clone = function() {
  throw "Stage is not created clone";
};

display.Stage.prototype.loadMeta = function(meta) {
  this.frameCount = meta.fcon;
  this.rect = new geom.Rect(meta.size);
  this.bgcolor = meta.bgcolor;
};

display.Stage.prototype.updateSize = function () {
  this.getRenderer().updateSize(this);
};

