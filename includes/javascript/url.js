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

var Url = Class.create({
  // Constructor
  initialize: function(sModule, sAction) {
    this.oParams = {};
    this.oWindow = null;
    this.sFragment = null;
  	this.oPrefixed = {};
  	if(sModule && sAction) {
  	  this.setModuleAction(sModule, sAction);
  	}
  },
  
  setModuleAction: function(sModule, sAction) {
    this.addParam("m", sModule);
    this.addParam("a", sAction);
  },
  
  setModuleTab: function(sModule, sTab) {
    this.addParam("m", sModule);
    this.addParam("tab", sTab);
  },
  
  setModuleDosql: function(sModule, sDosql) {
    this.addParam("m", sModule);
    this.addParam("dosql", sDosql);
  },
  
  setFragment: function(sFragment) {
    this.sFragment = sFragment;
  },
  
  addParam: function(sName, sValue) {
    this.oParams[sName] = sValue;
  },
  
  addObjectParam: function(sName, oObject) {
    if (typeof oObject != "object") {
      this.addParam(sName, oObject);
      return;
    }
    
    // Recursive call
    $H(oObject).each( function(pair) {
      this.addObjectParam(printf("%s[%s]", sName, pair.key), pair.value);
    }, this);
  },
  
  addFormData: function(oForm) {
    Object.extend(this.oParams, getForm(oForm).serialize(true));
  },
  
  mergeParams: function(oObject) {
    Object.extend(this.oParams, oObject);
  },
  
  addElement: function(oElement, sParamName) {
    if (!oElement) {
      return;
    }
  
    if (!sParamName) {
      sParamName = oElement.name;
    }
  
    this.addParam(sParamName, oElement.value);
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
  },
  
  redirectOpener: function() {
    window.opener.location.href = this.make();
  },
  
  pop: function(iWidth, iHeight, sWindowName, sBaseUrl, sPrefix) {
    this.addParam("dialog", "1");
  
    var iLeft = 50;
    iWidth = iWidth || 800;
    iHeight = iHeight || 600;
    sWindowName = sWindowName || "";
  
    // Pefixed window collection
    if (sPrefix && this.oPrefixed[sPrefix]) {
      this.oPrefixed[sPrefix] = this.oPrefixed[sPrefix].reject(function(oWindow) {
        return oWindow.closed;
      } );
          
      // Purge closed windows
      iLeft += (iWidth + 8) * this.oPrefixed[sPrefix].length;
    }
    
    var sFeatures = Url.buildPopupFeatures({left: iLeft, height: iHeight, width: iWidth});
  
    // Forbidden characters for IE
    sWindowName = sWindowName.replace(/[ -]/gi, "_");
    var sTargetUrl = sBaseUrl || "";
    this.oWindow = window.open(sTargetUrl + this.make(), sWindowName, sFeatures);  
    window.children[sWindowName] = this.oWindow;
		
    // Prefixed window collection
    if (sPrefix) {
      if (!this.oPrefixed[sPrefix]) {
        this.oPrefixed[sPrefix] = [];
      }
      this.oPrefixed[sPrefix].push(this.oWindow);
    }
  },
  
  popDirect: function(iWidth, iHeight, sWindowName, sBaseUrl) {
    var sFeatures = Url.buildPopupFeatures({height: iHeight, width: iWidth});
  
    iWidth = iWidth || 800;
    iHeight = iHeight || 600;
    sWindowName = sWindowName || "";
		
    // Forbidden characters for IE
    sWindowName = sWindowName.replace(/[ -]/gi, "_");
    
    this.oWindow = window.open(sBaseUrl + this.make(), sWindowName, sFeatures);
    window.children[sWindowName] = this.oWindow;
  },
  
  popunder: function(iWidth, iHeight, sWindowName) {
    this.pop(iWidth, iHeight, sWindowName);
    this.oWindow.blur();
    window.focus();
  },
  
  popup: function(iWidth, iHeight, sWindowName, sPrefix) {
    this.pop(iWidth, iHeight, sWindowName, null, sPrefix);
  
    // Prefixed window collection
    if (sPrefix) {
      (this.oPrefixed[sPrefix] || []).each(function (oWindow) { 
        oWindow.focus();
      } );
    }
    
    this.oWindow.focus();
  },
  
  autoComplete: function(idInput, idPopulate, oOptions) {
    oOptions = Object.extend({
	    minChars: 3,
	    frequency: 0.5,
      dropdown: false,
	    
	    // Allows bigger width than input
			onShow: function(element, update) { 
        if(!update.style.position || update.style.position == 'absolute') {
          update.style.position = 'absolute';
          Position.clone(element, update, {
            setWidth: !parseFloat(update.getStyle('width')), // In order to make the list as wide as the input if the style contains width:0
            setHeight: false, 
            offsetTop: element.offsetHeight
          });
        }
        Effect.Appear(update,{duration:0.25});
      }
    }, oOptions || {});
    
    var input = $(idInput).addClassName("autocomplete");

    // Autocomplete
    this.addParam("ajax", 1);
    this.addParam("suppressHeaders", 1);
    
    var autocompleter = new Ajax.Autocompleter(idInput, idPopulate, this.make(), oOptions);
    
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
      var container = new Element("div", {style: "border:none;margin:0;padding:0;position:relative;", className: "dropdown"});
      var height = input.getHeight()-4;
      var margin = parseInt(input.getStyle("marginTop"))+1;

      input.wrap(
        container.setStyle({paddingRight: (height+5)+'px'}).
                  clonePosition(input, {setLeft: false, setTop: false})
      );
      container.insert($(idPopulate));
      
      var dropdown = new Element("div", {
        style:"padding:0;position:absolute;right:0;top:0;width:"+height+"px;height:"+height+"px;margin:"+margin+"px;cursor:pointer;", 
        className: "dropdown-trigger"
      });
      
      dropdown.observe("click", function(){
        autocompleter.activate.bind(autocompleter)();
      });
      container.insert(dropdown);
    }
    
    return autocompleter;
  },
  
  close: function() {
    if(this.oWindow) {
      this.oWindow.close();
    } 
  },
  
  requestUpdate: function(ioTarget, oOptions) {
    this.addParam("suppressHeaders", "1");
    this.addParam("ajax", "1");
  
    var oDefaultOptions = {
      waitingText: "Chargement",
      urlBase: "",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      getParameters: null,
      onFailure: function(){$(ioTarget).update("<div class='error'>Le serveur rencontre quelques problemes.</div>");}
    };
  
    Object.extend(oDefaultOptions, oOptions);
    
    AjaxResponse.onAfterEval = oDefaultOptions.onAfterEval;
    
    if (oDefaultOptions.waitingText) {
      $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
	    if (ioTarget == SystemMessage.id) {
		    SystemMessage.doEffect();
	    }
    }
    else {
      WaitingMessage.cover(ioTarget);
    }  
  	
    var getParams = oDefaultOptions.getParameters ? "?" + $H(oDefaultOptions.getParameters).toQueryString() : '';
    new Ajax.Updater(ioTarget, oDefaultOptions.urlBase + "index.php" + getParams, oDefaultOptions);
  },
  
  requestJSON: function(fCallback, oOptions) {
    this.addParam("suppressHeaders", "1");
    this.addParam("ajax", "");
  
    var oDefaultOptions = {
      waitingText: null,
      urlBase: "",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      evalJSON: 'force',
      getParameters: null
    };
  
    Object.extend(oDefaultOptions, oOptions);
    oDefaultOptions.onSuccess = function(transport){fCallback(transport.responseJSON)};
  	
    var getParams = oDefaultOptions.getParameters ? "?" + $H(oDefaultOptions.getParameters).toQueryString() : '';
    new Ajax.Request(oDefaultOptions.urlBase + "index.php" + getParams, oDefaultOptions);
  },
  
  requestUpdateOffline: function(ioTarget, oOptions) {
    if (typeof netscape != 'undefined' && typeof netscape.security != 'undefined') {
      netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
    }
    
    this.addParam("_syncroOffline", "1");
    if(config.date_synchro){
      this.addParam("_synchroDatetime" , config.date_synchro);
    }
    
    var oDefaultOptions = {
        urlBase: config.urlMediboard
    };
  
    Object.extend(oDefaultOptions, oOptions);
  		
//    References.clean(ioTarget);
    this.requestUpdate(ioTarget, oDefaultOptions);
  },
  
  periodicalUpdate: function(ioTarget, oOptions) {
    this.addParam("suppressHeaders", "1");
    this.addParam("ajax", "1");
  
    var oDefaultOptions = {
      waitingText: "Chargement",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true
    };
  
    Object.extend(oDefaultOptions, oOptions);
    
    if(oDefaultOptions.waitingText)
      $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
    
    return new Ajax.PeriodicalUpdater(ioTarget, "index.php", oDefaultOptions);
  },
  
  ViewFilePopup: function(objectClass, objectId, elementClass, elementId, sfn){
    this.setModuleAction("dPfiles", "preview_files");
    this.addParam("popup", "1");
    this.addParam("objectClass", objectClass);
    this.addParam("objectId", objectId);
    this.addParam("elementClass", elementClass);
    this.addParam("elementId", elementId);
    if(sfn!=0){
      this.addParam("sfn", sfn);
    }
    this.popup(750, 550, "Fichier");
  }
} );

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
  url.addParam("suppressHeaders", "1");
  url.addParam("ajax", "1");
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

function popChgPwd() {
  var url = new Url("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}