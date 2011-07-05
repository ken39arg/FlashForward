
/**
 * @constructor
 */
display.DisplayContainer = function () {
  display.Display.call(this);
  this.currentFrame = 0;
  this.renderFrame = -1;
  this.frameCount = 0;
  this.playing = true;
  this.vars = {};
  this.clipLayer = [];
  this.actionQueue = [];
  this.displayList = [];
};

inherits(display.DisplayContainer, display.Display);

display.DisplayContainer.prototype.clone = function() {
  var obj = new display.DisplayContainer();
  obj.cid = this.cid;
  obj.context = this.context;
  obj.controls = this.controls;
  return obj;
};

display.DisplayContainer.prototype.remove = function (parent) {
  this.clipLayer = [];
  this.displayList = [];
  this.actionQueue = [];
  display.Display.prototype.remove.call(this, parent);
};

display.DisplayContainer.prototype.loadControls = function (controls) {
  this.controls = controls;
  this.frameCount = controls.length;
};

display.DisplayContainer.prototype.added = function () {
};

display.DisplayContainer.prototype.advance = function () {
  var i, len = 0;
  var displays = this.displayList;
  if (this.playing) {
    if (this.renderFrame !== this.currentFrame) {
      this.updateDisplay();
    }
  }
  len = displays.length;
  for (i=0; i<len; ++i) {
    displays[i].advance();
  }
};

display.DisplayContainer.prototype.processActionQueue = function() {
  var avm;
  var i, len = 0;
  var displays = this.displayList;
  while ((avm = this.actionQueue.shift())) {
    avm.execute();
  }
  len = displays.length;
  for (i=0; i<len; ++i) {
    displays[i].processActionQueue();
  }
};

display.DisplayContainer.prototype.display = function() {
  var renderer = this.getRenderer();
  var displays = this.displayList;
  var disp;
  var i, len = 0;
  if (renderer.preRender(this)) {
    len = displays.length;
    for (i=0; i<len; ++i) {
      disp = displays[i];

      if (disp.isAppended === false) {
        disp.first();
      }

      renderer.rendererChild(this, disp, displays[i-1]);

      disp.display();

      disp.isAppended = true;
    }
    renderer.render(this)
  }
  this.isUpdate = false;
  if (this.playing) {
    this.nextFrame();
  }
};

display.DisplayContainer.prototype.play = function () {
  this.playing = true;
};

display.DisplayContainer.prototype.stop = function () {
  this.playing = false;
};

display.DisplayContainer.prototype.nextFrame = function() {
  ++this.currentFrame;
  if (this.currentFrame >= this.frameCount) {
    this.currentFrame = 0;
  }
};

display.DisplayContainer.prototype.previousFrame = function() {
  --this.currentFrame;
  if (this.currentFrame < 0) {
    this.currentFrame = 0;
  }
};

display.DisplayContainer.prototype.gotoFrame = function (frame, playing) {
  var b_frame = this.currentFrame;
  this.currentFrame = (isFinite(frame))
                     ? (frame > 1 ? frame - 1 : 0)
                     : this._getIndexByLabel(frame);
  if (this.controls.length < this.currentFrame) {
    this.currentFrame = this.controls.length - 1;
  }
  this.playing = playing;
  var control =this.controls[this.currentFrame];
  if (b_frame !== this.currentFrame) {
    var newDisplays = control.d;
    var oldDisplays = this.displayList;
    var oldDisplay, newDisplay;
    var f=false;
    var i, j, len, len2 = 0;
    var removeList = [];
    len = oldDisplays.length;
    for (i=0;i<len;++i) {
      f=false;
      oldDisplay = oldDisplays[i];
      len2 = newDisplays.length;
      for (j=0;j<len2;++j) {
        newDisplay = newDisplays[j];
        if (newDisplay.dp == oldDisplay.depth && newDisplay.cid == oldDisplay.cid) {
          f = true;
          break;
        }
      }
      if (f === false) {
        removeList.push(oldDisplay.depth);
      }
    }
    len = removeList.length;
    for (i=0;i<len;++i) {
      this.removeChildAt(removeList[i]);
    }
    this._displayDisplay(control.d);
    this._actionDisplay(control.act);

    var displays = this.displayList;
    len = displays.length;
    for (i=0;i<len;++i) {
      displays[i].advance();
    }
  }
};

display.DisplayContainer.prototype.updateDisplay = function () {
  var control = this.controls[this.currentFrame];

  this._removesDisplay(control.rm);
  this._displayDisplay(control.d);
  this._actionDisplay(control.act);
};

display.DisplayContainer.prototype._removesDisplay = function (removes) {
  // remove
  if (removes === undefined) {
    return;
  }
  var len = removes.length;
  for (var i = 0; i< len; ++i) {
    this.removeChildAt(removes[i]);
  }
};

