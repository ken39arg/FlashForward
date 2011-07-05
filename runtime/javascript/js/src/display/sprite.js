
display.Sprite = function () {
  display.DisplayContainer.call(this);
};

inherits(display.Sprite, display.DisplayContainer);

display.Sprite.prototype.getRenderer = function () {
  return this.context.renderer.Sprite;
};

display.Sprite.prototype.loadContent = function(content) {
  display.DisplayContainer.prototype.loadContent.call(this,content)
  this.frameCount = content.meta.fcon;
  this.loadControls(content.ctls);
};

display.Sprite.prototype.clone = function() {
  var obj = new display.Sprite();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.frameCount = this.frameCount;
  obj.controls = this.controls;
  obj.url = this.url;
  obj.type = this.type;
  return obj;
};

display.Sprite.prototype.getRect = function() {
  var disp;
  var i, len = 0;
  if (this.rect == null) {
    this.rect = new geom.Rect;
    len = this.displayList.length;
    for (i = 0; i < len; ++i) {
      disp = this.displayList[i];
      if (disp.transform.matrix) {
        this.rect.addTransformd(disp.getRect(), disp.transform.matrix);
      } else {
        this.rect.add(disp.getRect());
      }
    }
  }
  return this.rect;
};

display.Sprite.prototype.updateDisplay = function () {
  this.rect = null;
  display.DisplayContainer.prototype.updateDisplay.call(this);
};

