function sqr(v){return v*v;}function sqrt(v){return Math.sqrt(v);}function sin(v){return Math.sin(v);}function cos(v){return Math.cos(v);}function acos(v){return Math.acos(v);}

Number.prototype.deg = function () {
    return this / Math.PI * 180;
};

Number.prototype.rad = function () {
    return this / 180 * Math.PI;
};

function Vector(x, y) {
    this.x = x;
    this.y = y;
}

Object.defineProperty(Vector.prototype,'length',{get: function() {
    return sqrt(sqr(this.x) + sqr(this.y));
}});

Vector.prototype.dot = function(v) {
    return this.x * this.y + v.x * v.y;
};

Vector.prototype.rotateAround = function(o, a) {
    let v = this, c = cos(a), s = sin(a), x, y;
    x = (v.x - o.x) * c - (v.y - o.y) * s + o.x;
    y = (v.x - o.x) * s + (v.y - o.y) * c + o.y;
    this.x = x; this.y = y;
};

function angleBetweenPoints(a,b,c) {
    let
        x1 = a.x - b.x,
        x2 = c.x - b.x,
        y1 = a.y - b.y,
        y2 = c.y - b.y,
        d1 = sqrt(sqr(x1) + sqr(y1)),
        d2 = sqrt(sqr(x2) + sqr(y2));
    return acos((x1 * x2 + y1 * y2) / (d1 * d2));
}

function angleBetweenVectors(a,b) {
    return acos(a.dot(b) / (a.length * b.length));
}