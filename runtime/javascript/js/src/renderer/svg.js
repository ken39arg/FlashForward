var SVG = "http://www.w3.org/2000/svg"
var XLINK = "http://www.w3.org/1999/xlink"

var svg = {}
svg.displaySort = function (a, b) {
  return b.depth - a.depth;
}

svg.Display = {
  load: function(obj, content) {
    obj.isLoaded = true;
  },
  first: function(obj) {
    var node = document.createElementNS(SVG, "use");
    node.setAttributeNS(XLINK, "xlink:href", "#" + obj.cid);
    if (obj.name) {
      node.setAttributeNS(null, "class", obj.name);
    }
    if (obj.clipDepth) {
      var wrapper = document.createElementNS(SVG, "clipPath");
      wrapper.setAttributeNS(null, "id", obj.id);
      wrapper.appendChild(node);
      obj.node = wrapper;
    } else {
      node.setAttributeNS(null, "id", obj.id);
      obj.node = node;
    }
  },
  preRender: function (obj) {
    return true;
  },
  render: function(obj) {
    if (obj.isUpdate === false) {
      return
    }
    var node = (obj.clipDepth === 0) ? obj.node: obj.node.childNodes[0]
    if (obj.visible !== null) {
      node.setAttributeNS(null, "visibility", obj.visible);
    }
    if (obj.visible !== "hidden") {
      var transform = obj.transform;
      if (transform.matrix) {
        node.setAttributeNS(null, "transform", transform.matrix.toString());
      }
      if (transform.cxform) {
        node.setAttributeNS(null, "opacity", transform.cxform.ma);
      }
    }
  },
  remove: function (obj) {
  }
}

svg.Shape = copyObject(svg.Display)
svg.Shape.load = function (obj, content) {
  var node = document.importNode(content, true);
  obj.context.stage.defs.appendChild(node);
  obj.rect = new geom.Rect(node.getAttribute("viewBox"));
  obj.isLoaded = true;
}

svg.Bitmap = copyObject(svg.Display)
svg.Bitmap.load = function (obj, content) {
  obj.url = obj.context.baseurl + "/" + content.url;
  obj.rect = new geom.Rect([0,0,content.width,content.height]);

  var node = document.createElementNS(SVG, "image");
  node.setAttributeNS(null, "id", obj.cid);
  node.setAttributeNS(null, "width", obj.rect.width());
  node.setAttributeNS(null, "height", obj.rect.height());
  node.setAttributeNS(XLINK, "xlink:href", obj.url);
  obj.context.stage.defs.appendChild(node);
  obj.isLoaded = true;
}

svg.Font = copyObject(svg.Shape)

svg.Text = copyObject(svg.Display)
svg.Text.first = function(obj) {
  var node = document.createElementNS(SVG, "text");
  node.setAttributeNS(null, "id", obj.id);
  if (obj.name) {
    node.setAttributeNS(null, "class", obj.name);
  }
  if (obj.style["align"]) {
    switch (obj.style["align"]) {
    case "left":
      node.setAttributeNS(null, "text-anchor", "start");
      node.setAttributeNS(null, "x", obj.rect.xmin)
      break;
    case "center":
      node.setAttributeNS(null, "text-anchor", "middle");
      node.setAttributeNS(null, "x", obj.rect.width() / 2 + obj.rect.xmin)
      break;
    case "right":
      node.setAttributeNS(null, "text-anchor", "end");
      node.setAttributeNS(null, "x", obj.rect.xmax)
      break;
    }
  }
  node.setAttributeNS(null, "y", obj.style["size"] + obj.rect.ymin)
  if (obj.style["word-wrap"]) console.warn("word-wrap is no support on SVG");
  if (obj.style["multiline"]) console.warn("multiline is no support on SVG");
  if (obj.style["border"]) console.warn("border is not implement on SVG");
  if (obj.style["size"]) node.setAttributeNS(null, "font-size", obj.style["size"]);
  if (obj.style["color"]) node.setAttributeNS(null, "fill", obj.style["color"]);
  if (obj.style["opacity"]) node.setAttributeNS(null, "opacity", Number(obj.style["opacity"]));
  if (obj.style["left-mergin"]) console.warn("left-mergin is not implement on SVG");
  if (obj.style["right-mergin"]) console.warn("right-mergin is not implement on SVG");
  if (obj.style["indent"]) console.warn("indent is not implement on SVG");
  if (obj.style["leading"]) console.warn("leading is not implement on SVG");
  if (obj.style["font"]) node.setAttributeNS(null, "font-family", obj.style["font"]);
  if (obj.style["italic"]) node.setAttributeNS(null, "font-style", "italic");
  if (obj.style["bold"]) node.setAttributeNS(null, "font-weight", "bold");
  obj.node = node;
  obj.text = obj.initialText
  obj.updateText = true;
}
svg.Text.updateText = function(obj) {
  if (obj.text == "") {
    return;
  }
  if (obj.node.firstChild) {
    obj.node.replaceChild(document.createTextNode(obj.text), obj.node.firstChild);
  } else {
    obj.node.appendChild(document.createTextNode(obj.text));
  }
}

