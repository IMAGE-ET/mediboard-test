/**
 * Class utility object
 */
 
Class.extend = function (oClass, oExtension) {
  Object.extend(oClass.prototype, oExtension);
}

/**
 * Try utility object
 */

Try.extend( { 
  // Try as many functions as possible and returns array of return values
  allThese : function() {
    var aReturnValues = [];
    for (var i = 0; i < arguments.length; i++) {
      var oLambda = arguments[i];
      try {
        aReturnValues.push(oLambda());
      } catch (e) {
        aReturnValues.push(false);
      }
    }
    return aReturnValues;
  }
});
 
/**
 * Function class
 */
 
Class.extend(Function, {
  getName: function() {
    var re = /function ([^\(]*)/;
    return this.toString().match(re)[1] || "anonymous";
  }
});

/**
 * Element utility object
 */

// Caution: Object.extend syntax causes weird exceptions to be thrown further on execution

Element.addEventHandler = function(oElement, sEvent, oHandler) {
  var sEventMethod = "on" + sEvent;
  var oPreviousHandler = oElement[sEventMethod] || function() {};
  oElement[sEventMethod] = function () {
    oPreviousHandler();
    oHandler(oElement);
  }
}

/**
 * Element.ClassNames class
 */

Class.extend(Element.ClassNames, {
  toggle: function(sClassName) {
    this[this.include(sClassName) ? 'remove' : 'add'](sClassName);
  },
  
  flip: function(sClassName1, sClassName2) {
    if (this.include(sClassName1)) {
      this.remove(sClassName1);
      this.add(sClassName2);
      return;
    }
    
    if (this.include(sClassName2)) {
      this.remove(sClassName2);
      this.add(sClassName1);
      return;
    }
  }
});


