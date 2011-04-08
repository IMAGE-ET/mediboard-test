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
    sType = sType || "action";

    this.oParams = {};
    this.oWindow = null;
    this.sFragment = null;
    this.oPrefixed = {};
    
    if(sModule && sAction) {
      if(sType === "action") {
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
  
  addParam: function(sName, sValue, bAcceptArray) {
    if (bAcceptArray && Object.isArray(sValue)) {
      $A(sValue).each(function(elt, i) {
        this.oParams[sName.replace(/\[([^\[]*)\]$/, "["+i+"]")] = elt;
      }, this);
      return this;
    }
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
    if (window.opener && !window.opener.closed) {
      window.opener.location.assign(this.make());
    } 
    else {
      this.redirect();
    }
  },
  
  getPopupFeatures: function(){
    return Object.clone(Url.popupFeatures);
  },
  
  pop: function(iWidth, iHeight, sWindowName, sBaseUrl, sPrefix, oPostParameters, iFrame) {
    this.addParam("dialog", 1);
  
    var features = this.getPopupFeatures();
    
    features = Object.extend(features, {
      width: iWidth,
      height: iHeight
    });

    if (features.height == "100%" || features.width == "100%") {
      if (features.width == "100%") {
        //features.fullscreen = true; // REALLY invasive under IE
        //features.type = "fullWindow";
        features.width = screen.availWidth || screen.width;
        features.left = 0;
      }
      
      if (features.height == "100%") {
        features.height = screen.availHeight || screen.height;
        features.top = 0;
      }
    }
    
    sWindowName = sWindowName || "";
    sBaseUrl = sBaseUrl || "";
    
    // the Iframe argument is used when exporting data (export_csv_array for ex.)
    if (!iFrame) {
      var sFeatures = Url.buildPopupFeatures(features);

      // Prefixed window collection
      if (sPrefix && this.oPrefixed[sPrefix]) {
        this.oPrefixed[sPrefix] = this.oPrefixed[sPrefix].reject(function(oWindow) {
          return oWindow.closed;
        });
      }
    
      // Forbidden characters for IE
      if (Prototype.Browser.IE) {
        sWindowName = sWindowName.replace(/[- '"]/gi, "_");
      }

      this.oWindow = window.open(oPostParameters ? "" : (sBaseUrl + this.make()), sWindowName, sFeatures);  
      window.children[sWindowName] = this.oWindow;

      this.oWindow.moveTo(features.left, features.top);
      
      if (!this.oWindow)
        return this.showPopupBlockerAlert(sWindowName);
    }
    
    if (oPostParameters) {
      var form = DOM.form({
        method: "post", 
        action: sBaseUrl + this.make(), 
        target: (iFrame ? iFrame.getAttribute("name") : sWindowName)
      });
      
      $(document.documentElement).insert(form);
      
      Form.fromObject(form, oPostParameters, true);
      form.submit();
      form.remove();
    }
    
    // Prefixed window collection
    if (sPrefix) {
      if (!this.oPrefixed[sPrefix]) {
        this.oPrefixed[sPrefix] = [];
      }
      this.oPrefixed[sPrefix].push(this.oWindow);
    }
    
    return this;
  },
  
  modale: function(options) {
    this.addParam("dialog", 1);
    
    var closeButton = DOM.button({type: "button", className: "cancel notext"});

    options = Object.extend({
      className: 'modal popup',
      width: 900,
      height: 600,
      iframe: true,
      title: "",
      closeOnClick: closeButton,
      closeOnEscape: true
    }, options);
    
    var viewport = document.viewport.getDimensions();
    options.height = Math.min(viewport.height-50, options.height);
    options.width = Math.min(viewport.width-50, options.width);
  
    // Hack
    this.modaleObject = Control.Modal.open(new Element("a", {href: this.make()}), options);
    
    var titleElement = DOM.div({className: "title"}, options.title || "&nbsp;");
    
    this.modaleObject.container.insert({top: titleElement});   
    
    if (options.closeOnClick) {
      this.modaleObject.container.insert({top: closeButton})
    }
    
    // iframe.onload not thrown under IE
    if (Prototype.Browser.IE) {
      var that = this.modaleObject;
      var iframe = that.container.down("iframe");

      iframe.onload = null;
      iframe.onreadystatechange = function(){
        if (iframe.readyState !== "complete") return;

        that.notify('onRemoteContentLoaded');
        if (that.options.indicator) 
          that.hideIndicator();

        iframe.onreadystatechange = null;
      }
    }
    
    this.modaleObject.observe("onRemoteContentLoaded", function(){
      var iframeWindow = this.container.down("iframe").contentWindow;
      
      if (!options.title) {
        titleElement.update(iframeWindow.document.title);
      }
      
      if (!options.closeOnEscape) {
        iframeWindow.document.stopObserving('keydown', iframeWindow.closeWindowByEscape);
      }
    }.bind(this.modaleObject));
  
    return this;
  },
  
  popDirect: function(iWidth, iHeight, sWindowName, sBaseUrl) {
    iWidth = iWidth || 800;
    iHeight = iHeight || 600;
    sWindowName = sWindowName || "";
    sBaseUrl = sBaseUrl || "";
    
    var sFeatures = Url.buildPopupFeatures({height: iHeight, width: iWidth});
    
    // Forbidden characters for IE
    if (Prototype.Browser.IE) {
      sWindowName = sWindowName.replace(/[- '"]/gi, "_");
    }
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
  
  popup: function(iWidth, iHeight, sWindowName, sPrefix, oPostParameters) {
    this.pop(iWidth, iHeight, sWindowName, null, sPrefix, oPostParameters);
  
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
    var saveInput = input;
    input = $(input);
    
    if (!input) {
      try {
        console.warn((saveInput || "$(input)") + " doesn't exist [Url.autoComplete]");
      } catch (e) {}
    
      return;
    }
    
    oOptions = Object.extend({
      minChars: 2,
      frequency: 0.5,
      width: null,
      dropdown: false,
      valueElement: null,
      
      // Allows bigger width than input
      onShow: function(element, update) {
        update.style.position = "absolute";
        
        var elementDimensions = element.getDimensions();
        
        update.show().clonePosition(element, {
          setWidth: true,
          setHeight: false, 
          setTop: true,
          setLeft: true,
          offsetTop: elementDimensions.height+1
        });

        // Default width behaviour
        var style = {
          width: "auto",
          whiteSpace: "nowrap",
          minWidth: elementDimensions.width+"px",
          maxWidth: "400px"
        };

        // Fixed width behaviour
        if (oOptions.width) {
          style = {
            width: oOptions.width
          };
        }
        
        var scroll = document.viewport.getScrollOffsets(); // Viewport offset
        var viewport = document.viewport.getDimensions(); // Viewport size
        var scrollOffset = update.cumulativeOffset();
        var updateHeight = update.getHeight();
        
        if (scrollOffset.top + updateHeight > viewport.height+scroll.top) {
          style.top = (parseInt(update.style.top)-elementDimensions.height-updateHeight+2) + "px";
        }
        
        update.setStyle(style)
              .setOpacity(1)
              .unoverflow();
        
        if (oOptions.onAfterShow) {
          oOptions.onAfterShow(element, update);
        }
      },
      
      onHide: function(element, update){ 
        update.scrollTop = 0;
        Element.hide(update); 
      }
    }, oOptions);
    
    input.addClassName("autocomplete");
    
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
    
    // Pour "eval" les scripts inserés (utile pour lancer le onDisconnected
    autocompleter.options.onComplete = function(request) {
      var content = request.responseText;
      content.evalScripts.bind(content).defer();
      this.updateChoices(content);
    }.bind(autocompleter);
    
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
      var container = new Element("div").addClassName("dropdown");
      
      input.wrap(container);
      container.insert(populate);
      
      // The trigger button
      var trigger = new Element("div").addClassName("dropdown-trigger");
      trigger.insert(new Element("div"));
      
      // Hide the list
      var hideAutocomplete = function(e){
        autocompleter.onBlur(e);
        //$$("div.autocomplete").invoke("hide");
      }.bindAsEventListener(this);
      
      // Show the list
      var showAutocomplete = function(e, dontClear){
        var oldValue;
        
        if (!dontClear) {
          oldValue = $V(input);
          $V(input, '', false);
        }
        
        autocompleter.activate.bind(autocompleter)();
        Event.stop(e);
        document.observeOnce("mousedown", hideAutocomplete);
        
        if (!dontClear) {
          $V(input, oldValue, false);
        }

        input.select();
      };
      
      // Bind the events
      trigger.observe("mousedown", showAutocomplete.bindAsEventListener(this));
      //input.observe("click", showAutocomplete.bindAsEventListener(this, true));
      input.observe("click", function(){
        var valueElement = oOptions.valueElement;
        
        if (valueElement && valueElement.value == "") {
          input.value = "";
        }
        else if (valueElement && valueElement.hasClassName("ref")) {
          try {
            input.select();
          } catch(e) {}
        }
          
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
  
  requestModal: function(iWidth, iHeight, oOptions) {
    var div = DOM.div(null,
      DOM.div({
        className: 'content'
      }).setStyle({
        overflowY: 'auto',
        overflowX: 'hidden',
        height: iHeight ? iHeight+'px' : '',
        width: iWidth ? iWidth+'px' : ''
      })
    );
    $(document.body).insert(div);

    // Decoration preparing
    var closeButton  = DOM.button({type: "button", className: "cancel notext"}, $T('Close'));
    var reloadButton = DOM.button({type: "button", className: "change notext"}, $T('Reload'));
    
    var m = this.oParams['m'];
    var a = this.oParams['a'];
    var titleElement = DOM.div({className: "title"}, $T('mod-'+m+'-tab-'+a));
    
    this.modaleObject = modal(div, {
      className: 'modal popup',
      closeOnClick: closeButton
    });
    
    this.modaleObject.observe("afterClose", function(){div.remove()});
    
    this.modaleObject.container.insert({top: reloadButton});
    this.modaleObject.container.insert({top: closeButton});
    this.modaleObject.container.insert({top: titleElement});   

    // Repositionnement de la modale quand la hauteur n'est pas spécifiée
    oOptions = Object.extend({
      onComplete: this.modaleObject.position.bind(this.modaleObject)
    }, oOptions);
    this.requestUpdate(div.down('.content'), oOptions);
  
    reloadButton.onclick = function() {
      this.requestUpdate(div.down('.content'), oOptions);
    }.bind(this);
    
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
    
    var customInsertion = oOptions && oOptions.insertion;
    
    oOptions = Object.extend( {
      waitingText: null,
      urlBase: "",
      method: "get",
      parameters:  $H(this.oParams).toQueryString(), 
      asynchronous: true,
      evalScripts: true,
      getParameters: null,
      coverIE: true,
      onComplete: Prototype.emptyFunction,
      onFailure: function(){ element.update('<div class="error">Le serveur rencontre quelques problèmes.</div>');}
    }, oOptions);
    
    oOptions.onComplete = oOptions.onComplete.wrap(function(onComplete) {
      prepareForms(element);
      initNotes();
      onComplete();
      Element.warnDuplicates();
    });
    
    // If we have a custom insertion, we should not touch the origin target
    if (!customInsertion) {
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
        if (!Prototype.Browser.IE || oOptions.coverIE) 
          WaitingMessage.cover(element);
      }
      
      if (oOptions.waitingText) {
        element.update('<div class="loading">' + oOptions.waitingText + '...</div>');
      }
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
    
    var updater = new Ajax.PeriodicalUpdater(element, "index.php", oOptions);
    
    updater.options.onComplete = updater.options.onComplete.wrap(function(onComplete) {
      prepareForms(element);
      initNotes();
      onComplete();
      Element.warnDuplicates();
    });
    
    return updater;
  },
  
  ViewFilePopup: function(objectClass, objectId, elementClass, elementId, sfn){
    var popupName = "Fichier";
    popupName += "-"+elementClass+"-"+elementId;
    
    /*
    var event = Function.getEvent();
    if (event) {
      Event.stop(event);
      if (event.shiftKey)
        popupName += "-"+objectClass+"-"+objectId;
    }*/
    
    this.setModuleAction("dPfiles", "preview_files");
    this.addParam("popup", 1);
    this.addParam("objectClass", objectClass);
    this.addParam("objectId", objectId);
    this.addParam("elementClass", elementClass);
    this.addParam("elementId", elementId);
    if(sfn != 0){
      this.addParam("sfn", sfn);
    }
    this.popup(785, 600, popupName);
  }
} );

Url.activeRequests = {
  post: 0,
  get: 0
};

Url.popupFeatures = {
  left: 50,
  top: 50,
  height: 600,
  width: 800,
  scrollbars: true,
  resizable: true,
  menubar: true
 };

Url.buildPopupFeatures = function(features) {
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

Url.update = function(form, element) {
  new Url().addFormData(form).requestUpdate(element);
  return false;
};

Url.hashParams = function() {
  return window.location.hash.substr(1).toQueryParams();
};
