/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

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


// Makes an element to be in the viewport instead of overflow
Element.addMethods({
  unoverflow: function(element) {
    var dim = element.getDimensions(); // Element dimensions
    var pos = element.cumulativeOffset(); // Element position
    var scroll = document.viewport.getScrollOffsets(); // Viewport offset
    var viewport = document.viewport.getDimensions(); // Viewport size

    pos.left -= scroll.left;
    pos.top -= scroll.top;

    pos.right  = pos[2] = pos.left + dim.width;  // Element right position
    pos.bottom = pos[3] = pos.top + dim.height; // Element bottom position
    
    // If the element exceeds the viewport on the right
    if (pos.right > viewport.width) {
      element.setStyle({marginLeft: Math.max(viewport.width - pos.right, -pos.left) + 'px'});
    }

    // If the element exceeds the viewport on the bottom
    if (pos.bottom > viewport.height) {
      element.setStyle({marginTop: Math.max(viewport.height - pos.bottom, -pos.top) + 'px'});
    }
    
    return element;
  },
  
  setVisible: function(element, condition) {
    return element[condition ? "show" : "hide"]();
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
      props[params.shift()] = (params.length == 0) ? true : params.reduce();
    });
    return props;
  },
  
  /** Add a class name to an element, and removing this class name to all of it's siblings */
  addUniqueClassName: function(element, className) {
    element.siblings().each(function(e){e.removeClassName(className)});
    return element.addClassName(className);
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