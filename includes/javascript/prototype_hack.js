/**
 * Hack for Firefox 2.0 bug
 *
 * Bug causes Object and Function object to be emptied 
 * when opening a new window
 */
 
ObjectInitialisation = {
  // Function to be launched during body.onload event
  hackIt : function() {
    if (Object.extend) {
      return;
    }

		Console.trace("Hacking Object initialisation");
    Object.extend = function(destination, source) {
      for (property in source) {
        destination[property] = source[property];
      }
      return destination;
    }

    Object.inspect = function(object) {
      try {
        if (object == undefined) return 'undefined';
        if (object == null) return 'null';
        return object.inspect ? object.inspect() : object.toString();
      } catch (e) {
        if (e instanceof RangeError) return '...';
        throw e;
      }
    }
    
    Function.prototype.bind = function() {
      var __method = this, args = $A(arguments), object = args.shift();
      return function() {
        return __method.apply(object, args.concat($A(arguments)));
      }
    }
    
    Function.prototype.bindAsEventListener = function(object) {
      var __method = this;
      return function(event) {
        return __method.call(object, event || window.event);
      }
    }
    
    Function.prototype.getName = function() {
      var re = /function ([^\(]*)/;
      return this.toString().match(re)[1] || "anonymous";
    }
  }
}
 