display.DisplayContainer.prototype._displayDisplay = function (display) {
  var i, j, len, len2 = 0;
  var p, disp,def, cl;
  this.renderFrame = this.currentFrame;
  len = display.length;
  for (i = 0; i < len; ++i) {
    p = display[i];
    disp = this.getChildAt(p.dp);
    if (disp && disp.cid !== p.cid) {
      this.removeChildAt(p.dp);
    }
    if (!disp || disp.context == null) {
      def = this.context.dictionary[p.cid];
      if (def === undefined) {
        console.warn("not found define "+p.cid);
        continue;
      }
      disp = def.clone();
      if (disp.context == null) {
        console.error(disp, p);
        continue;
      }
    }
    if (p.name) {
      disp.name = p.name;
    }
    disp.updateTransform(p.mtx, p.cx);
    if (disp.parent === null) {
      this.addChildAt(p.dp, disp);
    }
    if (this.clipLayer.length > 0) {
      len2=this.clipLayer.length;
      for (j=0;j<len2;++j) {
        cl = this.clipLayer[j]
        if (cl.from < p.dp && p.dp <= cl.to) {
          disp.clipId = cl.id;
        }
      }
    }
    if (p.cdp && disp.clipDepth != p.cdp) {
      disp.clipDepth = p.cdp;
      this.clipLayer.push({"id":disp.id, "from":p.dp, "to":p.cdp});
    }
  }
};

display.DisplayContainer.prototype._actionDisplay = function (action) {
  var i, len = 0;
  if (action && (len = action.length) > 0) {
    for (i = 0; i < len; ++i) {
      this.actionQueue.push(new Avm(this, action[i]));
    }
  }
};

display.DisplayContainer.prototype.addChildAt = function(depth, disp) {
  var displays = this.displayList;
  disp.parent = this;
  disp.depth = depth;
  disp.buildId();
  disp.added();
  displays.push(disp);
  displays.sort(this.context.renderer.displaySort);
};

display.DisplayContainer.prototype.getChildAt = function(depth) {
  var displays = this.displayList;
  var len = displays.length;
  for (var i = 0; i<len; ++i) {
    if (displays[i].depth === depth) {
      return displays[i];
    }
  }
  return undefined;
};

display.DisplayContainer.prototype.getChildByName = function(name) {
  var displays = this.displayList;
  var len = displays.length;
  for (var i = 0; i<len; ++i) {
    if (displays[i].name === name) {
      return displays[i];
    }
  }
  return undefined;
};

display.DisplayContainer.prototype.resolvePath = function (path) {
  if (this.cid !== "root" && this.parent === null) {
    throw "Error: parent is null."+this.cid;
  }
  if (path === "") {
    return this;
  }
  var i = path.indexOf("/");
  switch (i) {
    case -1:
      return this.getChildByName(path);
    case 0:
      return this.context.stage.resolvePath(path.substring(1));
    default:
      var target = path.substring(0, i);
      if (target === "..") {
        return this.parent.resolvePath(path.substring(i+1));
      } else {
        var obj = this.getChildByName(target);
        return (obj) ? obj.resolvePath(path.substring(i+1)) : undefined;
      }
  }
};

display.DisplayContainer.prototype.removeChildAt = function(depth) {
  var disp;
  var displays = this.displayList;
  var len = displays.length - 1;
  for (var i = len;0<=i;--i) {
    disp = displays[i];
    if (disp.depth === depth) {
      if (disp.isAppended) {
        this.getRenderer().removeChild(this, disp)
      }
      disp.remove();
      displays.splice(i, 1);
    }
  }
  len = this.clipLayer.length;
  if (len > 0) {
    var cl;
    for (i=0;i<len;++i) {
      cl = this.clipLayer[i]
      if (cl && cl.from === depth) {
        this.clipLayer.splice(i,1);
      }
    }
  }
};

display.DisplayContainer.prototype.cloneSprite = function(target, name, depth) {
  var s = this.resolvePath(target);
  var n = s.clone();
  var m = (s.transform.matrix) ? s.transform.matrix.toArray() : [1,0,0,1,0,0];
  var c = (s.transform.cxform) ? s.transform.cxform.toArray() : [1,1,1,1,0,0,0,0];
  n.name = name;
  n.updateTransform(m, c);
  this.addChildAt(depth, n);
  n.advance();
};

display.DisplayContainer.prototype.setVars = function(name, value) {
  this.vars[name] = value;
};

display.DisplayContainer.prototype.getVars = function(name) {
  return this.vars[name];
};

display.DisplayContainer.prototype.callAction = function(label) {
  var index = this._getIndexByLabel(label);
  var control = this.controls[index];
  if (!control) {
    return false;
  }
  var action = control.act;
  var i, len = 0;
  var avm;
  if (action && (len = action.length) > 0) {
    for (i = 0; i < len; ++i) {
      avm = new Avm(this, action[i]);
      avm.execute();
    }
  }
  return true;
};

display.DisplayContainer.prototype._getIndexByLabel = function (label) {
  var c;
  var len = this.controls.length;
  for (var i = 0; i < len; ++i) {
    c = this.controls[i];
    if(c.label && c.label === label) {
      return i;
    }
  }
  return 0;
};

