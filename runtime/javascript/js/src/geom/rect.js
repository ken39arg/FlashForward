
geom.Rect = function (ar) {
  this.xmin = 0;
  this.ymin = 0;
  this.xmax = 0;
  this.ymax = 0;
  if (!ar) {
  } else if (typeof ar === "object" && ar.length === 4) {
    this.fromArray(ar);
  } else if (typeof ar === "string" && ar.indexOf(" ")) {
    this.fromArray(ar.split(" "));
  }
};
geom.Rect.prototype.width = function() {
  return this.xmax - this.xmin;
};

geom.Rect.prototype.height = function() {
  return this.ymax - this.ymin;
};

geom.Rect.prototype.toString = function() {
  return "rect("+this.xmin+","+this.ymin+","+this.xmax+","+this.ymax+")";
};

geom.Rect.prototype.fromArray = function (ar) {
  this.xmin = ar[0];
  this.ymin = ar[1];
  this.xmax = ar[2];
  this.ymax = ar[3];
};

geom.Rect.prototype.add = function(rect) {
  if (rect.xmin < this.xmin) this.xmin = rect.xmin;
  if (this.xmax < rect.xmax) this.xmax = rect.xmax;
  if (rect.ymin < this.ymin) this.ymin = rect.ymin;
  if (this.ymax < rect.ymax) this.ymax = rect.ymax;
};

geom.Rect.prototype.addTransformd = function(rect, matrix) {
  var x1 = rect.xmin;
  var x2 = rect.xmax;
  var y1 = rect.ymin;
  var y2 = rect.ymax;
  var p1 = matrix.transform(x1, y1);
  var p2 = matrix.transform(x1, y2);
  var p3 = matrix.transform(x2, y1);
  var p4 = matrix.transform(x2, y2);
  this.expandTo(p1.x, p1.y);
  this.expandTo(p2.x, p2.y);
  this.expandTo(p3.x, p3.y);
  this.expandTo(p4.x, p4.y);
};

geom.Rect.prototype.expandTo = function(x,y) {
  if (x < this.xmin) this.xmin = x;
  if (this.xmax < x) this.xmax = x;
  if (y < this.ymin) this.ymin = y;
  if (this.ymax < y) this.ymax = y;
};