svg.DisplayContainer = copyObject(svg.Display)
svg.DisplayContainer.rendererChild = function(obj, child, prevChild) {
  var node;
  if (child.clipId) {
    if (!obj.clipNode.hasOwnProperty(child.clipId)) {
      node = document.createElementNS(SVG, "g");
      node.setAttributeNS(null, "clip-path", "url(#"+child.clipId+")");
      node.setAttributeNS(null, "id", "c_"+child.clipId);
      if (prevChild) {
        obj.node.insertBefore(node, prevChild.node);
      } else {
        obj.node.appendChild(node);
      }
      obj.clipNode[child.clipId] = node;
    } else {
      node = obj.clipNode[child.clipId];
    }
  } else {
    node = obj.node;
  }
  if (child.isAppended === false) {
    if (prevChild && prevChild.clipId === child.clipId) {
      node.insertBefore(child.node, prevChild.node);
    } else {
      node.appendChild(child.node);
    }
  }
}
svg.DisplayContainer.removeChild = function(obj, child) {
  if (child.node) {
    obj.node.removeChild(child.node);
  }
}
svg.DisplayContainer.first = function(obj) {
  obj.node = document.createElementNS(SVG, (obj.clipDepth) ? "clipPath": "g");
  obj.node.setAttributeNS(null, "id", obj.id);
  if (obj.name) {
    obj.node.setAttributeNS(null, "class", obj.name);
  }
  obj.clipNode = {};
}

svg.Sprite = copyObject(svg.DisplayContainer)

svg.Stage = copyObject(svg.DisplayContainer)
svg.Stage.load = function (obj, content) {
  obj.dom = document.createElementNS(SVG, "svg");

  obj.defs = document.createElementNS(SVG, "defs");
  obj.dom.appendChild(obj.defs);

  var bgcolor = document.createElementNS(SVG, "rect");
  bgcolor.setAttributeNS(null, "x", 0);
  bgcolor.setAttributeNS(null, "y", 0);
  bgcolor.setAttributeNS(null, "width", "100%");
  bgcolor.setAttributeNS(null, "height", "100%");
  bgcolor.setAttributeNS(null, "fill", obj.bgcolor);
  obj.dom.appendChild(bgcolor);
  obj.isLoaded = true;
}
svg.Stage.first = function (obj) {
  obj.node = document.createElementNS(SVG, "g");
  obj.node.setAttributeNS(null, "id", obj.id);
  obj.dom.appendChild(obj.node);

  obj.clipNode = {};
}
svg.Stage.updateSize = function (obj) {
  obj.dom.setAttributeNS(null, "width", obj.rect.width() / obj.context.ratio);
  obj.dom.setAttributeNS(null, "height", obj.rect.height() / obj.context.ratio);
  obj.node.setAttributeNS(null, "transform", "scale(" + (1 / obj.context.ratio) + ")");
}


renderer.svg = svg;
