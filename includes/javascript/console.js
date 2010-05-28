/**
 * Javascript console
 */
var Console = {
  id: "console",

  hide: function() {
    $(this.id).hide();
  },

  toggle: function() {
    $(this.id).down(".body").toggle();
  },
  
  trace: function(sContent, sClass, nIndent) {
    sClass = sClass || "label";
    
    if(Preferences.INFOSYSTEM == 1) {
      Element.show(this.id);
    }
    
    var eDiv = new Element("div", {className: sClass});
    eDiv.innerHTML = sContent.toString().escapeHTML();

    if (nIndent) {
      eDiv.setStyle({ marginLeft: nIndent + "em" });
    }

    $(this.id).down(".body").insert(eDiv);
    Console.scrollDown.defer();
  },
  
  scrollDown: function() {
    $(Console.id).scrollTop = $(Console.id).scrollHeight;
  },
  
  traceValue: function(oValue) {
    if (oValue === null) {
      this.trace("null", "value");
      return;
    }
    
    switch (typeof oValue) {
      case "undefined": 
        this.trace("undefined", "value");
        break;
      
      case "object":
        if (oValue instanceof Array) {
          this.trace(">> [Array]", "value");
        } else {        
          this.trace(">> " + oValue, "value");
        }
        break;

      case "function":
        this.trace(">> Function" + oValue.getSignature(), "value");
        break;

      case "string":
        this.trace("'" + oValue + "'", "value");
        break;

      default:
        this.trace(oValue, "value");
    }
  },
  
  error: function(exception) {
    var regexp = /([^@])+@(http[s]?:([^:]+))?:([\d]+)/g;
    exception.stack = (exception.stack || exception.stacktrace || "").match(regexp);
    this.debug(exception, "Exception", { level: 2 } );
  },
  
  debug: function(oValue, sLabel, oOptions) {
    if (Preferences.INFOSYSTEM != 1) {
      return;
    }
  
    sLabel = sLabel || "Value";

    oOptions = Object.extend({
      level: 1,
      current: 0
    }, oOptions);
  
    if (oOptions.current > oOptions.level) {
      return;
    }
            
    try {
      this.trace(sLabel + ": ", "key", oOptions.current);
      
      if (oValue === null) {
        this.trace("null", "value");
        return;
      }
      
      switch (typeof oValue) {
        case "undefined": 
          this.trace("undefined", "value");
          break;
        
        case "object":
          oOptions.current++;
          if (oValue instanceof Array) {
            this.trace("[Array]", "value");
            oValue.each(function(value) { 
              Console.debug(value, "", oOptions);
            } );
          } else {
            this.trace(oValue, "value");
            $H(oValue).each(function(pair) {
              Console.debug(pair.value, pair.key, oOptions);
              
            } );
          }
          break;
  
        case "function":
          this.trace("[Function] : " + oValue.getSignature(), "value");
          break;
  
        case "string":
          this.trace("'" + oValue + "'", "value");
          break;
  
        default:
          this.trace(oValue, "value");
      }

    }
    catch(e) {
      this.trace("[couldn't get value]", "error");
    }
  },
  
  debugElement: function(oElement, sLabel, oOptions) {
    sLabel = sLabel || "Element";
    
    oOptions = Object.extend({
      level: 1,
      current: 0
    }, oOptions);
    
    oElement = $(oElement);
    
    var oNoRecursion = { 
      level: oOptions.current, 
      current: oOptions.current
    };
    
    this.debug(oElement, sLabel, oNoRecursion);

    oOptions.current++;

    if (oOptions.current > oOptions.level) {
      return;
    }
            
    oNoRecursion = { 
      level: oOptions.current, 
      current: oOptions.current
    };

    // Text nodes don't have tagName
    if (oElement.tagName) {
      this.debug(oElement.tagName.toLowerCase(), "tagName",  oNoRecursion);
    }
    
    if (oElement instanceof Text) {
      this.debug(oElement.textContent, "textContent", oNoRecursion);
    }
    
    $A(oElement.attributes).each( function(oAttribute) {
      Console.debug(oAttribute.nodeValue, "Attributes." + oAttribute.nodeName, oOptions);
    } );

    $A(oElement.childNodes).each( function(oElement) {
      Console.debugElement(oElement, "Element", oOptions)
    } );
  },
  
  error: function (sMsg) {
    this.trace("Error: " + sMsg, "error");
  },
  
  warn: function (sMsg) {
    this.trace("Warning: " + sMsg, "warning");
  },
  
  start: function() {
    this.dStart = new Date;
  },
  
  stop: function() {
    var dStop = new Date;
    this.debug(dStop - this.dStart, "Duration in milliseconds");
    this.dStart = null;
  }
};

// If there is no console object, it uses the Mediboard Console
if (typeof console === "undefined") {
  window.console = Console;
  Console.log = Console.debug;
}

/**
 * Assert utility object
 */ 
var Assert = {
  that: function (bPredicate, sMsg) {
    if (!bPredicate) {
      var aArgs = $A(arguments);
      aArgs.shift();
      sMsg = printf.apply(null, aArgs);
      console.error(new Error(sMsg));
    }
  }
};
