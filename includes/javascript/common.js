/* $Id: checkForms.js 7654 2009-12-18 10:42:06Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: 7654 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Javascript error logging
function errorHandler(errorMsg, url, lineNumber, exception) {
  try {
    exception = exception || new Error(errorMsg, url, lineNumber);
    try {
      console.error(exception);
    } catch (e) {}
    
    new Ajax.Request("index.php?m=system&a=js_error_handler&suppressHeaders=1&dialog=1", {
      method: 'post',
      parameters: 'm=system&a=js_error_handler&' +
      $H({
        errorMsg: errorMsg,
        url: url,
        lineNumber: lineNumber,
        stack: exception.stack || exception.stacktrace,
        location: location.href
      }).toQueryString()
    });
  } catch (e) {}
  return true;
};

// TODO needs testing (doesn't throw console.error every time)
if (Prototype.Browser.IE)
  window.onerror = errorHandler;

/**
 * Main page initialization scripts
 */
var Main = {
  callbacks: [],
  loadedScripts: {},
  initialized: false,
  
  /**
   * Add a script to be lanuched after onload notification
   * On the fly execution if already page already loaded
   */
  add: function(callback) {
    if (this.initialized) {
      callback.defer();
    }
    else {
      this.callbacks.push(callback);
    }
  },
  
  require: function(script, options) {
    if (this.loadedScripts[script]) {
      return;
    }
    
    options = Object.extend({
      evalJS: true,
      onSuccess: (function(script){
        return function() {
          Main.loadedScripts[script] = true;
        }
      })(script)
    }, options);
    
    return new Ajax.Request(script, options);
  },
  
  /**
   * Call all Main functions
   */
  init: function() {
    this.callbacks.each(function(e) { e() } );
    this.initialized = true;
  }
};

/**
 * References manipulation
 */
var References = {
  /**
   * Clean references involved in memory leaks
   */
  clean: function(obj) {
    var i, j, e, 
        elements = obj.childElements(), 
        le = elements.length, la;
    for (j = 0; j < le; j++) {
      e = elements[j];
      if (e) {
        if (e.attributes) {
          la = e.attributes.length;
          for (i = 0; i < la; i++) {
            if (Object.isFunction(e.attributes[i])) e.attributes[i] = null;
          }
        }
        Element.remove(e);
      }
    }
  }
};

/** l10n functions */
function $T() {
  var args = $A(arguments),
      key = args[0];
  args[0] = (window.locales ? (window.locales[key] || key) : key);
  return printf.apply(null, args);
}

function closeWindowByEscape(e) {
  if(Event.key(e) == 27){
    e.stop();
    window.close();
  }
}