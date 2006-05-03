/*
    example:
    
    fucntion up() { ... }
    function down() { ... }
    function left() { ... }
    function right() { ... }
    
    var k = new Keyboard();
    k.up = up;
    k.down = down;
    k.left = left;
    k.right = right;
    document.onkeydown = k.event;
*/
function Keyboard() {
    this.up = 38;
    this.down = 40;
    this.left = 37;
    this.right = 39;
    this.space = 32;
    this.escape = 27;
    this.set = function(key, func) {
        this.keys.push(key);
        this.funcs.push(func);
    };
    this.event = function(e) {
        if (!e) { e = window.event; }
        for (var i = 0; i < self.keys.length; ++i) {
            if (e.keyCode == self.keys[i]) {
                self.funcs[i]();
            }
        }
    };
    this.keys = [];
    this.funcs = [];
    var self = this;
}