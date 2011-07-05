
geom.Matrix = function (ar) {
  this.a = 1;
  this.b = 0;
  this.c = 0;
  this.d = 1;
  this.tx = 0;
  this.ty = 0;
  if (typeof ar === "object" && ar.length === 6) {
    this.fromArray(ar);
  } else if (typeof ar === "string") {
    this.fromString(ar);
  }
};

geom.Matrix.prototype.toString = function() {
  return "matrix("+this.a+","+this.b+","+this.c+","+this.d+","+this.tx+","+this.ty+")";
};

geom.Matrix.prototype.toArray = function() {
  return [this.a, this.b, this.c, this.d, this.tx, this.ty];
};

geom.Matrix.prototype.fromString = function(str) {
  var s = /\(([0-9,\.\-]*)\)/.exec(str.replace(" ",","));
  this.fromArray(s[1].split(","));
};

geom.Matrix.prototype.fromArray = function (ar) {
  this.a  = Number(ar[0]);
  this.b  = Number(ar[1]);
  this.c  = Number(ar[2]);
  this.d  = Number(ar[3]);
  this.tx = Number(ar[4]);
  this.ty = Number(ar[5]);
};

geom.Matrix.prototype.add = function(v) {
  var n = new geom.Matrix;
  n.a = this.a * v.a + this.c * v.b;
  n.b = this.b * v.a + this.d * v.b;
  n.c = this.a * v.c + this.c * v.d;
  n.d = this.b * v.c + this.d * v.d;
  n.tx = this.a * v.tx + this.c * v.ty + this.tx;
  n.ty = this.b * v.tx + this.d * v.ty + this.ty;
  return n;
};

geom.Matrix.prototype._rotationX = function() {
  return Math.atan2(this.b, this.a);
};

geom.Matrix.prototype._rotationY = function() {
  return Math.atan2(-this.c, this.d);
};

geom.Matrix.prototype.set_x = function(x) {
  this.tx = x;
};

geom.Matrix.prototype.get_x = function() {
  return this.tx;
};

geom.Matrix.prototype.set_y = function(y) {
  this.ty = y;
};

geom.Matrix.prototype.get_y = function() {
  return this.ty;
};

geom.Matrix.prototype.set_xscale = function(xscale) {
  var rot_x = this._rotationX();
  this.a = xscale * Math.cos(rot_x);
  this.b = xscale * Math.sin(rot_x);
};

geom.Matrix.prototype.get_xscale = function() {
  return Math.sqrt(Math.pow(this.a, 2) + Math.pow(this.b, 2));
};

geom.Matrix.prototype.set_yscale = function(yscale) {
  var rot_y = this._rotationY();
  this.c = -1 * yscale * Math.sin(rot_y);
  this.d = yscale * Math.cos(rot_y);
};

geom.Matrix.prototype.get_yscale = function() {
  return Math.sqrt(Math.pow(this.c, 2) + Math.pow(this.d, 2));
};

geom.Matrix.prototype.set_rotation = function(rotation) {
  var rot_x = this._rotationX();
  var rot_y = this._rotationY();
  var scale_x = this.get_xscale();
  var scale_y = this.get_yscale();

  this.a = scale_x * Math.cos(rotation);
  this.b = scale_x * Math.sin(rotation);
  this.c = -1 * scale_y * Math.sin(rot_y - rot_x + rotation);
  this.d = scale_y * Math.cos(rot_y - rot_x + rotation);
};

geom.Matrix.prototype.get_rotation = function() {
  return this._rotationX();
};

geom.Matrix.prototype.transform = function(x, y) {
  var _x = this.a * x + this.c * y + this.tx;
  var _y = this.b * x + this.d * y + this.ty;
  return {x: _x, y: _y}
};


