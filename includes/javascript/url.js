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
  return "index.php?" + this.aParams.join("&");
}

Url.prototype.redirect = function() {
  if(this.oWindow)
    this.oWindow.location.href = this.make();
  else
    window.location.href = this.make();
}

Url.prototype.pop = function(iWidth, iHeight, sWindowName) {
  this.addParam("dialog", "1");
  params = 'left=50, top=50, height=' + iHeight + ', width=' + iWidth;
  params += ', resizable=yes, scrollbars=yes, menubar=yes';
  this.oWindow = window.open(this.make(), name, params);  
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
    evalScripts: true,
  };

  Object.extend(oDefaultOptions, oOptions);
  
  if(oDefaultOptions.waitingText)
    $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
    
  new Ajax.Updater(ioTarget, "index.php", oDefaultOptions);  
}

Url.prototype.periodicalUpdate = function(ioTarget, oOptions) {
  this.addParam("suppressHeaders", "1");
  this.addParam("ajax", "1");

  var oDefaultOptions = {
    waitingText: "Chargement",
    method: "get",
    parameters:  this.aParams.join("&"), 
    asynchronous: true,
    evalScripts: true,
  };

  Object.extend(oDefaultOptions, oOptions);
  
  if(oDefaultOptions.waitingText)
    $(ioTarget).innerHTML = "<div class='loading'>" + oDefaultOptions.waitingText + "...<br>Merci de patienter.</div>";
  
  new Ajax.PeriodicalUpdater(ioTarget, "index.php", oDefaultOptions);
}