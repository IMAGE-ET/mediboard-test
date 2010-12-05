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
        errorMsg: errorMsg + _IEAdditionalInfo,
        url: url,
        lineNumber: lineNumber,
        stack: exception.stack || exception.stacktrace || printStackTrace(),
        location: location.href
      }).toQueryString()
    });
  } catch (e) {}
  return true;
};

/*
 * @author Rob Reid
 * @version 20-Mar-09
 * Description: Little helper function to return details about IE 8 and its various compatibility settings either use as it is
 * or incorporate into a browser object. Remember browser sniffing is not the best way to detect user-settings as spoofing is
 * very common so use with caution.
 */
function IEVersion(){
  var na = navigator.userAgent;
  var version = "NA";
  var ieDocMode = "NA";
  var ie8BrowserMode = "NA";
  
  // Look for msie and make sure its not opera in disguise
  if(/msie/i.test(na) && (!window.opera)){
    // also check for spoofers by checking known IE objects
    if(window.attachEvent && window.ActiveXObject){
    
      // Get version displayed in UA although if its IE 8 running in 7 or compat mode it will appear as 7
      version = (na.match( /.+ie\s([\d.]+)/i ) || [])[1];
      
      // Its IE 8 pretending to be IE 7 or in compat mode   
      if(parseInt(version) == 7){
        
        // documentMode is only supported in IE 8 so we know if its here its really IE 8
        if(document.documentMode){
          version = 8; //reset? change if you need to
          
          // IE in Compat mode will mention Trident in the useragent
          if(/trident\/\d/i.test(na))
            ie8BrowserMode = "Compat Mode";
          
          // if it doesn't then its running in IE 7 mode
          else
            ie8BrowserMode = "IE 7 Mode";
        }
      }
      
      else if(parseInt(version)==8){
        // IE 8 will always have documentMode available
        if(document.documentMode) ie8BrowserMode = "IE 8 Mode";
      }
      
      // If we are in IE 8 (any mode) or previous versions of IE we check for the documentMode or compatMode for pre 8 versions     
      ieDocMode = document.documentMode ? document.documentMode : (document.compatMode && document.compatMode == "CSS1Compat") ? 7 : 5; //default to quirks mode IE5               
    }
  }
         
  return {
    UserAgent: na,
    Version: version,
    BrowserMode: ie8BrowserMode,
    DocMode: ieDocMode
  };
}

var _IEAdditionalInfo = "";

// TODO needs testing (doesn't throw console.error every time)
if (Prototype.Browser.IE) {
  try {
    (function(){
      var ieVersion = IEVersion();
      _IEAdditionalInfo = " (Version:"+ieVersion.Version+" BrowserMode:"+ieVersion.BrowserMode+" DocMode:"+ieVersion.DocMode+")";
      
      // If DocMode is the same as the browser version (IE8 not in Compat mode) and IE8+
      if (ieVersion.Version >= 8 && (ieVersion.Version == ieVersion.DocMode) && (ieVersion.BrowserMode != "Compat Mode")) {
        window.onerror = errorHandler;
      }
    })();
  } catch(e) {}
}

// Exclude HTTrack errors
if (/httrack/i.test(navigator.userAgent)) {
  errorHandler = function(){};
}

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