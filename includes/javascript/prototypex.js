/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class utility object
 */
Class.extend = function (oClass, oExtension) {
  Object.extend(oClass.prototype, oExtension);
};
 
/**
 * Function class
 */
Class.extend(Function, {
  getSignature: function() {
    var re = /function ([^\{]*)/;
    return this.toString().match(re)[1];
  }
});

/**
 * Recursively merges two objects.
 * @param {Object} src - source object (likely the object with the least properties)
 * @param {Object} dest - destination object (optional, object with the most properties)
 * @return {Object} recursively merged Object
 */
Object.merge = function(src, dest){
  var i, v, result = dest || {};
  for(i in src){
    v = src[i];
    result[i] = (v && typeof(v) === 'object' && !(v.constructor === Array || v.constructor === RegExp) && !Object.isElement(v)) ? Object.merge(v, dest[i]) : result[i] = v;
  }
  return result;
};

/** TODO: Remove theses fixes */

// Fixes a bug that scrolls the page when in an autocomplete 
Class.extend(Autocompleter.Base, {
  markPrevious: function() {
   if(this.index > 0) {this.index--;}
   else {
    this.index = this.entryCount-1;
    this.update.scrollTop = this.update.scrollHeight;
   }
   var selection = this.getEntry(this.index);
   if(selection.offsetTop < this.update.scrollTop){
    this.update.scrollTop = this.update.scrollTop-selection.offsetHeight;
   }
  },
  markNext: function() {
   if(this.index < this.entryCount-1) {this.index++;}
   else {
    this.index = 0;
    this.update.scrollTop = 0;
   }
   var selection = this.getEntry(this.index);
   if((selection.offsetTop+selection.offsetHeight) > this.update.scrollTop+this.update.offsetHeight){
    this.update.scrollTop = this.update.scrollTop+selection.offsetHeight;
   }
  },
  updateChoices: function(choices) {
    if(!this.changed && this.hasFocus) {
      this.update.innerHTML = choices;
      Element.cleanWhitespace(this.update);
      Element.cleanWhitespace(this.update.down());

      if(this.update.firstChild && this.update.down().childNodes) {
        this.entryCount =
          this.update.down().childNodes.length;
        for (var i = 0; i < this.entryCount; i++) {
          var entry = this.getEntry(i);
          entry.autocompleteIndex = i;
          this.addObservers(entry);
        }
      } else {
        this.entryCount = 0;
      }

      this.stopIndicator();
      this.update.scrollTop = 0;
      
      // was "this.index = 0;"
      this.index = this.options.dontSelectFirst ? -1 : 0;

      if(this.entryCount==1 && this.options.autoSelect) {
        this.selectEntry();
        this.hide();
      } else {
        this.render();
      }
    }
  },
  onKeyPress: function(event) {
    if(this.active)
      switch(event.keyCode) {
       case Event.KEY_TAB:
         // Tab key should not select an element if this is a STR autocomplete
         if (this.element.hasClassName("str")) {
           this.hide();
           this.active = false;
           this.changed = false;
           this.hasFocus = false;
           return;
         }
       case Event.KEY_RETURN:
         if (this.index < 0) return; 
         this.selectEntry();
         Event.stop(event);
       case Event.KEY_ESC:
         this.hide();
         this.active = false;
         Event.stop(event);
         return;
       case Event.KEY_LEFT:
       case Event.KEY_RIGHT:
         return;
       case Event.KEY_UP:
         this.markPrevious();
         this.render();
         Event.stop(event);
         return;
       case Event.KEY_DOWN:
         this.markNext();
         this.render();
         Event.stop(event);
         return;
      }
     else
       if(event.keyCode==Event.KEY_TAB || event.keyCode==Event.KEY_RETURN ||
         (Prototype.Browser.WebKit > 0 && event.keyCode == 0)) return;

    this.changed = true;
    this.hasFocus = true;

    if(this.observer) clearTimeout(this.observer);
    this.observer =
      setTimeout(this.onObserverEvent.bind(this), this.options.frequency*1000);
  },
  getTokenBounds: function() {
    if (!this.options.caretBounds && (null != this.tokenBounds)) return this.tokenBounds;
    var value = this.element.value;
    if (value.strip().empty()) return [-1, 0];
    
    // This has been added so that the token bounds are relative to the current cert position
    if (this.options.caretBounds) {
      var caret = this.element.caret().begin;
      var start = value.substr(0, caret).lastIndexOf("\n")+1;
      var end = value.substr(caret).indexOf("\n")+caret+1;
      return (this.tokenBounds = [start, end]);
    }
    
    // This needs to be declared here as the arguments.callee is not the same
    var firstDiff = function(newS, oldS) {
      var boundary = Math.min(newS.length, oldS.length);
      for (var index = 0; index < boundary; ++index)
        if (newS[index] != oldS[index])
          return index;
      return boundary;
    };
    /////////////
    
    var diff = firstDiff(value, this.oldElementValue);
    var offset = (diff == this.oldElementValue.length ? 1 : 0);
    var prevTokenPos = -1, nextTokenPos = value.length;
    var tp;
    for (var index = 0, l = this.options.tokens.length; index < l; ++index) {
      tp = value.lastIndexOf(this.options.tokens[index], diff + offset - 1);
      if (tp > prevTokenPos) prevTokenPos = tp;
      tp = value.indexOf(this.options.tokens[index], diff + offset);
      if (-1 != tp && tp < nextTokenPos) nextTokenPos = tp;
    }
    return (this.tokenBounds = [prevTokenPos + 1, nextTokenPos]);
  },
  selectEntry: function() {
    this.active = false;
    if(this.index > -1){ 
      this.updateElement(this.getCurrentEntry());
    }
  }
});

Element.addMethods({
  // To fix a bug in Prototype 1.6.0.3 (no need to patch the lib)
  getOffsetParent: function(element) {
    if (element.offsetParent) return $(element.offsetParent);
    if (element == document.body) return $(element);

    while ((element = element.parentNode) && element != document.body && element != document) // Added " && element != document"
      if (Element.getStyle(element, 'position') != 'static')
        return $(element);

    return $(document.body);
  }
});

/** END HACKS */

/**
 * Element.ClassNames class
 */
Class.extend(Element.ClassNames, {
  load: function (sCookieName, nDuration) {
    var oCookie = new CookieJar({expires: nDuration});
    var sValue = oCookie.getValue(sCookieName, this.element.id);
    if (sValue) {
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


// Makes an element to be in the viewport instead of overflow
Element.addMethods({
  unoverflow: function(element, offset) {
    var dim = element.getDimensions(); // Element dimensions
    var pos = element.cumulativeOffset(); // Element position
    var scroll = document.viewport.getScrollOffsets(); // Viewport offset
    var viewport = document.viewport.getDimensions(); // Viewport size
    offset = offset || 0;

    pos.left -= scroll.left;
    pos.top -= scroll.top;

    pos.right  = pos[2] = pos.left + dim.width; // Element right position
    pos.bottom = pos[3] = pos.top + dim.height; // Element bottom position
    
    // If the element exceeds the viewport on the right
    if (pos.right > (viewport.width - offset)) {
      element.style.left = parseInt(element.style.left) - (pos.right - viewport.width) - offset + 'px';
    }

    // If the element exceeds the viewport on the bottom
    if (pos.bottom > (viewport.height - offset)) {
      element.style.top = parseInt(element.style.top) - (pos.bottom - viewport.height) - offset + 'px';
    }
    
    return element;
  },
  
  centerH : function(element) {
    element.setStyle({
      left: 0
    });
    var viewport = document.viewport.getDimensions(); // Viewport size
    var dim = element.getDimensions(); // Element dimensions
    element.setStyle({
      left: (viewport.width - dim.width) / 2 + "px",
      width: dim.width - 10 +"px"
    });
    return element;
  },
  
  setVisible: function(element, condition) {
    return element[condition ? "show" : "hide"]();
  },
	
  setVisibility: function(element, condition) {
    return element.setStyle( {
		  visibility: condition ? "visible" : "hidden"
	  } );
  },
	
	setClassName: function(element, className, condition) {
    if (condition ) element.addClassName(className);
    if (!condition) element.removeClassName(className);
	},
  
  getInnerWidth: function(element){
    var aBorderLeft = parseInt(element.getStyle("border-left-width")),
        aBorderRight = parseInt(element.getStyle("border-right-width"));
    return element.offsetWidth - aBorderLeft - aBorderRight;
  },
	
  getInnerHeight: function(element){
    var aBorderTop = parseInt(element.getStyle("border-top-width")),
        aBorderBottom = parseInt(element.getStyle("border-bottom-width"));
    return element.offsetHeight - aBorderTop - aBorderBottom;
  },
	
	/** Gets the elements properties (specs) thanks to its className */
	getProperties: function (element) {
    var props = {};

    $w(element.className).each(function (value) {
      var params = value.split("|");
      props[params.shift()] = (params.length == 0) ? true : (params.length > 1 ? params : params[0]);
    });
    return props;
  },
  
  /** Add a class name to an element, and removing this class name to all of it's siblings */
  addUniqueClassName: function(element, className) {
    $(element).siblings().invoke('removeClassName', className);
    return element.addClassName(className);
  },
	
  clone: function(element, deep) {
    return $($(element).cloneNode(deep)).writeAttribute("id", "");
  },
  
  /** Get the surrounding form of the element  */
  getSurroundingForm: function(element) {
    if (element.form) return $(element.form);
    return $(element).up('form');
  }
});

Element.addMethods(['input', 'textarea'], {
  emptyValue: function (element) {
    var notWhiteSpace = /\S/;
    return Object.isUndefined(element.value) ?
      element.empty() : 
      !notWhiteSpace.test(element.value);
  }
});

Element.addMethods('select', {
  sortByLabel: function(element){
    var selected = $V(element),
        sortedOptions = element.childElements().sortBy(function(o){
      return o.text;
    });
    element.update();
    sortedOptions.each(function(o){
      element.insert(o);
    });
    $V(element, selected, false);
  }
});

Element.addMethods('form', {
  clear: function(form, fire){
    $A(form.elements).each(function(e){
      if (e.type != "hidden" || /(autocomplete|date|time)/i.test(e.className)) {
        $V(e, '', fire);
      }
    });
  }
});

Object.extend(Event, {
  key: function(e){
    return (window.event && (window.event.keyCode || window.event.which)) || e.which || e.keyCode || false;
  },
  isCapsLock: function(e){
    var charCode = Event.key(e);
    var shiftOn = false;
    
    if (e.shiftKey) {
      shiftOn = e.shiftKey;
    } else if (e.modifiers) {
      shiftOn = !!(e.modifiers & 4);
    }

    if ((charCode >= 97 && charCode <= 122 && shiftOn) || 
        (charCode >= 65 && charCode <= 90 && !shiftOn)) {
      return true;
    }
    
    // Keys from the top of a French keyboard
    /*var keys = {
      "0": "à",
      "1": "&",
      "2": "é",
      "3": "\"",
      "4": "'",
      "5": "(",
      "6": "-",
      "7": "è",
      "8": "_",
      "9": "ç",
      "°": ")",
      "+": "=",
      "¨": "^",
      "£": "$",
      "%": "ù",
      "µ": "*",
      "?": ",",
      ".": ";",
      "/": ":",
      "§": "!",
      ">": "<"
    };
    
    var c = String.fromCharCode(charCode);
    
    if ( shiftOn && Object.values(keys).indexOf(c) != -1 ||
        !shiftOn && keys[c]) return true;*/

    return false;
  }
});

Object.extend(String, {
  allographs: {
    withDiacritics   : "àáâãäåòóôõöøèéêëçìíîïùúûüÿñ",
    withoutDiacritics: "aaaaaaooooooeeeeciiiiuuuuyn"
  },
  glyphs: {
    "a": "àáâãäå",
    "c": "ç",
    "e": "èéêë",
    "i": "ìíîï",
    "o": "òóôõöø",
    "u": "ùúûü",
    "y": "ÿ",
    "n": "ñ"
  }
});

Class.extend(String, {
  trim: function() {
    return this.replace(/^\s+|\s+$/g, "");
  },
  pad: function(ch, length, right) {
    length = length || 30;
    ch = ch || ' ';
    var t = this;
    while(t.length < length) t = (right ? t+ch : ch+t);
    return t;
  },
  unslash: function() {
    return this
      .replace(/\\n/g, "\n")
      .replace(/\\t/g, "\t");
  },
  stripAll: function() {
    return this.strip().gsub(/\s+/, " ");
  },
  removeDiacritics: function(){
    var str = this;
    var from, to;
    
    from = String.allographs.withDiacritics.split("");
    to   = String.allographs.withoutDiacritics.split("");
    
    from.each(function(c, i){
      str = str.gsub(c, to[i]);
    });
    
    from = String.allographs.withDiacritics.toUpperCase().split("");
    to   = String.allographs.withoutDiacritics.toUpperCase().split("");
    
    from.each(function(c, i){
      str = str.gsub(c, to[i]);
    });
    
    return str;
  },
  // @todo: should extend RegExp instead of String
  allowDiacriticsInRegexp: function() {
    var re = this.removeDiacritics();
    
    var translation = {};
    $H(String.glyphs).each(function(g){
      translation[g.key] = "["+g.key+g.value+"]";
    });
        
    $H(translation).each(function(t){
      re = re.replace(new RegExp(t.key, "gi"), t.value);
    });
    
    return re;
  },
  like: function(term) {
    var specials = "/.*+?|()[]{}\\".split("");
    
    term = term.replace(new RegExp('(\\' + specials.join('|\\') + ')', "g"), '\\$1');
    
    return !!this.match(new RegExp(term.trim().allowDiacriticsInRegexp(), "i"));
  }
});

Ajax.PeriodicalUpdater.addMethods({
  resume: function() {
    this.updateComplete();
  }
});

PeriodicalExecuter.addMethods({
  resume: function() {
    if (!this.timer) this.registerCallback();
  }
});

document.observeOnce = function(event_name, outer_callback){
  $(document.documentElement).observeOnce(event_name, outer_callback);
};
