
/**
 * @constructor
 */
display.Text = function () {
  display.Display.call(this);
};

inherits(display.Text, display.Display);

display.Text.prototype.getRenderer = function () {
  return this.context.renderer.Text;
};

display.Text.prototype.loadContent = function (content) {
  this.rect = new geom.Rect(content.meta.size);
  this.style = content.style;
  this.initialText = (content.text) ? content.text : "";
  this.text = this.initialText;
  this.variable = (content.variable) ? content.variable : false;
  this.updateText = false;
  display.Display.prototype.loadContent.call(this,content);
};

display.Text.prototype.clone = function() {
  var obj = new display.Text();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.rect = this.rect;
  obj.style = this.style;
  obj.text = "";
  obj.initialText = this.initialText;
  obj.variable = this.variable;
  obj.url = this.url;
  obj.type = this.type;
  return obj;
};

display.Text.prototype.advance = function() {
  if (this.variable !== false) {
    var new_text = this.text;
    if (this.variable.indexOf(":") > 0) {
      var t = this.variable.split(":");
      var c = this.parent.resolvePath(t[0]);
      new_text = (c) ? c.getVars(t[1]): "";
    } else {
      new_text = this.parent.getVars(this.variable);
    }
    if (new_text !== this.text) {
      this.text = new_text;
      this.updateText = true;
    }
  }
};

display.Text.prototype.display = function() {
  display.Display.prototype.display.call(this);
  if (this.updateText) {
    this.getRenderer().updateText(this);
  }
};

