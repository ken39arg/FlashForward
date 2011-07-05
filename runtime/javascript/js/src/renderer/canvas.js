var SVG_TYPE_PATH  = 1;
var SVG_TYPE_TEXT  = 2;
var SVG_TYPE_GROUP = 3;
var SVG_TYPE_USE   = 4;

var TEXT_ALIGN_LEFT   = 0;
var TEXT_ALIGN_CENTER = 1;
var TEXT_ALIGN_RIGHT  = 2;

var textAlines = {"left": TEXT_ALIGN_LEFT, "center": TEXT_ALIGN_CENTER, "right": TEXT_ALIGN_RIGHT};

var regexpSVGID = /\(\#(.*)\)/;
var regexpMLQZ = /[MLQZ]/;
var regexpColor1 = /([0-9A-Fa-f]{6})/;
var regexpColor2 = /rgb\(([0-9,\.\-]*)\)/;

var canvas = {};

canvas.displaySort = function (a, b) {
  return a.depth - b.depth;
};

canvas.Display = {
  load: function(obj, content) {
    obj.isLoaded = true;
  },
  first: function(obj) {
  },
  preRender: function(obj) {
    if (obj.visible === "hidden") {
      return false
    }
    return true;
  },
  render: function(obj) {
  },
  remove: function(obj) {
  }
};

canvas.Shape = copyObject(canvas.Display);

canvas.Shape.load = function(obj, content) {
  obj.rect = new geom.Rect(content.getAttribute("viewBox"));
  obj.content = canvas.parseSvg(content, {});
  obj.isLoaded = true;
};

canvas.Shape.render = function(obj) {
  var ctx = obj.context.stage.ctx;
  var is_m = (obj.transform.matrix !== null) ? true: false;
  var is_c = (obj.transform.cxform !== null) ? true: false;
  var isCLipLayer = (obj.clipDepth > 0) ? true: false;

  if (is_m) ctx.addMatrix(obj.transform.matrix);
  if (is_c) ctx.addCxform(obj.transform.cxform);

  canvas.renderShape(obj.content, ctx, obj.context, isCLipLayer);

  if (is_m) ctx.removeMatrix();
  if (is_c) ctx.removeCxform();
};

canvas.Bitmap = copyObject(canvas.Display);

canvas.Bitmap.load = function(obj, content) {
  obj.url = obj.context.baseurl + "/" + content.url;
  obj.rect = new geom.Rect([0,0,content.width,content.height]);
  var image = new Image();
  image.src = obj.url
  obj.content = image;
  // onload 代替
  var check = function () {
    if (obj.content.width > 0 || obj.content.height > 0) {
      obj.isLoaded = true;
    } else {
      setTimeout(check, 0)
    }
  }
  setTimeout(check,0)
};

canvas.Font = copyObject(canvas.Display);

canvas.Font.load = function(obj, content) {
  var fontName = content.firstChild.getAttribute("font-family")
  var id = content.getAttribute("id")
  var head = document.getElementsByTagName('head');
  var style = document.createElement("style")
  style.innerHTML = "@font-face{" +
                    "font-family:'"+fontName+"';" +
                    "src:url("+obj.url+"#"+id+") format(\"svg\");" +
                    "}"
  head.item(0).appendChild(style);
  obj.isLoaded = true;
};

canvas.Text = copyObject(canvas.Display);

canvas.Text.first = function(obj) {
  var textStyle = {};
  if (obj.style["word-wrap"]) console.warn("word-wrap is no support on canvas");
  if (obj.style["multiline"]) console.warn("multiline is no support on canvas");
  if (obj.style["border"]) console.warn("border is not implement on canvas");
  if (obj.style["left-mergin"]) console.warn("left-mergin is not implement on canvas");
  if (obj.style["right-mergin"]) console.warn("right-mergin is not implement on canvas");
  if (obj.style["indent"]) console.warn("indent is not implement on canvas");
  if (obj.style["leading"]) console.warn("leading is not implement on canvas");

  textStyle.italic = (obj.style["italic"]) ? true: false;
  textStyle.bold = (obj.style["bold"]) ? true: false;
  textStyle.font = (obj.style["font"]) ? "'"+obj.style["font"]+"'" : false;
  textStyle.align = textAlines[(obj.style["align"]) ? obj.style["align"] : "left"];
  textStyle.color = canvas.parseColor(obj.style["color"], obj.style["opacity"]);
  textStyle.size = (obj.style["size"]) ? obj.style["size"] : false;

  obj.textStyle = textStyle;

  obj.text = obj.initialText;
};

canvas.Text.updateText = function(obj) {};

canvas.Text.render = function(obj) {
  var ctx = obj.context.stage.ctx;
  var is_m = (obj.transform.matrix !== null) ? true: false;
  var is_c = (obj.transform.cxform !== null) ? true: false;

  if (is_m) ctx.addMatrix(obj.transform.matrix);
  if (is_c) ctx.addCxform(obj.transform.cxform);

  ctx.renderEditText(obj.text, obj.rect, obj.textStyle);

  if (is_m) ctx.removeMatrix();
  if (is_c) ctx.removeCxform();
};

canvas.DisplayContainer = copyObject(canvas.Display);

canvas.DisplayContainer.rendererChild = function(obj, child, prevChild) {
  if (obj.clipLayer.length > 0) {
    if (child.clipDepth > 0) {
      obj.context.stage.ctx.save(child.clipId);
      obj.isClip = false;
    } else if (child.clipId != null && obj.isClip !== true) {
      obj.context.stage.ctx.clip(child.clipId);
      obj.isClip = true;
    } else if (child.clipId != null && child.clipDepth == 0 && obj.isClip === true) {
      obj.context.stage.ctx.restore();
      obj.isClip = false;
    }
  }
};

canvas.DisplayContainer.removeChild = function(obj, child) {};

canvas.Sprite = copyObject(canvas.DisplayContainer);

canvas.Sprite.preRender = function (obj) {
  if (obj.visible === "hidden") {
    return false;
  }
  if (obj.transform.matrix !== null) {
    obj.context.stage.ctx.addMatrix(obj.transform.matrix);
  }
  if (obj.transform.cxform !== null) {
    obj.context.stage.ctx.addCxform(obj.transform.cxform);
  }
  return true;
};

canvas.Sprite.render = function (obj) {
  if (obj.transform.matrix !== null) {
    obj.context.stage.ctx.removeMatrix();
  }
  if (obj.transform.cxform !== null) {
    obj.context.stage.ctx.removeCxform();
  }
  if (obj.isClip === true) {
    obj.context.stage.ctx.restore();
  }
};

canvas.Stage = copyObject(canvas.DisplayContainer);

canvas.Stage.load = function (obj, content) {
  var wrapper = document.createElement("div");

  wrapper.setAttribute("style", "background-color:"+obj.bgcolor);

  obj.dom = wrapper;
};

canvas.Stage.first = function (obj) {
  var canvas = document.createElement("canvas");
  canvas.id =  obj.id;
  canvas.width = obj.rect.width() / obj.context.ratio;
  canvas.height = obj.rect.height() / obj.context.ratio;
  obj.dom.appendChild(canvas);

  obj.isUpdateSize = true;

  obj.canvas = canvas;
  obj.ctx = new Ctx(canvas);
};

canvas.Stage.preRender = function (obj) {
  if (obj.isUpdateSize) {
    obj.canvas.width = obj.rect.width() / obj.context.ratio;
    obj.canvas.height = obj.rect.height() / obj.context.ratio;
    obj.isUpdateSize = false;
  }
  obj.ctx.reset(obj.context.ratio);
  return true;
};

canvas.Stage.updateSize = function (obj) {
  obj.isUpdateSize = true;
  //obj.canvas.width = obj.rect.width() / obj.context.ratio;
  //obj.canvas.height = obj.rect.height() / obj.context.ratio;
};

// Parser
canvas.parseSvg = function (node, tmp) {
  switch (node.tagName) {
    case "use": // image
      var id = node.getAttribute('xlink:href');
      return {
        type: SVG_TYPE_USE,
        use: id.replace("#",""),
        matrix: new geom.Matrix(node.getAttribute('transform'))
      }
    case "path": // single
      var id;
      var p = {
        type: SVG_TYPE_PATH,
        fill: false,
        stroke: false,
        data: []
      }
      p.fill = canvas.parseColor(node.getAttribute('fill'),
                                   node.getAttribute('fill-opacity'));
      if (typeof p.fill === "string") {
        if ((id = regexpSVGID.exec(p.fill)) !== null && tmp.hasOwnProperty(id[1])) {
          p.grad = tmp[id[1]];
        }
      }
      p.stroke = canvas.parseColor(node.getAttribute('stroke'),
                                     node.getAttribute('stroke-opacity'));
      if (p.stroke !== false) {
        p.strokeWidth = node.getAttribute('stroke-width');
      }
      p.data = canvas.parsePathData(node.getAttribute('d'));

      return p;

    case "g": // group
      var g = [];
      var len = node.childNodes.length;
      var i = 0;
      var d;
      for (i=0;i<len;++i) {
        d = canvas.parseSvg(node.childNodes[i], tmp);
        if (d !== false) g.push(d);
      }
      return {
        type: SVG_TYPE_GROUP,
        item: g
      };

    case "linearGradient": // single
    case "radialGradient": // single
      var id = node.getAttribute('id');
      var sm = node.getAttribute('spreadMethod');
      var g = [];
      var len = node.childNodes.length;
      var i = 0;
      var d;
      for (i=0;i<len;++i) {
        d = node.childNodes[i];
        g.push({
          color:  d.getAttribute('stop-color'),
          offset: parseInt(d.getAttribute('offset')) / 100
        });
      }
      tmp[id] = {
        sm: sm,
        item: g
      };
      return false

    case "text": // single
      var id  = node.getAttribute('id');
      var fontSize = node.getAttribute('font-size');
      var fontName = node.getAttribute('font-family');
      var fill = canvas.parseColor(node.getAttribute('fill'));
      var texts = [];
      var text;
      var len = node.childNodes.length;
      var i = 0;
      var x,y=0;
      var font = "";
      for (i=0;i<len;++i) {
        text = node.childNodes[i];
        //x = node.getAttribute("x").split(" ")[0]
        //y = node.getAttribute("y")
        texts.push({
          x: text.getAttribute("x").split(" ")[0],
          y: text.getAttribute("y"),
          fontName: (text.hasAttribute("font-family")) ? text.getAttribute('font-family'):fontName,
          fontSize: (text.hasAttribute("font-size")) ? text.getAttribute('font-size'):fontSize,
          fill: (text.hasAttribute("fill")) ? canvas.parseColor(text.getAttribute('fill')):fill,
          text: text.textContent
        });
      }
      return {
        type: SVG_TYPE_TEXT,
        texts: texts
      };

    default:
      console.warn("unknown type "+node.tagName, node);
      return false;
  }
};

canvas.parsePathData = function(data){
  var ret = [];
  var d = data.split(" ");
  var p, c;
  while ((p = d.shift()) !== undefined) {
    if (regexpMLQZ.test(p)) {
      c = p;
    } else {
      d.unshift(p);
    }
    switch (c) {
      case "M":
      case "L":
        ret.push([c, Number(d.shift()), Number(d.shift())]);
        break;
      case "Q":
        ret.push([c, Number(d.shift()), Number(d.shift()), Number(d.shift()), Number(d.shift())]);
        break;
      case "Z":
        ret.push([c])
        break;
    }
  }
  return ret;
};

canvas.parseColor = function(color, opacity){
  var ret = false;
  var c;
  if (color === undefined || color === null || color === "none") {
    return false;
  } else if ((c = regexpColor1.exec(color)) !== null) {
    ret = [
      parseInt("0x" + c[1].slice(0,2)),
      parseInt("0x" + c[1].slice(2,4)),
      parseInt("0x" + c[1].slice(4,6))
    ];
  } else if ((c = regexpColor2.exec(color)) !== null) {
    ret = c[1].split(",").map(parseInt);
  } else {
    return color;
  }
  if (opacity !== undefined && opacity !== null && opacity !== false) {
    ret.push(((Number(opacity) * 100 + 0.5) | 0) / 100);
  }
  return ret;
};

// render
canvas.renderShape = function(content, ctx, context, isCLipLayer) {
  switch (content.type) {
    case SVG_TYPE_USE:
      var imgObj = context.dictionary[content.use];
      ctx.drawImage(imgObj.content, content.matrix);
      break
    case SVG_TYPE_PATH:
      var len = content.data.length;
      var path;
      var f;
      ctx.setup();
      for (var i = 0; i < len; ++i) {
        path = content.data[i];
        f = path[0];
        ctx[f](path.slice(1));
      }
      if (isCLipLayer) {
        break;
      }
      if (content.fill) {
        ctx.fill(content.fill);
      }
      if (content.stroke) {
        ctx.stroke(content.stroke, content.strokeWidth);
      }
      break
    case SVG_TYPE_GROUP:
      var len = content.item.length;
      for (var i = 0; i < len; ++i) {
        canvas.renderShape(content.item[i], ctx, context, isCLipLayer);
      }
      break
    case SVG_TYPE_TEXT:
      var len = content.texts.length;
      ctx.setup();
      for (var i = 0; i < len; ++i) {
        ctx.renderStaticText(content.texts[i]);
      }
      break
    default:
      console.warn("unknown content type "+content);
  }
};

/**
 * @constructor
 */
var Ctx = function (canvas) {
  this.canvas = canvas;
  this.ctx = canvas.getContext("2d");
  this.matrix = [];
  this.state = [];
  this._matrix = null;
};

Ctx.prototype.reset = function(ratio) {
  if (this.state.length > 0) {
    while (this.state.length > 0) {
      this.restore();
    }
  }
  this.ctx.clearRect(0,0,this.width,this.height);
  this.ctx.lineCap = "round";
  this.ctx.lineJoin = "round";
  this.width = this.canvas.width;
  this.height = this.canvas.height;
  this.scale = 1/ratio;
  this.matrix = [new geom.Matrix([this.scale,0,0,this.scale,0,0])];
  this.cxform = [];
};

Ctx.prototype.addMatrix = function (matrix) {
  this.matrix.push(matrix);
};

Ctx.prototype.removeMatrix = function () {
  this.matrix.pop();
};

Ctx.prototype.addCxform = function(c) {
  this.cxform.push(c);
};

Ctx.prototype.removeCxform = function() {
  this.cxform.pop();
};

Ctx.prototype.setup = function() {
  this._matrix = new geom.Matrix;
  var len = this.matrix.length;
  for (var i = 0;i < len;++i) {
    this._matrix = this._matrix.add(this.matrix[i]);
  }
  this.ctx.beginPath();
};

Ctx.prototype.transform = function(x, y) {
  var p = this._matrix.transform(x, y);
  return {"x": (p.x + 0.5) | 0, "y": (p.y + 0.5) | 0};
};

Ctx.prototype.colorTransform = function(color) {
  var c = this._colorTransform(color);
  var r,g,b,a = 0;
  r = (c.r + 0.5) | 0;
  g = (c.g + 0.5) | 0;
  b = (c.b + 0.5) | 0;
  a = ((c.a * 100 + 0.5) | 0) / 100;
  return (a === 1) ?  "rgb("+r+","+g+","+b+")" : "rgba("+r+","+g+","+b+","+a+")";
};

Ctx.prototype._colorTransform = function(color) {
  var c = {r:color[0], g:color[1], b:color[2], a: 1.0};
  if (color.length > 3) {
    c.a = color[3];
  }
  var i = this.cxform.length;
  for (;i>0;--i) {
    c = this.cxform[i-1].transform(c.r, c.g, c.b, c.a);
  }
  return c;
};

Ctx.prototype.M = function(arg) {
  var d = this.transform(arg[0], arg[1]);
  this.ctx.moveTo(d.x, d.y);
};

Ctx.prototype.L = function(arg) {
  var d = this.transform(arg[0], arg[1]);
  this.ctx.lineTo(d.x, d.y);
};

Ctx.prototype.Q = function(arg) {
  var c = this.transform(arg[0], arg[1]);
  var d = this.transform(arg[2], arg[3]);
  this.ctx.quadraticCurveTo(c.x, c.y, d.x, d.y);
};

Ctx.prototype.Z = function(arg) {
  this.ctx.closePath();
};

Ctx.prototype.fill = function(color) {
  var c = "";
  if (typeof color === "string") {
    c = color;
  } else if (color.length > 2) {
    c = this.colorTransform(color);
  }
  this.ctx.fillStyle = c;
  this.ctx.fill();
};

Ctx.prototype.stroke = function(color, width) {
  var c = "";
  if (typeof color === "string") {
    c = color;
  } else if (color.length > 2) {
    c = this.colorTransform(color);
  }
  this.ctx.lineWidth = width * this.scale;
  this.ctx.strokeStyle = c;
  this.ctx.stroke();
};

Ctx.prototype.drawImage = function (image, matrix) {
  this.addMatrix(matrix);
  this.setup();
  var dxy = this.transform(0, 0);
  var dw = image.width * this._matrix.get_xscale();
  var dh = image.height * this._matrix.get_yscale();
  var ba = this.ctx.globalAlpha;
  var c = this._colorTransform([255,255,255,1]);
  if (c.a < 1) {
    this.ctx.globalAlpha = c.a;
  }
  this.ctx.drawImage(image, dxy.x, dxy.y, dw, dh);
  this.removeMatrix();
  this.ctx.globalAlpha = ba;
};

Ctx.prototype.renderStaticText = function (textObj) {
  var d = this.transform(textObj.x, textObj.y);
  var font = ((textObj.fontSize * this.scale + 0.5) | 0) + "px '" + textObj.fontName + "'";

  this.ctx.textAlign = "left";
  this.ctx.fillStyle = this.colorTransform(textObj.fill);
  this.ctx.font = font;
  this.ctx.fillText(textObj.text, d.x, d.y);
};

Ctx.prototype.renderEditText = function (text, rect, textStyle) {
  var x, y = 0
  var d = {};
  var font = [];
  switch (textStyle.align) {
  case TEXT_ALIGN_LEFT:
    x = rect.xmin;
    break;
  case TEXT_ALIGN_CENTER:
    x = rect.width() / 2 + rect.xmin;
    break;
  case TEXT_ALIGN_RIGHT:
    x = rect.xmax;
    break;
  }
  y = rect.ymin + textStyle.size;
  this.setup();
  d = this.transform(x, y)
  if (textStyle.italic) font.push("italic");
  if (textStyle.bold) font.push("italic");
  if (textStyle.size) font.push(((textStyle.size * this.scale + 0.5) | 0) + "px");
  if (textStyle.font) font.push(textStyle.font);
  this.ctx.textAlign = textStyle.align;
  this.ctx.fillStyle = this.colorTransform(textStyle.color);
  this.ctx.font = font.join(" ");
  this.ctx.fillText(text, d.x, d.y);
};

Ctx.prototype.save = function(id) {
  this.ctx.save();
  this.state.push(id);
};

Ctx.prototype.clip = function(id) {
  this.ctx.clip();
};

Ctx.prototype.restore = function() {
  this.state.pop();
  this.ctx.restore();
};

canvas.Ctx = Ctx;

renderer.canvas = canvas;

