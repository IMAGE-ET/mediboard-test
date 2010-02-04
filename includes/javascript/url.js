/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Url Class
 * Lazy poping and ajaxing
 */

window.children = {};

Ajax.Responders.register({
  onCreate: function(e) {
    Url.activeRequests[e.method]++;
  },
  onComplete: function(e) {
    Url.activeRequests[e.method]--;
  },
  onException: function(e) {
    Url.activeRequests[e.method]--;
  }
});

var Url = Class.create({
  initialize: function(sModule, sAction, sType) {
    if(!sType) sType = "action";
    this.oParams = {};
    this.oWindow = null;
    this.sFragment = null;
  	this.oPrefixed = {};
  	if(sModule && sAction) {
  	  if(sType == "action") {
   	    this.setModuleAction(sModule, sAction);
   	  } else {
   	    this.setModuleTab(sModule, sAction);
   	  }
  	}
  },
  
  setModuleAction: function(sModule, sAction) {
    return this.addParam("m", sModule)
               .addParam("a", sAction);
  },
  
  setModuleTab: function(sModule, sTab) {
    return this.addParam("m", sModule)
               .addParam("tab", sTab);
  },
  
  setModuleDosql: function(sModule, sDosql) {
    return this.addParam("m", sModule)
               .addParam("dosql", sDosql);
  },
  
  setFragment: function(sFragment) {
    this.sFragment = sFragment;
    return this;
  },
  
  addParam: function(sName, sValue) {
    this.oParams[sName] = sValue;
    return this;
  },
  
  addObjectParam: function(sName, oObject) {
    if (typeof oObject != "object") {
      return this.addParam(sName, oObject);
    }
    
    // Recursive call
    $H(oObject).each( function(pair) {
      this.addObjectParam(printf("%s[%s]", sName, pair.key), pair.value);
    }, this);
    
    return this;
  },
  
  addFormData: function(oForm) {
    Object.extend(this.oParams, getForm(oForm).serialize(true));
    return this;
  },
  
  mergeParams: function(oObject) {
    Object.extend(this.oParams, oObject);
    return this;
  },
  
  addElement: function(oElement, sParamName) {
    if (!oElement) return this;
  
    if (!sParamName) {
      sParamName = oElement.name;
    }
  
    return this.addParam(sParamName, oElement.value);
  },
  
  make: function() {
    var sUrl = "?" + $H(this.oParams).toQueryString();
    if (this.sFragment) sUrl += "#"+this.sFragment;
    return sUrl;
  },
  
  redirect: function() {
    var uri = decodeURI(this.make());
    if(this.oWindow)
      this.oWindow.location.href = uri;
    else
      window.location.href = uri;
    
    return this;
  },
  
  redirectOpener: function() {
    if (window.opener) {
      window.opener.location.href = this.make();
    } 
		else {
      this.redirect();
    }
  },
	
  pop: function(iWidth, iHeight, sWindowName, sBaseUrl, sPrefix) {
    this.addParam("dialog", 1);
  
    var iLeft = 50;
    iWidth = iWidth || 800;
    iHeight = iHeight || 600;
    sWindowName = sWindowName || "";
    sBaseUrl = sBaseUrl || "";
    
    var sFeatures = Url.buildPopupFeatures({left: iLeft, height: iHeight, width: iWidth});
  
    // Pefixed window collection
    if (sPrefix && this.oPrefixed[sPrefix]) {
      this.oPrefixed[sPrefix] = this.oPrefixed[sPrefix].reject(function(oWindow) {
        return oWindow.closed;
      });
          
      // Purge closed windows
      iLeft += (iWidth + 8) * this.oPrefixed[sPrefix].length;
    }
  
    // Forbidden characters for IE
    sWindowName = sWindowName.replace(/[ -]/gi, "_");
    this.oWindow = window.open(sBaseUrl + this.make(), sWindowName, sFeatures);  
    window.children[sWindowName] = this.oWindow;
		
    if (!this.oWindow)
      return this.showPopupBlockerAlert(sWindowName);
    
    // Prefixed window collection
    if (sPrefix) {
      if (!this.oPrefixed[sPrefix]) {
        this.oPrefixed[sPrefix] = [];
      }
      this.oPrefixed[sPrefix].push(this.oWindow);
    }
    
    return this;
  },
  
  popDirect: function(iWidth, iHeight, sWindowName, sBaseUrl) {
    iWidth = iWidth || 800;
    iHeight = iHeight || 600;
    sWindowName = sWindowName || "";
    sBaseUrl = sBaseUrl || "";
    
    var sFeatures = Url.buildPopupFeatures({height: iHeight, width: iWidth});
		
    // Forbidden characters for IE
    sWindowName = sWindowName.replace(/[ -]/gi, "_");
    this.oWindow = window.open(sBaseUrl + this.make(), sWindowName, sFeatures);
    window.children[sWindowName] = this.oWindow;
    
    if (!this.oWindow)
      this.showPopupBlockerAlert(sWindowName);
    
    return this;
  },
  
  popunder: function(iWidth, iHeight, sWindowName) {
    this.pop(iWidth, iHeight, sWindowName);
    this.oWindow.blur();
    window.focus();
    
    return this;
  },
  
  popup: function(iWidth, iHeight, sWindowName, sPrefix) {
    this.pop(iWidth, iHeight, sWindowName, null, sPrefix);
  
    // Prefixed window collection
    if (sPrefix) {
      (this.oPrefixed[sPrefix] || []).each(function (oWindow) { 
        oWindow.blur(); // Chrome issue
        oWindow.focus();
      });
    }

    if (this.oWindow) {
      this.oWindow.blur(); // Chrome issue
      this.oWindow.focus();
    } else {
      this.showPopupBlockerAlert(sWindowName);
    }
      
    return this;
  },
  
  showPopupBlockerAlert: function(popupName){
    Modal.alert($T("Popup blocker alert", popupName));
    return this;
  },
  
  autoComplete: function(input, populate, oOptions) {
    oOptions = Object.extend({
      minChars: 2,
      frequency: 0.5,
			width: null,
      dropdown: false,
      valueElement: null,
	    
	    // Allows bigger width than input
			onShow: function(element, update) { 
        update.style.position = 'absolute';
        Position.clone(element, update, {
          setWidth: false,
          setHeight: false, 
          offsetTop: element.offsetHeight
        });

        if (oOptions.width) {
          update.style.width = oOptions.width;
        }
        else {
          update.style.width = "auto";
					update.style.whiteSpace = "nowrap"; 
          update.style.minWidth = $(input).getDimensions().width+"px";
          update.style.maxWidth = "400px";
        }
        update.show().setOpacity(1).unoverflow();
      },
      
      onHide: function(element, update){ Element.hide(update) }
    }, oOptions);
    
    input = $(input).addClassName("autocomplete");
    
    populate = $(populate);
    if (!populate) {
      populate = new Element("div").addClassName("autocomplete").hide();
      input.insert({after: populate});
    }

    // Autocomplete
    this.addParam("ajax", 1);
    this.addParam("suppressHeaders", 1);
    
    if (oOptions.valueElement) {
      oOptions.afterUpdateElement = function(input, selected) {
        var valueElement = $(selected).down(".value");
        var value = valueElement ? valueElement.innerHTML.strip() : selected.innerHTML.stripTags().strip();
        $V(oOptions.valueElement, value);
      };
      
      var clearElement = function(){
        if ($V(input) == "") {
          $V(oOptions.valueElement, "");
        }
      };
      
      input.observe("change", clearElement).observe("ui:change", clearElement);
    }
    
    var autocompleter = new Ajax.Autocompleter(input, populate, this.make(), oOptions);
    
    autocompleter.startIndicator = function(){
      if(this.options.indicator) Element.show(this.options.indicator);
      input.addClassName("throbbing");
    };
    autocompleter.stopIndicator = function(){
      if(this.options.indicator) Element.hide(this.options.indicator);
      input.removeClassName("throbbing");
    };
    
    // Drop down button, like <select> tags
    if (oOptions.dropdown) {
      var container = new Element("div", {style: "border:none;margin:0;padding:0;position:relative;display:inline-block"}).addClassName("dropdown"),
          height = (input.getHeight() || 18)-2,
          margin = parseInt(input.getStyle("marginTop"));
      
      container.setStyle({paddingRight: (height+3)+'px'}).
                clonePosition(input, {setLeft: false, setTop: false});
      
      if (!container.getWidth()) {
        container.style.width = null;
        input.style.marginRight = "-1px";
      }
      
      input.wrap(container);
      container.insert(populate);
      
      // The trigger button
      var trigger = new Element("div", {
        style:"padding:0;position:absolute;right:0;top:0;width:"+height+"px;height:"+height+"px;margin:"+margin+"px;cursor:pointer;"
      }).addClassName("dropdown-trigger");
      
      trigger.insert(new Element("div", {style: "position:absolute;right:0;top:0;left:0;bottom:0;"}));
      
      // Hide the list
      var hideAutocomplete = function(e){
        autocompleter.onBlur(e);
      }.bindAsEventListener(this);
      
      // Show the list
      var showAutocomplete = function(e, dontClear){
        if (!dontClear) {
          $V(input, '');
          input.fire("ui:change");
        }
        input.focus();
        autocompleter.activate.bind(autocompleter)();
        Event.stop(e);
        document.observeOnce("mousedown", hideAutocomplete);
      };
      
      // Bind the events
      trigger.observe("mousedown", showAutocomplete.bindAsEventListener(this));
      input.observe("click", showAutocomplete.bindAsEventListener(this, true));
      input.observe("focus", function(){
        if (oOptions.valueElement && oOptions.valueElement.value == "")
          input.value = "";
        else 
          input.select();
          
        input.fire("ui:change");
        autocompleter.activate.bind(autocompleter)();
      });
      populate.observe("mousedown", Event.stop);
      
      container.insert(trigger);
    }
    
    return autocompleter;
  },
  
  close: function() {
    if(this.oWindow) this.oWindow.close();
    return this;
  },
  
  requestUpdate: function(ioTarget, oOptions) {
    this.addParam("suppressHeaders", 1);
    this.addParam("ajax", 1);
    
    var element = $(ioTarget);
    
    if (!element) {
      console.warn(ioTarget+" doesn't exist");
      return;
    }
    
    oOptions = Object.extend( {
      waitingText: null,
      urlBase: "",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      getParameters: null,
      onComplete: Prototype.emptyFunction,
      onFailure: function(){ element.update('<div class="error">Le serveur rencontre quelques problèmes.</div>');}
    }, oOptions);
    
    oOptions.onComplete = oOptions.onComplete.wrap(function(onComplete) {
      prepareForms(element);
      initNotes();
      onComplete();
    });
    
    // Empty holder gets a div for load notifying
    if (!/\S/.test(element.innerHTML)) {
      element.update('<div style="height: 2em;" />');
    }

    // Animate system message
    if (element.id == SystemMessage.id) {
      oOptions.waitingText = $T("Loading in progress");
      SystemMessage.doEffect();
    }
    // Cover div
    else {
      WaitingMessage.cover(element);
    }
  	
    if (oOptions.waitingText) {
      element.update('<div class="loading">' + oOptions.waitingText + '...</div>');
    }

    var getParams = oOptions.getParameters ? "?" + $H(oOptions.getParameters).toQueryString() : '';
    new Ajax.Updater(element, oOptions.urlBase + "index.php" + getParams, oOptions);
    
    return this;
  },
  
  requestJSON: function(fCallback, oOptions) {
    this.addParam("suppressHeaders", 1);
    this.addParam("ajax", "");
  
    oOptions = Object.extend({
      urlBase: "",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      evalJSON: 'force',
      getParameters: null
    }, oOptions);
    
    oOptions.onSuccess = function(transport){fCallback(transport.responseJSON)};
  	
    var getParams = oOptions.getParameters ? "?" + $H(oOptions.getParameters).toQueryString() : '';
    new Ajax.Request(oOptions.urlBase + "index.php" + getParams, oOptions);
    
    return this;
  },
  
  requestUpdateOffline: function(ioTarget, oOptions) {
    if (typeof netscape != 'undefined' && typeof netscape.security != 'undefined') {
      netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
    }
    
    this.addParam("_syncroOffline", 1);
    if(config.date_synchro){
      this.addParam("_synchroDatetime" , config.date_synchro);
    }
    
    oOptions = Object.extend({
      urlBase: config.urlMediboard
    }, oOptions);

    this.requestUpdate(ioTarget, oOptions);
    
    return this;
  },
  
  periodicalUpdate: function(ioTarget, oOptions) {
    this.addParam("suppressHeaders", 1);
    this.addParam("ajax", 1);
    
    var element = $(ioTarget);
    if (!element) {
      console.warn(ioTarget+" doesn't exist");
      return;
    }

    // Empty holder gets a div for load notifying
    if (!/\S/.test(element.innerHTML)) {
      element.update('<div style="height: 2em" />');
    }

    oOptions = Object.extend({
      onCreate: WaitingMessage.cover.curry(element),
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      onComplete: Prototype.emptyFunction
    }, oOptions);
    
    oOptions.onComplete = oOptions.onComplete.wrap(function(onComplete) {
      prepareForms(element);
      onComplete();
    });
		
    return new Ajax.PeriodicalUpdater(element, "index.php", oOptions);
  },
  
  ViewFilePopup: function(objectClass, objectId, elementClass, elementId, sfn){
    this.setModuleAction("dPfiles", "preview_files");
    this.addParam("popup", 1);
    this.addParam("objectClass", objectClass);
    this.addParam("objectId", objectId);
    this.addParam("elementClass", elementClass);
    this.addParam("elementId", elementId);
    if(sfn != 0){
      this.addParam("sfn", sfn);
    }
    this.popup(750, 550, "Fichier");
  }
} );

