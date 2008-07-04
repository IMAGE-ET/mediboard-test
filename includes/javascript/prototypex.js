/**
 * Class utility object
 */
Class.extend = function (oClass, oExtension) {
  Object.extend(oClass.prototype, oExtension);
}

 
/**
 * Function class
 */
Class.extend(Function, {
  getSignature: function() {
    var re = /function ([^\{]*)/;
    return this.toString().match(re)[1];
  }
});

// Caution: Object.extend syntax causes weird exceptions to be thrown further on execution

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
  load: function (sCookieName, nDuration) {
    var oCookie = new CookieJar({expires: nDuration});
    if (sValue = oCookie.getValue(sCookieName, this.element.id)) {
      this.set(sValue);
    }
  },
  
  save: function (sCookieName, nDuration) {
    new CookieJar({expires: nDuration}).setValue(sCookieName, this.element.id, this.toString());
  },

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

Object.extend(Form.Element, {
  // Set an element value an notify 'change' event
  setValue: function(element, value) {
   	// Test if element exist
    if (element && element.value != value) {
      element.value = value;
      (element.onchange || Prototype.emptyFunction).bind(element)();
    }
  },
  
  Selection: {
		setAll: function(input) {
		  this.setRange(input, 0, input.value.length);
		},
		
		setRange: function(input, start, end) {
      // Gecko case
 		  if (input.selectionStart != undefined) {
				input.selectionStart = start;
				input.selectionEnd = end;
	   	}
	   	
			// IE case
	   	if (input.createTextRange != undefined) {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveStart("character", start);
				range.moveEnd("character", end - start);
				range.select();
	   	}
	  },
	  
		getStart: function(input) {
      // Gecko case
 		  if (input.selectionStart != undefined) {
				return input.selectionStart;
			}
			
			// IE case
	   	if (document.selection) {
				var range = document.selection.createRange();
				var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
				if (!isCollapsed) {
					range.collapse(true);
				}
				var b = range.getBookmark();
				return b.charCodeAt(2) - 2;
			}
		},
		
		getEnd: function(input)  {
      // Gecko case
 		  if (input.selectionEnd != undefined) {
				return input.selectionEnd;
			}
			
			// IE case
	   	if (document.selection) {
				var range = document.selection.createRange();
				var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
				if (!isCollapsed) {
					range.collapse(false);
				}
				var b = range.getBookmark();
				return b.charCodeAt(2) - 2;
			}
		}
	}
} );

// Makes an element to be in the viewport instead of overflow
Element.addMethods({
  unoverflow: function(element) {
    var dim = element.getDimensions(); // Element dimensions
    var pos = element.cumulativeOffset(); // Element position
    var viewport = document.documentElement.getDimensions(); // Viewport size

    pos.right  = pos[2] = pos.left + dim.width;  // Element right position
    pos.bottom = pos[3] = pos.top  + dim.height; // Element bottom position
    
    // If the element exceeds the viewport on the right
    if (pos.right > viewport.width) {
      element.setStyle({marginLeft: Math.max(viewport.width - pos.right, -pos.left) + 'px'});
    }

    // If the element exceeds the viewport on the bottom
    if (pos.bottom > viewport.height) {
      element.setStyle({marginTop: Math.max(viewport.height - pos.bottom, -pos.top) + 'px'});
    }
    
    return element;
  }
});

EventEx = {};

Object.extend(EventEx, {
  _domReady : function() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;

    if (this._timer)  clearInterval(this._timer);
    
    this._readyCallbacks.each(function(f) { f() });
    this._readyCallbacks = null;
},
  onDOMReady : function(f) {
    if (!this._readyCallbacks) {
      var domReady = this._domReady.bind(this);
      
      if (document.addEventListener)
        document.addEventListener("DOMContentLoaded", domReady, false);
        
        /*@cc_on @*/
        /*@if (@_win32)
            document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
            document.getElementById("__ie_onload").onreadystatechange = function() {
                if (this.readyState == "complete") domReady(); 
            };
        /*@end @*/
        
        if (/WebKit/i.test(navigator.userAgent)) { 
          this._timer = setInterval(function() {
            if (/loaded|complete/.test(document.readyState)) domReady(); 
          }, 10);
        }
        
        Event.observe(window, 'load', domReady);
        Event._readyCallbacks =  [];
    }
    Event._readyCallbacks.push(f);
  }
});
