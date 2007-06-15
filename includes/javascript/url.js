// Class URL, for easy url parameters writing and poping
function Url() {
  this.oParams = {};
  this.oWindow = null;
  this.oPrefixed = {};
}

Url.prototype.setModuleAction = function(sModule, sAction) {
  this.addParam("m", sModule);
  this.addParam("a", sAction);
}

Url.prototype.setModuleTab = function(sModule, sTab) {
  this.addParam("m", sModule);
  this.addParam("tab", sTab);
}

Url.prototype.addParam = function(sName, sValue) {
  this.oParams[sName] = sValue;
}

Url.prototype.addElement = function(oElement, sParamName) {
  if (!oElement) {
  	return;
  }

  if (!sParamName) {
    sParamName = oElement.name;
  }

  this.addParam(sParamName, oElement.value);
}

Url.prototype.make = function() {
  var sUrl = "?" + $H(this.oParams).toQueryString();
  return sUrl;
}

Url.prototype.redirect = function() {
  if(this.oWindow)
    this.oWindow.location.href = this.make();
  else
    window.location.href = this.make();
}

Url.prototype.pop = function(iWidth, iHeight, sWindowName, sBaseUrl, sPrefix) {
  this.addParam("dialog", "1");

  var iLeft = 50;


  // Pefixed window collection
  if (sPrefix && this.oPrefixed[sPrefix]) {
  	this.oPrefixed[sPrefix] = this.oPrefixed[sPrefix].reject(function(oWindow) {
  		return oWindow.closed;
  	} );
  	  	
  	// Purge closed windows
  	iLeft += (iWidth + 8) * this.oPrefixed[sPrefix].length;
  }
  

  var aFeatures = new Array;
  aFeatures.push("left=" + iLeft);
  aFeatures.push("top=50");
  aFeatures.push("height=" + iHeight);
  aFeatures.push("width=" + iWidth);
  aFeatures.push("scrollbars=yes");
  aFeatures.push("resizable=yes");
  aFeatures.push("menubar=yes");

  // Forbidden characters for IE
  sWindowName = sWindowName.replace(/[ -]/gi, "_");
  var sTargetUrl = sBaseUrl || "";
  this.oWindow = window.open(sTargetUrl + this.make(), sWindowName, aFeatures.join(", "));  
	
  // Prefixed window collection
  if (sPrefix) {
    if (!this.oPrefixed[sPrefix]) {
    	this.oPrefixed[sPrefix] = [];
    }
    this.oPrefixed[sPrefix].push(this.oWindow);
  }
}

Url.prototype.popDirect = function(iWidth, iHeight, sWindowName, sBaseUrl) {
  var aFeatures = new Array;
  aFeatures.push("left=50");
  aFeatures.push("top=50");
  aFeatures.push("height=" + iHeight);
  aFeatures.push("width=" + iWidth);
  aFeatures.push("scrollbars=yes");
  aFeatures.push("resizable=yes");
  aFeatures.push("menubar=yes");

  // Forbidden characters for IE
  sWindowName = sWindowName.replace(/[ -]/gi, "_");
  
  this.oWindow = window.open(sBaseUrl + this.make(), sWindowName, aFeatures.join(", ")); 
}

Url.prototype.popunder = function(iWidth, iHeight, sWindowName) {
  this.pop(iWidth, iHeight, sWindowName);
  this.oWindow.blur();
  window.focus();
}

Url.prototype.popup = function(iWidth, iHeight, sWindowName, sPrefix) {
  this.pop(iWidth, iHeight, sWindowName, null, sPrefix);

  // Prefixed window collection
  if (sPrefix) {
    (this.oPrefixed[sPrefix] || []).each(function (oWindow) { 
    	oWindow.focus();
    } );
  }
  
  this.oWindow.focus();
}

Url.prototype.close = function() {
  if(this.oWindow) {
    this.oWindow.close();
  } 
}

Url.prototype.requestUpdate = function(ioTarget, oOptions) {
  this.addParam("suppressHeaders", "1");
  this.addParam("ajax", "1");

  var oDefaultOptions = {
    waitingText: "Chargement",
    urlBase: "",
    method: "get",
    parameters:  $H(this.oParams).toQueryString(), 
    asynchronous: true,
    evalScripts: true,
    onFailure: function(){$(ioTarget).innerHTML = "<div class='error'>Le serveur rencontre quelques problemes.</div>";}
//    onException: function(){$(ioTarget).innerHTML = "<div class='error'>Le serveur est injoignable.</div>";}
  };

  Object.extend(oDefaultOptions, oOptions);
  
  AjaxResponse.onAfterEval = oDefaultOptions.onAfterEval;
  
  if (oDefaultOptions.waitingText)
    $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
    
  new Ajax.Updater(ioTarget, oDefaultOptions["urlBase"] + "index.php", oDefaultOptions);  
}

Url.prototype.requestUpdateOffline = function(ioTarget, oOptions) {
  if (typeof netscape != 'undefined' && typeof netscape.security != 'undefined') {
    netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
  }
  
  this.addParam("_syncroOffline"   , "1");
  if(config["date_synchro"]){
    this.addParam("_synchroDatetime" , config["date_synchro"]);
  }
  
  var oDefaultOptions = {
      urlBase: config["urlMediboard"]
  };

  Object.extend(oDefaultOptions, oOptions);
  
  this.requestUpdate(ioTarget, oDefaultOptions);
}

Url.prototype.periodicalUpdate = function(ioTarget, oOptions) {
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
  
  new Ajax.PeriodicalUpdater(ioTarget, "index.php", oDefaultOptions);
}

Url.prototype.ViewFilePopup = function(objectClass, objectId, elementClass, elementId, sfn){
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