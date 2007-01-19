// Class URL, for easy url parameters writing and poping
function Url() {
  this.aParams = new Array;
  this.oWindow = null;
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
  this.aParams.push(sName + "=" + sValue);
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
  return "?" + this.aParams.join("&");
}

Url.prototype.redirect = function() {
  if(this.oWindow)
    this.oWindow.location.href = this.make();
  else
    window.location.href = this.make();
}

Url.prototype.pop = function(iWidth, iHeight, sWindowName) {
  this.addParam("dialog", "1");
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
  
  this.oWindow = window.open(this.make(), sWindowName, aFeatures.join(", "));  
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

Url.prototype.popup = function(iWidth, iHeight, sWindowName) {
  this.pop(iWidth, iHeight, sWindowName);
  this.oWindow.focus();
}

Url.prototype.close = function() {
  this.oWindow.close();
}

Url.prototype.requestUpdate = function(ioTarget, oOptions) {
  this.addParam("suppressHeaders", "1");
  this.addParam("ajax", "1");

  var oDefaultOptions = {
    waitingText: "Chargement",
    method: "get",
    parameters:  this.aParams.join("&"), 
    asynchronous: true,
    evalScripts: true
  };

  Object.extend(oDefaultOptions, oOptions);
  
  AjaxResponse.onAfterEval = oDefaultOptions.onAfterEval;
  
  if (oDefaultOptions.waitingText)
    $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
    
  new Ajax.Updater(ioTarget, "index.php", oDefaultOptions);  
}

Url.prototype.requestUpdateOffline = function(ioTarget, oOptions) {
  if (typeof netscape != 'undefined' && typeof netscape.security != 'undefined') {
    netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
  }
  this.addParam("suppressHeaders"  , "1");
  this.addParam("ajax"             , "1");
  this.addParam("_syncroOffline"   , "1");
  this.addParam("_synchroDatetime" , config["date_synchro"]);
  
  var oDefaultOptions = {
    waitingText: "Chargement",
    method: "get",
    parameters:  this.aParams.join("&"), 
    asynchronous: true,
    evalScripts: true
  };

  Object.extend(oDefaultOptions, oOptions);
  
  if(oDefaultOptions.waitingText)
    $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";

  new Ajax.Updater(ioTarget, config["urlMediboard"], oDefaultOptions);
}

Url.prototype.periodicalUpdate = function(ioTarget, oOptions) {
  this.addParam("suppressHeaders", "1");
  this.addParam("ajax", "1");

  var oDefaultOptions = {
    waitingText: "Chargement",
    method: "get",
    parameters:  this.aParams.join("&"), 
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