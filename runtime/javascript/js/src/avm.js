/** @const */
var AVM_END                    = 0x00;
var AVM_NEXT_FRAME             = 0x04;
var AVM_PREVIOUS_FRAME         = 0x05;
var AVM_PLAY                   = 0x06;
var AVM_STOP                   = 0x07;
var AVM_TOGGLE_QUALITY         = 0x08;
var AVM_STOP_SOUND             = 0x09;
var AVM_ADD                    = 0x0A;
var AVM_SUBTRACT               = 0x0B;
var AVM_MULTIPLY               = 0x0C;
var AVM_DIVIDE                 = 0x0D;
var AVM_EQUAL                  = 0x0E;
var AVM_LESS                   = 0x0F;
var AVM_LOGICAL_AND            = 0x10;
var AVM_LOGICAL_OR             = 0x11;
var AVM_LOGICAL_NOT            = 0x12;
var AVM_STRING_EQUAL           = 0x13;
var AVM_STRING_LENGTH          = 0x14;
var AVM_STRING_EXTRACT         = 0x15;
var AVM_POP                    = 0x17;
var AVM_TO_INTEGER             = 0x18;
var AVM_GET_VARIABLE           = 0x1C;
var AVM_SET_VARIABLE           = 0x1D;
var AVM_SET_TARGET2            = 0x20;
var AVM_STRING_ADD             = 0x21;
var AVM_GET_PROPERTY           = 0x22;
var AVM_SET_PROPERTY           = 0x23;
var AVM_CLONE_SPRITE           = 0x24;
var AVM_REMOVE_SPRITE          = 0x25;
var AVM_TRACE                  = 0x26;
var AVM_START_DRAG             = 0x27;
var AVM_STOP_DRAG              = 0x28;
var AVM_STRING_LESS            = 0x29;
var AVM_THROW                  = 0x2A;
var AVM_CAST_OBJECT            = 0x2B;
var AVM_IMPLEMENTS             = 0x2C;
var AVM_FSCOMMAND2             = 0x2D;
var AVM_RANDOM                 = 0x30;
var AVM_MB_STRING_LENGTH       = 0x31;
var AVM_CHAR_TO_ASCII          = 0x32;
var AVM_ASCII_TO_CHAR          = 0x33;
var AVM_GET_TIME               = 0x34;
var AVM_MB_STRING_EXTRACT      = 0x35;
var AVM_MB_CHAR_TO_ASCII       = 0x36;
var AVM_MB_ASCII_TO_CHAR       = 0x37;
var AVM_DELETE                 = 0x3A;
var AVM_DELETE_ALL             = 0x3B;
var AVM_SET_LOCAL_VARIABLE     = 0x3C;
var AVM_CALL_FUNCTION          = 0x3D;
var AVM_RETURN                 = 0x3E;
var AVM_MODULO                 = 0x3F;
var AVM_NEW                    = 0x40;
var AVM_DECLARE_LOCAL_VARIABLE = 0x41;
var AVM_DECLARE_ARRAY          = 0x42;
var AVM_DECLARE_OBJECT         = 0x43;
var AVM_TYPE_OF                = 0x44;
var AVM_GET_TARGER             = 0x45;
var AVM_ENUMERATE              = 0x46;
var AVM_ADD2                   = 0x47;
var AVM_LESS_THAN2             = 0x48;
var AVM_EQUAL2                 = 0x49;
var AVM_NUMBER                 = 0x4A;
var AVM_STRING                 = 0x4B;
var AVM_DUPLICATE              = 0x4C;
var AVM_SWAP                   = 0x4D;
var AVM_GET_MEMBER             = 0x4E;
var AVM_SET_MEMBER             = 0x4F;
var AVM_INCREMENT              = 0x50;
var AVM_DECREMENT              = 0x51;
var AVM_CALL_METHOD            = 0x52;
var AVM_NEW_METHOD             = 0x53;
var AVM_INSTANCE_OF            = 0x54;
var AVM_ENUMERATE_OBJECT       = 0x55;
var AVM_AND                    = 0x60;
var AVM_OR                     = 0x61;
var AVM_XOR                    = 0x62;
var AVM_SHIFT_LEFT             = 0x63;
var AVM_SHIFT_RIGHT            = 0x64;
var AVM_SHIFT_RIGHT_UNSIGNED   = 0x65;
var AVM_STRICT_EQUAL           = 0x66;
var AVM_GREATER_THAN           = 0x67;
var AVM_STRING_GREATER_THAN    = 0x68;
var AVM_EXTENDS                = 0x69;
var AVM_GOTO_FRAME             = 0x81;
var AVM_GET_URL                = 0x83;
var AVM_STORE_REGISTER         = 0x87;
var AVM_DECLARE_DICTIONARY     = 0x88;
var AVM_STRICT_MODE            = 0x89;
var AVM_WAIT_FOR_FRAME         = 0x8A;
var AVM_SET_TARGET             = 0x8B;
var AVM_GOTO_LABEL             = 0x8C;
var AVM_WAIT_FOR_FRAME2        = 0x8D;
var AVM_DECLARE_FUNCTION2      = 0x8E;
var AVM_TRY                    = 0x8F;
var AVM_WITH                   = 0x94;
var AVM_PUSH                   = 0x96;
var AVM_JUMP                   = 0x99;
var AVM_GET_URL2               = 0x9A;
var AVM_DECLARE_FUNCTION       = 0x9B;
var AVM_IF                     = 0x9D;
var AVM_CALL                   = 0x9E;
var AVM_GOTO_FRAME2            = 0x9F;