Url.activeRequests = {
  post: 0,
  get: 0
};

Url.buildPopupFeatures = function(features) {
  features = Object.extend({
    left: 50,
    top: 50,
    height: 600,
    width: 800,
    scrollbars: true,
    resizable: true,
    menubar: true
  }, features);
  
  var a = [], value;
  $H(features).each(function(f){
    value = (f.value === true ? 'yes' : (f.value === false ? 'no' : f.value));
    a.push(f.key+'='+value);
  });
  
  return a.join(',');
};

/** General purpose ping
 *  @return {Boolean} true if user is connected, false otherwise
 */
Url.ping = function(options) {
  var url = new Url("system", "ajax_ping");
  url.addParam("suppressHeaders", 1);
  url.addParam("ajax", 1);
  url.requestUpdate("systemMsg", options);
};

/** Parses the URL to extract its components
 * Based on the work of Steven Levithan <http://blog.stevenlevithan.com/archives/parseuri>
 * @param {String} url - The URL to parse
 * @return {Object} The URL components
 */
Url.parse = function(url) {
  url = url || location.href;

  var keys = ["source","scheme","authority","userInfo","user","pass","host","port","relative","path","directory","file","query","fragment"],
      regex = /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      m = regex.exec(url),
      c = {},
      i = keys.length;

  while (i--) c[keys[i]] = m[i] || "";

  return c;
};
