
geom.Cxform =  function (ar) {
  this.mr = 1;
  this.mg = 1;
  this.mb = 1;
  this.ma = 1;
  this.ar = 0;
  this.ag = 0;
  this.ab = 0;
  this.aa = 0;
  if (typeof ar === "object" && ar.length === 8) {
    this.fromArray(ar);
  }
};

geom.Cxform.prototype.toString = function() {
  return "cxform("+this.mr+","+this.mg+","+this.mb+","+this.ma+","+this.ar+","+this.ag+","+this.ab+","+this.aa+")";
};

geom.Cxform.prototype.fromArray = function (ar) {
  this.mr = ar[0];
  this.mg = ar[1];
  this.mb = ar[2];
  this.ma = ar[3];
  this.ar = ar[4];
  this.ag = ar[5];
  this.ab = ar[6];
  this.aa = ar[7];
};

geom.Cxform.prototype.toArray = function() {
  return [this.mr,this.mg,this.mb,this.ma,this.ar,this.ag,this.ab,this.aa];
};

geom.Cxform.prototype.set_alpha = function(alpha) {
  this.ma = alpha;
};

geom.Cxform.prototype.get_alpha = function() {
  return this.ma;
};

geom.Cxform.prototype.transform = function(r,g,b,a) {
  var c = {};
  if (a === undefined) a = 1.0;
  c.r = Math.max(0, Math.min(r * this.mr + this.ar, 255));
  c.g = Math.max(0, Math.min(g * this.mg + this.ag, 255));
  c.b = Math.max(0, Math.min(b * this.mb + this.ab, 255));
  c.a = Math.max(0, Math.min(a * this.ma + this.aa, 1.0));
  return c;
};