/**
 * @constructor
 */
var Avm = function (target, scripts) {
    this.target   = target;
    this.actionTarget = target;
    this.stack    = [];
    this.points   = [];
    this.commands = [];
    this.index    = 0;
    for (var i in scripts) {
      this.points.push(parseInt(i));
      this.commands.push(scripts[i]);
    }
};

/**
 * @public
 */
Avm.prototype.execute = function() {
  var command = [];
  var funcname = "";
  this.actionTarget = this.target;
  while ((command = this.getNextCommand_())) {
    if (command[0] === AVM_END) {
      return;
    }
    try {
      this.exec_(command[0], command[1]);
    } catch (e) {
      console.error(e, command, this.stack);
    }
  }
};

Avm.prototype.push_ = function (v) {
  this.stack.push(v);
};

Avm.prototype.pop_ = function () {
  return this.stack.pop();
};

/**
 * @private
 */
Avm.prototype.getNextCommand_ = function() {
  return this.commands[this.index++];
};

/**
 * @private
 */
Avm.prototype.jump_ = function (line) {
  this.index = this.points.indexOf(parseInt(line));
  if (this.index === -1) {
    console.error("goto point is undefined. " + line, this.commands, this.points);
  }
};

/**
 * @private
 */
Avm.prototype.exec_ = function (funcId, v) {

  switch (funcId) {

  case AVM_PUSH:
    this.push_(v[0]);
  break;

  case AVM_POP:
    this.pop_();
  break;

  case AVM_IF:
    var condition = this.pop_();
    if (condition) {
      this.jump_(v[0]);
    }
  break;

  case AVM_JUMP:
    this.jump_(v[0]);
  break;

  case AVM_SET_VARIABLE:
    var value = this.pop_();
    var name = this.pop_();
    var t =[];
    if (name === undefined) {
      console.error("#"+this.target.id+".Action::set name is undefined")
      return false;
    }
    if (name.indexOf(":") > 0) {
      t = name.split(":");
      this.actionTarget.resolvePath(t[0]).setVars(t[1], value);
    } else {
      this.actionTarget.setVars(name, value);
    }
  break;

  case AVM_GET_VARIABLE:
    var name = this.pop_();
    var t =[];
    var r ="";
    if (name === undefined) {
      console.error("#"+this.target.id+".Action::set name is undefined")
      return false;
    }
    if (name.indexOf(":") > 0) {
      t = name.split(":");
      r = this.actionTarget.resolvePath(t[0]).getVars(t[1]);
    } else {
      r = this.actionTarget.getVars(name);
    }
    this.push_(r);
  break;

  case AVM_CALL:
    var label = this.pop_();
    this.actionTarget.callAction(label);
  break;

  case AVM_ADD:
  case AVM_ADD2:
    var a = Number(this.pop_());
    var b = Number(this.pop_());
    if (this.target.context.version <= 4) {
      if (isNaN(a)) a = 0;
      if (isNaN(b)) b = 0;
    }
    this.push_(b+a);
  break;

  case AVM_SUBTRACT:
    var a = Number(this.pop_());
    var b = Number(this.pop_());
    if (this.target.context.version <= 4) {
      if (isNaN(a)) a = 0;
      if (isNaN(b)) b = 0;
    }
    this.push_(b-a);
  break;

  case AVM_MULTIPLY:
    var a = Number(this.pop_());
    var b = Number(this.pop_());
    if (this.target.context.version <= 4) {
      if (isNaN(a)) a = 0;
      if (isNaN(b)) b = 0;
    }
    this.push_(b*a);
  break;

  case AVM_DIVIDE:
    var a = Number(this.pop_());
    var b = Number(this.pop_());
    if (this.target.context.version <= 4) {
      if (isNaN(a)) a = 0;
      if (isNaN(b)) b = 0;
    }
    this.push_(b/a);
  break;

  case AVM_EQUAL:
  case AVM_STRING_EQUAL:
    var a = this.pop_();
    var b = this.pop_();
    this.push_(b==a);
  break;

  case AVM_LESS:
    var a = this.pop_();
    var b = this.pop_();
    this.push_(b<a);
  break;

  case AVM_LOGICAL_AND:
    var a = this.pop_();
    var b = this.pop_();
    this.push_(Boolean(b&&a));
  break;

  case AVM_LOGICAL_OR:
    var a = this.pop_();
    var b = this.pop_();
    this.push_(Boolean(b||a));
  break;

  case AVM_LOGICAL_NOT:
    var a = this.pop_();
    this.push_(Boolean(!a));
  break;

  case AVM_STRING_ADD:
    var a = "" + this.pop_();
    var b = "" + this.pop_();
    if (a === "undefined") a = "";
    if (b === "undefined") b = "";
    this.push_(b.concat(a));
  break;

  case AVM_STRING_EXTRACT:
    var count  = parseInt(this.pop_());
    var index  = parseInt(this.pop_());
    var str    = "" + this.pop_();
    this.push_(str.substr(index - 1, count));
  break;

  case AVM_STRING_LENGTH:
    var str = "" + this.pop_();
    this.push_(str.length);
  break;

  case AVM_TO_INTEGER:
    var v = parseInt(this.pop_(), 10);
    this.push_(v);
  break;

  case AVM_PLAY:
    this.actionTarget.play();
  break;

  case AVM_STOP:
    this.actionTarget.stop();
  break;

  case AVM_NEXT_FRAME:
    this.actionTarget.nextFrame();
  break;

  case AVM_PREVIOUS_FRAME:
    this.actionTarget.previousFrame();
  break;

  case AVM_GOTO_FRAME2:
    var frame = this.pop_();
    if (this.actionTarget === undefined && this.target.context.version <= 4.0) {
      return;
    }
    this.actionTarget.gotoFrame(frame, v[0]);
  break;

  case AVM_GOTO_FRAME:
    this.actionTarget.gotoFrame(parseInt(v[0]) + 1, v[1]);
  break;

  case AVM_GOTO_LABEL:
    this.actionTarget.gotoFrame(v[0], v[1]);
  break;

  case AVM_SET_TARGET:
    var target = v[0];
    this.actionTarget = (target) ? this.target.resolvePath(target) : this.target;
    if (this.actionTarget === undefined) {
      console.error("Not found target=" + target + " base=" + this.target.name + " base_frame=" + this.target.currentFrame);
    }
  break;

  case AVM_SET_TARGET2:
    var target = this.pop_();
    this.actionTarget = (target) ? this.target.resolvePath(target) : this.target;
    if (this.actionTarget === undefined) {
      console.error("Not found target=" + target + " base=" + this.target.name + " base_frame=" + this.target.currentFrame);
    }
  break;

  case AVM_GET_PROPERTY:
    var prop = this.pop_();
    var target = this.pop_();
    var disp = (target) ? this.actionTarget.resolvePath(target) : this.actionTarget;
    var p = disp.getProperty(prop);
    this.push_(p);
  break;

  case AVM_SET_PROPERTY:
    var value = this.pop_();
    var prop = this.pop_();
    var target = this.pop_();
    var disp = (target) ? this.actionTarget.resolvePath(target) : this.actionTarget;
    disp.setProperty(prop, value);
  break;

  case AVM_CLONE_SPRITE:
    var depth = parseInt(this.pop_());
    var target = "" + this.pop_();
    var source = "" + this.pop_();

    this.actionTarget.cloneSprite(source, target, depth);
  break;

  case AVM_GET_TIME:
  break;

  case AVM_RANDOM:
    var max = this.pop_();
    this.push_(Math.floor(Math.random() * max));
  break;

  case AVM_TRACE:
    var val = this.pop_();
    console.log("trace", val);
  break;

  case AVM_END:
  break;

  case AVM_FSCOMMAND2:
    var arg = this.pop_();
    for (var i=0;i<arg;++i) {
      this.pop_();
    }
  break;

  default:
    console.warn("Unknown Action "+funcId, v);
  break;
  }
};
