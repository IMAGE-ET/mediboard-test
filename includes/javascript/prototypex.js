/**
 * Class utility object
 */
 
Class.extend = function (oClass, oExtension) {
  Object.extend(oClass.prototype, oExtension);
}

/**
 * Try utility object
 */

Object.extend(Try, { 
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
  },
  
  getSignature: function() {
    var re = /function ([^\{]*)/;
    return this.toString().match(re)[1];
  }
});

/**
 * Element utility object
 */

// Hack because IE won't be able to extend TextNodes (nodeType == 3). 
// Useful for Element.cleanWhitespance used in Scriptaculous auto-completion
Element.extend = function(element) {
  if (!element) return;
  
  // The Hack line
  if (element.nodeType == 3) return element; 
  
  if (!element._extended && element.tagName && element != window) {
    var methods = Element.Methods;
    for (property in methods) {
      var value = methods[property];
      if (typeof value == 'function')
        element[property] = value.bind(null, element);
    }
  }

  element._extended = true;
  return element;
}

// Caution: Object.extend syntax causes weird exceptions to be thrown further on execution

Event.Observe

Element.addEventHandler = function(oElement, sEvent, oHandler) {
  var sEventMethod = "on" + sEvent;
  var oPreviousHandler = oElement[sEventMethod] || function() {};
  oElement[sEventMethod] = function () {
    oPreviousHandler.bind(oElement)();
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

/**
 * Hack to make Ajax from prototype.js work with Rico
 * For prototype version 1.5.0 Final
 */

//Ajax.Updater.prototype.setRequestHeaders = 
//Ajax.Request.prototype.setRequestHeaders = function() {
//  var headers = {
//    'X-Requested-With': 'XMLHttpRequest',
//    'X-Prototype-Version': Prototype.Version,
//    'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
//  };

//  if (this.options.method == 'post') {
//    headers['Content-type'] = this.options.contentType +
//      (this.options.encoding ? '; charset=' + this.options.encoding : '');

    /* Force "Connection: close" for older Mozilla browsers to work
     * around a bug where XMLHttpRequest sends an incorrect
     * Content-length header. See Mozilla Bugzilla #246651.
     */
//    if (this.transport.overrideMimeType &&
//        (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005)
//          headers['Connection'] = 'close';
//  }

  // user-defined headers
//  if (typeof this.options.requestHeaders == 'object') {
//    var extras = this.options.requestHeaders;

//    if (typeof extras.push == 'function')
//      for (var i = 0; i < extras.length; i += 2)
//        headers[extras[i]] = extras[i+1];
//    else
//      $H(extras).each(function(pair) { headers[pair.key] = pair.value });
//  }

// BEFORE HACK
//    for (var name in headers)
//      this.transport.setRequestHeader(name, headers[name]);

// AFTER HACK    
//    $H(headers).each(function(pair) { 
//    this.transport.setRequestHeader(pair.key, pair.value); 
//  }.bind(this));
	
//}


