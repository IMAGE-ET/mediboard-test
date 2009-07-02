/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function main() {
  try {
    if (sessionLocked) Session.lock();
    prepareForms();
    SystemMessage.init();
    WaitingMessage.init();
    initPuces();
    Main.init();
  }
  catch (e) {
    Console.debugException(e);
  }
}

document.observe('dom:loaded', main);

/**
 * Main page initialization scripts
 */
var Main = {
  scripts: [],
  initialized: false,
  
  /**
   * Add a script to be lanuched after onload notification
   * On the fly execution if already page already loaded
   */
  add: function(script) {
    if (this.initialized) {
      script();
    }
    else {
      this.scripts.push(script);
    }
  },
  
  /**
   * Call all Main functions
   */
  init: function() {
    this.scripts.each(function(e) { e() } );
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
    var i, j, e, elements = obj.descendants();
    for (j = 0; j < elements.length; j++) {
      if (e = elements[j]) {
        if (e.attributes) {
          for (i = 0; i < e.attributes.length ; i++) {
            if (Object.isFunction(e.attributes[i])) e.attributes[i] = null;
          }
        }
        Element.remove(e);
      }
    }
  }
};

var WaitingMessage = {
  init: function() {
    window.onbeforeunload = function () {
      if(FormObserver.checkChanges()) {
        WaitingMessage.show();
      } else {
        return "Vous avez modifié certaines informations sur cette page sans les sauvegarder. Si vous appuyez sur OK, ces données seront perdues.";
      }
    };
  },
  
  show: function() {
    var doc  = document.documentElement,
		    mask = $('waitingMsgMask'),
				text = $('waitingMsgText');
				
    if (!mask && !text) return;
  
    // Display waiting text
    var vpd = document.viewport.getDimensions(),
		    etd = text.getDimensions();
				
    text.setStyle({
      top: (vpd.height - etd.height)/2 + "px",
      left: (vpd.width  - etd.width) /2 + "px",
      zIndex: 101,
      opacity: 0.8
    }).show();
    
    // Display waiting mask
    mask.setStyle({
      top: 0,
      left: 0,
      height: doc.scrollHeight + "px",
      width: doc.scrollWidth + "px",
      zIndex: 100,
      opacity: 0.2
    }).show();
  },
  
  cover: function(element) {
    element = $(element);
    
    var cover = new Element("div").addClassName('ajax-loading').hide(),
		    descendant = $(element).down();
    
    /** If the element is a TR, we add the div to the firstChild to avoid a bad page render (a div in a <table> or a <tr>)*/
    if (descendant && descendant.tagName.match(/^tr$/i)) {
			descendant.insert({bottom: cover});
		}
	  else {
	    element.insert({bottom: cover});
	  }
		
		cover.setStyle({
      opacity: 0.3,
      position: 'absolute'
    }).clonePosition(element).show();
  }
};

function createDocument(oSelect, consultation_id) {
  if (modele_id = oSelect.value) {
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", consultation_id);
    url.popup(700, 700, "Document");
  }
  
  oSelect.value = "";
}

function closeWindowByEscape(e) {
  if(getKeycode(e) == 27){
  	e.stop();
    window.close();
  }
}

var AjaxResponse = {
  onDisconnected: function() {
    if (window.children['login'] && window.children['login'].closed) window.children['login'] = null;

    if (!window.children['login']) {
      loginUrl = new Url;
      loginUrl.addParam("dialog", 1);
      loginUrl.pop(610, 300, "login");
    }
  },
  
  onPerformances: Prototype.emptyFunction
};


/**
 * System message effects
 */
var SystemMessage = {
  id: "systemMsg",
  autohide: null,
  effect: null,

  // Check message type (loading, notice, warning, error) from given div
  checkType: function() {
    this.autohide = $A($(this.id).childNodes).pluck("className").compact().last() == "message";
  },

  // show/hide the div
  doEffect : function (delay, forceFade) {
    // Cancel current effect
    if (this.effect) {
      this.effect.cancel();
      this.effect = null;
    }
      
    // Ensure visible        
    $(this.id).show().setOpacity(1);
    
    // Only hide on type 'message'
    this.checkType();
    if (!this.autohide && !forceFade) {
      return;
    }
    
    // Program fading
    this.effect = new Effect.Fade(this.id, { delay : delay || 5} );
  },
  
  init : function () {
    var element = $(this.id);
    Assert.that(element, "No system message div");
    
    // Hide on onclick
    element.observe('click', function(event) {
      SystemMessage.doEffect(0.1, true);
    });
        
    // Hide empty message immediately
    if (!element.innerHTML.strip()) {
      SystemMessage.doEffect(0.1, true);
      return;
    }
    
    SystemMessage.doEffect();
  }
};

/**
 * Javascript console
 */
var Console = {
  id: "console",

  hide: function() {
    $(this.id).hide();
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

    $(this.id).insert(eDiv);
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
  
  debugException: function(exception) {
    var regexp = /([^@])+@(http[s]?:([^:]+))?:([\d]+)/g;
    if (exception.stack) exception.stack = exception.stack.match(regexp);
    this.debug(exception, "Exception", { level: 2 } );
  },
  
  debug: function(oValue, sLabel, oOptions) {
    if (Preferences.INFOSYSTEM != "1") {
      return;
    }
  
    sLabel = sLabel || "Value";

    var oDefault = {
      level: 1,
      current: 0
    };
      
    Object.extend(oDefault, oOptions);
  
    if (oDefault.current > oDefault.level) {
      return;
    }
            
    try {
      this.trace(sLabel + ": ", "key", oDefault.current);
      
      if (oValue === null) {
        this.trace("null", "value");
        return;
      }
      
      switch (typeof oValue) {
        case "undefined": 
          this.trace("undefined", "value");
          break;
        
        case "object":
          oDefault.current++;
          if (oValue instanceof Array) {
            this.trace("[Array]", "value");
            oValue.each(function(value) { 
              Console.debug(value, "", oDefault);
            } );
          } else {
            this.trace(oValue, "value");
            $H(oValue).each(function(pair) {
              Console.debug(pair.value, pair.key, oDefault);
              
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
    
    var oDefault = {
      level: 1,
      current: 0
    };
      
    Object.extend(oDefault, oOptions);
    
    oElement = $(oElement);
    
    var oNoRecursion = { 
      level: oDefault.current, 
      current: oDefault.current
    };
    
    this.debug(oElement, sLabel, oNoRecursion);

    oDefault.current++;

    if (oDefault.current > oDefault.level) {
      return;
    }
            
    oNoRecursion = { 
      level: oDefault.current, 
      current: oDefault.current
    };

    // Text nodes don't have tagName
    if (oElement.tagName) {
      this.debug(oElement.tagName.toLowerCase(), "tagName",  oNoRecursion);
    }
    
    if (oElement instanceof Text) {
      this.debug(oElement.textContent, "textContent", oNoRecursion);
    }
    
    $A(oElement.attributes).each( function(oAttribute) {
      Console.debug(oAttribute.nodeValue, "Attributes." + oAttribute.nodeName, oDefault);
    } );

    $A(oElement.childNodes).each( function(oElement) {
      Console.debugElement(oElement, "Element", oDefault)
    } );
  },
  
  error: function (sMsg) {
    this.trace("Error: " + sMsg, "error");
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
	window.console = {
	  debug: Console.debug.bind(window),
	  log: Console.trace
	};
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
      Console.error(sMsg);
    }
  }
};

/**
 * PairEffect Class
 */

var PairEffect = Class.create({

  // Constructor
  initialize: function(idTarget, oOptions) {
    
    var oDefaultOptions = {
      idTarget       : idTarget,
      idTrigger      : idTarget + "-trigger",
      sEffect        : null, // could be null, "appear", "slide", "blind"
      bStartVisible  : false, // Make it visible at start
      bStoreInCookie : true,
      sCookieName    : "effects"
    };

    Object.extend(oDefaultOptions, oOptions);
    
    this.oOptions = oDefaultOptions;
    var oTarget   = $(this.oOptions.idTarget);
    var oTrigger  = $(this.oOptions.idTrigger);

    Assert.that(oTarget, "Target element '%s' is undefined", idTarget);
    Assert.that(oTrigger, "Trigger element '%s' is undefined ", this.oOptions.idTrigger);
  
    // Initialize the effect
    oTrigger.observe("click", this.flip.bind(this));
  
    // Initialize classnames and adapt visibility
    var aCNs = Element.classNames(oTrigger);
    aCNs.add(this.oOptions.bStartVisible ? "triggerHide" : "triggerShow");
    if (this.oOptions.bStoreInCookie) {
      aCNs.load(this.oOptions.sCookieName);
    }
    oTarget.setVisible(!aCNs.include("triggerShow"));   
  },
  
  // Flipper callback
  flip: function() {
    var oTarget = $(this.oOptions.idTarget);
    var oTrigger = $(this.oOptions.idTrigger);
    if (this.oOptions.sEffect && !Prototype.Browser.IE) {
      new Effect.toggle(oTarget, this.oOptions.sEffect);
    } else {
      oTarget.toggle();
    }
  
    var aCNs = Element.classNames(oTrigger);
    aCNs.flip("triggerShow", "triggerHide");
    
    if (this.oOptions.bStoreInCookie) {
      aCNs.save(this.oOptions.sCookieName);
    }
  }
} );

/**
 * PairEffect utiliy function
 */

Object.extend(PairEffect, {
  declaredEffects : {},

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }
    
    Object.extend(oDefaultOptions, oOptions);
    
    $$('.'+sTargetsClass).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oDefaultOptions.bStartAllVisible || (oElement.id == oDefaultOptions.idStartVisible);
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});


/**
 * TogglePairEffect Class
 */
var TogglePairEffect = Class.create({
  // Constructor
  initialize: function(idTarget1, idTarget2, oOptions) {
    
    var oDefaultOptions = {
      idFirstVisible : 1,
      idTarget1      : idTarget1,
      idTarget2      : idTarget2,
      idTrigger1     : idTarget1 + "-trigger",
      idTrigger2     : idTarget2 + "-trigger"
    };

    Object.extend(oDefaultOptions, oOptions);
    
    this.oOptions = oDefaultOptions;
    var oTarget1  = $(this.oOptions.idTarget1);
    var oTarget2  = $(this.oOptions.idTarget2);
    var oTrigger1 = $(this.oOptions.idTrigger1);
    var oTrigger2 = $(this.oOptions.idTrigger2);

    Assert.that(oTarget1, "Target1 element '%s' is undefined", idTarget1);
    Assert.that(oTarget2, "Target2 element '%s' is undefined", idTarget2);
    Assert.that(oTrigger1, "Trigger1 element '%s' is undefined ", this.oOptions.idTrigger1);
    Assert.that(oTrigger2, "Trigger2 element '%s' is undefined ", this.oOptions.idTrigger2);
  
    // Initialize the effect
    var fShow = this.show.bind(this);
    oTrigger1.observe("click", function() { fShow(2); } );
    oTrigger2.observe("click", function() { fShow(1); } );
    
    this.show(this.oOptions.idFirstVisible);
  },
  
  show: function(iWhich) {
    $(this.oOptions.idTarget1).setVisible(1 == iWhich);
    $(this.oOptions.idTarget2).setVisible(2 == iWhich);
    $(this.oOptions.idTrigger1).setVisible(1 == iWhich);
    $(this.oOptions.idTrigger2).setVisible(2 == iWhich);
  }
});

/**
 * PairEffect utiliy function
 */

Object.extend(TogglePairEffect, {
  declaredEffects : {},

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    };
    
    Object.extend(oDefaultOptions, oOptions);
    
    $A(document.getElementsByClassName(sTargetsClass)).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oDefaultOptions.bStartAllVisible || (oElement.id == oDefaultOptions.idStartVisible);
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});

/**
 * View port manipulation object
 *   Handle view ported objects
 */

var ViewPort = {
  SetAvlHeight: function (element, pct) {
    element = $(element);
    if (!element) return;

    var pos = 0,
		    winHeight = 0;
  
    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    pos       = element.cumulativeOffset()[1];
    winHeight = window.getInnerDimensions().height;
    element.style.overflow = "auto";
    element.style.height = ((winHeight - pos) * pct - 10) + "px";
  },
  
  SetFrameHeight: function(element, options){
    options = Object.extend({
      marginBottom : 15
    }, options || {});
      
    var fYFramePos        = 0;
    var fNavHeight        = 0;
    var fFrameHeight      = 0;
    
    // Calcul de la position top de la frame
    fYFramePos = Position.cumulativeOffset(element)[1];  
    
    // hauteur de la fenetre
    fNavHeight = window.getInnerDimensions().height;
    
    // Calcul de la hauteur de la div
    fFrameHeight = fNavHeight - fYFramePos;
    
    element.setAttribute("height", fFrameHeight - options.marginBottom);
  }
};

/** Token field used to manage multiple enumerations easily.
 *  @param element The element used to get piped values : token1|token2|token3
 *  @param options Accepts the following keys : onChange, confirm, sProps
 */
var TokenField = Class.create({
  initialize: function(element, options) {
    this.element = element;
    
    this.options = Object.extend({
      onChange: Prototype.emptyFunction,
      confirm : null,
      sProps  : null
    }, options || {});
  },
  onComplete: function(value) {
    if(this.options.onChange != null)
      this.options.onChange(value);
    return true;
  },
  add: function(value, multiple) {
    if (!value) {
      return false;
    }
    if(this.options.sProps) {
      oCode = new Element('input', {value: value, className: this.options.sProps});
      ElementChecker.prepare(oCode);
      ElementChecker.checkElement();
      if(ElementChecker.oErrors.length) {
        alert(ElementChecker.getErrorMessage());
        return false;
      }
    }
    var aToken = this.getValues();
    aToken.push(value);
    if(!multiple) aToken = aToken.uniq();
    
    this.onComplete(this.setValues(aToken));
    return true;
  },
  remove: function(value) {
    if(!value || (this.options.confirm && !confirm(this.options.confirm))) {
      return false;
    }

    this.onComplete(this.setValues(this.getValues().without(value)));
    return true;
  },
  contains: function(value) {
   return (this.getValues().indexOf(value) != -1);
  },
  toggle: function(value, force, multiple) {
    if (!Object.isUndefined(force)) {
      return this[force?"add":"remove"](value, multiple);
    }
    return this[this.contains(value)?"remove":"add"](value);
  },
  getValues: function(asString) {
    if (asString) {
      return this.element.value;
    }
    return this.element.value.split("|").without("");
  },
  setValues: function(values) {
    if (Object.isArray(values)) {
      values = values.join("|");
    }
    this.onComplete(this.element.value = values);
    return values;
  }
});

function view_log(classe, id) {
  url = new Url("system", "view_history");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.addParam("user_id", "");
  url.addParam("type", "");
  url.popup(600, 500, "history");
}

function guid_log(guid) {
  var parts = guid.split("-");
  view_log(parts[0], parts[1]);
}

function view_idsante400(classe, id) {
  url = new Url("dPsante400", "view_identifiants");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.popup(750, 400, "sante400");
}

function guid_ids(guid) {
  var parts = guid.split("-");
  view_idsante400(parts[0], parts[1]);
}

function uploadFile(classe, id, categorie_id, file_rename){
  url = new Url("dPfiles", "upload_file");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.addParam("file_category_id", categorie_id);
  url.addParam("file_rename", file_rename);
  url.popup(600, 200, "uploadfile");
}

var Note = Class.create({
  initialize: function() {
    this.url = new Url("system", "edit_note");
  },
  create: function (classe, object_id) {
    this.url.addParam("object_class", classe);
    this.url.addParam("object_id", object_id);
    this.popup();
  },
  edit: function(note_id) {
    this.url.addParam("note_id", note_id);
    this.popup();
  },
  popup: function () {
    this.url.popup(600, 300, "note");
  }
});

// *******
var notWhitespace   = /\S/;
Dom = {
  writeElem : function(elem_replace_id,elemReplace){
    elem = $(elem_replace_id);
    while (elem.firstChild) {
      elem.removeChild(elem.firstChild);
    }
    if(elemReplace){
      elem.appendChild(elemReplace);
    }
  },
  
  cloneElemById : function(id,withChildNodes){
    var elem = $(id).cloneNode(withChildNodes);
    elem.removeAttribute("id");
    return elem;
  },
  
  createTd : function(sClassname, sColspan){
    var oTd = document.createElement("td");
    if(sClassname){
      oTd.className = sClassname;
    }
    if(sColspan){
      oTd.setAttribute("colspan" , sColspan); 
    }
    return oTd;
  },
  
  createTh : function(sClassname, sColspan){
    var oTh = document.createElement("th");
    if(sClassname){
      oTh.className = sClassname;
    }
    if(sColspan){
      oTh.setAttribute("colspan" , sColspan); 
    }
    return oTh;
  },
  
  createImg : function(sSrc){
    var oImg = document.createElement("img");
    oImg.setAttribute("src", sSrc);
    return oImg;
  },
  
  createInput : function(sType, sName, sValue){
    var oInput = document.createElement("input");
    oInput.setAttribute("type"  , sType);
    oInput.setAttribute("name"  , sName);
    oInput.setAttribute("value" , sValue);
    return oInput;
  },
  
  createSelect : function(sName){
    var oSelect = document.createElement("select");
    oSelect.setAttribute("name"  , sName);
    return oSelect;
  },
  
  createOptSelect : function(sValue, sName, selected, oInsertInto){
    var oOpt = document.createElement("option");
    oOpt.setAttribute("value" , sValue);
    if(selected && selected == true){
      oOpt.setAttribute("selected" , "selected");
    }
    oOpt.innerHTML = sName;
    if(!oInsertInto){
      return oOpt;
    }
    oInsertInto.appendChild(oOpt);
  },
  
  cleanWhitespace : function(node){
    if(node.hasChildNodes()){
      for(var i=0; i< node.childNodes.length; i++){
        var childNode = node.childNodes[i];
        if((childNode.nodeType == Node.TEXT_NODE) && (!notWhitespace.test(childNode.nodeValue))){
          node.removeChild(node.childNodes[i]);
          i--;
        }else if (Object.isElement(childNode)) {
          Dom.cleanWhitespace(childNode);
        } 
      }
    }
  }
};

/** Levenstein function **/

function levenshtein( str1, str2 ) {
  // http://kevin.vanzonneveld.net
  // +   original by: Carlos R. L. Rodrigues
  // *     example 1: levenshtein('Kevin van Zonneveld', 'Kevin van Sommeveld');
  // *     returns 1: 3

  var s, l = (s = str1.split("")).length, t = (str2 = str2.split("")).length, i, j, m, n;
  if(!(l || t)) return Math.max(l, t);
  for(var a = [], i = l + 1; i; a[--i] = [i]);
  for(i = t + 1; a[0][--i] = i;);
  for(i = -1, m = s.length; ++i < m;){
    for(j = -1, n = str2.length; ++j < n;){
      a[(i *= 1) + 1][(j *= 1) + 1] = Math.min(a[i][j + 1] + 1, a[i + 1][j] + 1, a[i][j] + (s[i] != str2[j]));
    }
  }
  return a[l][t];
}

function luhn (code) {
  var code_length = code.length;
  var sum = 0;
  var parity = code_length % 2;
  
  for (var i = code_length - 1; i >= 0; i--) {
    var digit = code.charAt(i);
    
    if (i % 2 == parity) {
      digit *= 2;
      
      if (digit > 9) {
        digit -= 9;
      }
    }
    
    sum += parseInt(digit);
  }
  
  return ((sum % 10) == 0);
}


/* Control tabs creation. It saves selected tab into a cookie name TabState */
Object.extend (Control.Tabs, {
  storeTab: function (tabName, tab) {
    new CookieJar().setValue("TabState", tabName, tab);
  },
  loadTab: function (tabName) {
    return new CookieJar().getValue("TabState", tabName);
  },
  create: function (name, storeInCookie) {
    if ($(name)) {
      var tab = new Control.Tabs(name);
      if (storeInCookie) {
        tab.options.afterChange = function (tab, tabName) {
          Control.Tabs.storeTab(name, tab.id);
        }
				var tabName = Control.Tabs.loadTab(name);
				if (tabName) {
					tab.setActiveTab(tabName);
				}
      }
      return tab;
    }
  }
});


Class.extend (Control.Tabs, {
  changeTabAndFocus: function(iIntexTab, oField) {
    this.setActiveTab(iIntexTab);
    if (oField) {
      oField.focus();
    } else {
      var oForm = $$('form')[0];
      if (oForm) {
        oForm.focusFirstElement();
      }
    }
  }
});

window.getInnerDimensions = function() {
  return {width: document.documentElement.clientWidth, height: document.documentElement.clientHeight};
}

/** DOM element creator for Prototype by Fabien Ménager
 *  Inspired from Michael Geary 
 *  http://mg.to/2006/02/27/easy-dom-creation-for-jquery-and-prototype
 **/
var DOM = {
  defineTag: function (tag) {
    DOM[tag] = function () {
      return DOM.createNode(tag, arguments);
    };
  },
  
  createNode: function (tag, args) {
    var e;
    try {
      e = new Element(tag, args[0]);
      for (var i = 1; i < args.length; i++) {
        var arg = args[i];
        if (arg == null) continue;
        if (!Object.isArray(arg)) e.insert (arg);
        else {
          for (var j = 0; j < arg.length; j++) e.insert(arg[j]);
        }
      }
    }
    catch (ex) {
      Console.error('Cannot create <' + tag + '> element:\n' + Object.inspect(args) + '\n' + ex.message);
      e = null;
    }
    return e;
  },
  
  tags: [
    'a', 'br', 'button', 'canvas', 'div', 'fieldset', 'form',
    'h1', 'h2', 'h3', 'h4', 'h5', 'hr', 'img', 'input', 'label', 
    'legend', 'li', 'ol', 'optgroup', 'option', 'p', 'pre', 
    'select', 'span', 'strong', 'table', 'tbody', 'td', 'textarea',
    'tfoot', 'th', 'thead', 'tr', 'tt', 'ul'
  ]
};

DOM.tags.each(function (tag) {
  DOM.defineTag (tag);
});


/** l10n functions */
function $T(key) {
  return window.locales ? (window.locales[key] || key) : key;
}

// Replacements for the javascript alert() and confirm()
var Modal = {
  alert: function(message, options) {
    options = Object.extend({
      className: 'modal alert big-warning',
      okLabel: 'OK',
      onValidate: Prototype.emptyFunction,
      closeOnClick: null
    }, options || {});
    
    var html = '<div style="min-height: 3em;"></div><div style="text-align: center; margin-left: -3em;"><button class="tick" type="button">'+options.okLabel+'</button></div>';
    var m = Control.Modal.open(html, options);
    m.container.select('div').first().update(message);
    m.container.select('button.tick').first().observe('click', (function(){this.close(); options.onValidate();}).bind(m));
  },
  
  confirm: function(message, options) {
    options = Object.extend({
      className: 'modal confirm big-info',
      yesLabel: 'Oui',
      noLabel: 'Non',
      onValidate: Prototype.emptyFunction,
      closeOnClick: null
    }, options || {});
    
    var html = '<div style="min-height: 3em;"></div><div style="text-align: center; margin-left: -3em;">'+
      '<button class="tick" type="button">'+options.yesLabel+'</button>'+
      '<button class="cancel" type="button">'+options.noLabel+'</button>'+
    '</div>';
    var m = Control.Modal.open(html, options);
    m.container.select('div').first().update(message);
    m.container.select('button.tick').first().observe('click', (function(){this.close(); options.onValidate(true);}).bind(m));
    m.container.select('button.cancel').first().observe('click', (function(){this.close(); options.onValidate(false);}).bind(m));
  }, 
  
  open: function(container, options) {
    options = Object.extend({
      className: 'modal',
      closeOnClick: null,
      overlayOpacity: 0.5
    }, options || {});
    return Control.Modal.open(container, options);
  }
};
/*
window.open = function(element, title, options) {
  options = Object.extend({
    className: 'modal popup',
    width: 800,
    height: 500,
    iframe: true
  }, options || {});
  
  Control.Modal.open(element, options);
  return false;
}*/


window.modal = function(container, options) {
  options = Object.extend({
    className: 'modal',
    closeOnClick: null,
    overlayOpacity: 0.5
  }, options || {});
  return Control.Modal.open(container, options);
};

var Session = {
  window: null,
  lock: function(){
    var url = new Url;
    url.addParam("lock", true);
    url.requestUpdate("systemMsg", {
      method: "post",
      getParameters: {m: 'admin', a: 'ajax_unlock_session'}
    });
    var container = $('sessionLock');
    this.window = Modal.open(container);
    
    container.select('form').first().reset();
    container.select('input[type=text], input[type=password]').first().focus();
    
    $('main').hide();
  },
  request: function(form){
    var url = new Url;
    url.addElement(form.password);
    url.requestUpdate(form.select('.login-message').first(), {
      method: "post",
      getParameters: {m: 'admin', a: 'ajax_unlock_session'}
    });
    return false;
  },
  unlock: function(){
    this.window.close();
    $('main').show();
    return false;
  },
  close: function(){
    document.location.href = '?logout=-1';
  }
};

var UserSwitch = {
  window: null,
  popup: function(){
    var container = $('userSwitch');
    this.window = Modal.open(container);
    
    container.select('form').first().reset();
    container.select('input[type=text], input[type=password]').first().focus();
    document.observe('keydown', function(e){
      if (Event.key(e) == 27) UserSwitch.cancel();
    });
  },
  login: function(form){
    if (!checkForm(form)) return false;
    var url = new Url;
    url.addElement(form.username);
    url.addElement(form.password);
    url.requestUpdate(form.select('.login-message').first(), {
      method: "post",
      getParameters: {
        m: 'admin',
        a: 'ajax_login_as'
      }
    });
    return false;
  },
  cancel: function(){
    this.window.close();
  }
};
