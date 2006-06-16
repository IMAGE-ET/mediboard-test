// $Id$

function main() {
  prepareForms();
  initHTMLArea();
  initFCKEditor();
  pageMain();
}

function pageMain() {}

function initHTMLArea () {}
function initFCKEditor() {}

function getElementsByClassName(tagName, className, exactMatch) {
  var els = document.getElementsByTagName(tagName); 
  var elsTag = new Array;

  var elIt = 0;
  while (el = els[elIt++]) {
    // el.getAttribute("class") DOES NOT work in IE
    if (exactMatch ? el.className == className : el.className.indexOf(className) != -1) {
      elsTag.push(el);
    }
  }
  
  return elsTag;
}

function flipElementClass(elementId, firstClass, secondClass, cookieName) {
  var element = document.getElementById(elementId);
  
  if (!element) {
    return;
  }

  if (element.className != firstClass && element.className != secondClass) {
    throwError("The element class of '" + elementId + "' is neither '" + firstClass + "' nor '" + secondClass + "'.");
  }
  
  element.className = element.className == firstClass ? secondClass : firstClass;
  
  if (cookieName) {
    var cookie = new CJL_CookieUtil(cookieName);
    cookie.setSubValue(elementId, element.className);
  }
}

function initElementClass(elementId, cookieName) {
  var cookie = new CJL_CookieUtil(cookieName);
  value = cookie.getSubValue(elementId);
  if (value) {
	var oElement = document.getElementById(elementId);
	if (!oElement) {
		throwError(printf("Element with id '%s' doesn't exist", elementId));
	}
    oElement.className = value;
    
  }
}

function flipEffectElement(idTarget, sShowEffect, sHideEffect, idTrigger) {
  var oTargetElement = document.getElementById(idTarget);
  var oTriggerElement = document.getElementById(idTrigger);
  var sEffect = "";
  switch (oTriggerElement.className) {
  	case "triggerShow" : sEffect = sShowEffect; break;
  	case "triggerHide" : sEffect = sHideEffect; break;
  	default: throwError(printf("Trigger element class name should be either 'triggerShow' or 'triggerHide', instead of '%s'", oTriggerElement.className));
  }
  
  eval('new Effect.' + sEffect + '(oTargetElement)');
  flipElementClass(idTrigger, "triggerShow", "triggerHide", idTrigger);
}

function initEffectClass(idTarget, idTrigger) {
  initElementClass(idTrigger, idTrigger);
  
  var oTriggerElement = document.getElementById(idTrigger);
  var oTargetElement = document.getElementById(idTarget);
  oTargetElement.style.display = (oTriggerElement.className == "triggerShow") ? "none" : "";
}

function initGroups(groupname) {
  var trs = getElementsByClassName("tr", groupname, false);
  var trsit = 0;
  while(tr = trs[trsit++]) {
    tr.style.display = "none";
  }
  var cookie = new CJL_CookieUtil(groupname);
  groupvalues = cookie.getAllSubValues();
  for (groupid in groupvalues) {
    groupclass = groupvalues[groupid];
    if(groupclass == "groupexpand")
      flipGroup(groupid, "");
  }
}

function flipGroup(id, groupname) {
  flipElementClass(groupname + id, "groupcollapse", "groupexpand", groupname);
  var trs = getElementsByClassName("tr", groupname + id, true);
  var trsit = 0;
  while(tr = trs[trsit++]) {
    tr.style.display = tr.style.display == "table-row" ? "none" : "table-row";
  }
}

function confirmDeletion(oForm, oOptions, oOptionsAjax) {
  oDefaultOptions = {
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : 0,
    target  : ""
  }
  
  Object.extend(oDefaultOptions, oOptions);
  
  if (oDefaultOptions.objName.length) oDefaultOptions.objName = " '" + oDefaultOptions.objName + "'";
  if (confirm(oDefaultOptions.msg + oDefaultOptions.typeName + " " + oDefaultOptions.objName + " ?" )) {
  	oForm.del.value = 1;
  	if(oDefaultOptions.ajax)
  	  submitFormAjax(oForm, oDefaultOptions.target, oOptionsAjax);
  	else
  	  oForm.submit();
  }
}

function getFunctionName(oFunction) {
  var sFunction = oFunction.toString();
  var re = /function ([^{]*)/;
  var sFuncProt = sFunction.match(re)[0];
  return sFuncProt;
}

function throwError(sMsg) {
  var oCaller = throwError.caller;
  debug(getFunctionName(oCaller), printf("Error: %s", sMsg));
 
  while (oCaller = oCaller.caller) {
    debug(getFunctionName(oCaller), "backtrace");
  }
 
}

function makeDateFromDATE(sDate) {
  // sDate must be: YYYY-MM-DD
  var aParts = sDate.split("-");
  if (aParts.length != 3) throwError("'" + sDate + "' :Bad DATE format");

  var year  = parseInt(aParts[0], 10);
  var month = parseInt(aParts[1], 10);
  var day   = parseInt(aParts[2], 10);
  
  return new Date(year, month - 1, day); // Js months are 0-11!!
}

function makeDateFromDATETIME(sDateTime) {
  // sDateTime must be: YYYY-MM-DD HH:MM:SS
  var aHalves = sDateTime.split(" ");
  if (aHalves.length != 2) throwError("'" + sDateTime + "' :Bad DATETIME format");

  var sDate = aHalves[0];
  var date = makeDateFromDATE(sDate);

  var sTime = aHalves[1];
  var aParts = sTime.split(":");
  if (aParts.length != 3) throwError("'" + sTime + "' :Bad TIME format");

  date.setHours  (parseInt(aParts[0], 10));
  date.setMinutes(parseInt(aParts[1], 10));
  date.setSeconds(parseInt(aParts[2], 10));
  
  return date;
}

function makeDateFromLocaleDate(sDate) {
//  debug(sDate, "sDate");
  // sDate must be: dd/mm/yyyy
  var aParts = sDate.split("/");
  if (aParts.length != 3) throwError(printf("Bad Display date format : '%s'", sDate));

  var year  = parseInt(aParts[2], 10);
  var month = parseInt(aParts[1], 10);
  var day   = parseInt(aParts[0], 10);
  
  return new Date(year, month - 1, day); // Js months are 0-11!!
}

function makeDATEFromDate(date) {
  var y = date.getFullYear();
  var m = date.getMonth()+1; // Js months are 0-11!!
  var d = date.getDate();
  
  return printf("%04d-%02d-%02d", y, m, d);
}

function makeLocaleDateFromDate(date) {
  var y = date.getFullYear();
  var m = date.getMonth()+1; // Js months are 0-11!!
  var d = date.getDate();
  
  return printf("%02d/%02d/%04d", d, m, y);
}

function makeDATETIMEFromDate(date, useSpace) {
  var h = date.getHours();
  var m = date.getMinutes();
  var s = date.getSeconds();
  
  if(useSpace)
    return makeDATEFromDate(date) + printf(" %02d:%02d:%02d", h, m, s);
  else
    return makeDATEFromDate(date) + printf("+%02d:%02d:%02d", h, m, s);
}

function regFieldCalendar(sFormName, sFieldName, bTime) {
  if (bTime == null) bTime = false;
  
  var sInputId = sFormName + "_" + sFieldName;
  
  if (!document.getElementById(sInputId)) {
    return;
  }

  Calendar.setup( {
      inputField  : sInputId,
      displayArea : sInputId + "_da",
      ifFormat    : "%Y-%m-%d" + (bTime ? " %H:%M:%S" : ""),
      daFormat    : "%d/%m/%Y" + (bTime ? " %H:%M:%S" : ""),
      button      : sInputId + "_trigger",
      showsTime   : bTime
    } 
  );
}

function regRedirectPopupCal(sInitDate, sRedirectBase, sContainerId, bTime) {
  if (sContainerId == null) sContainerId = "changeDate";
  if (bTime == null) bTime = false;
	
  Calendar.setup( {
      button      : sContainerId,
      date        : makeDateFromDATE(sInitDate),
      showsTime   : bTime,
      onUpdate    : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? makeDATETIMEFromDate(calendar.date) : makeDATEFromDate(calendar.date)
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}

function regRedirectFlatCal(sInitDate, sRedirectBase, sContainerId, bTime) {
  if (sContainerId == null) sContainerId = "calendar-container";
  if (bTime == null) bTime = false;

  dInit = bTime ? makeDateFromDATETIME(sInitDate) : makeDateFromDATE(sInitDate);
  
  Calendar.setup( {
      date         : dInit,
      showsTime    : bTime,
      flat         : sContainerId,
      flatCallback : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? makeDATETIMEFromDate(calendar.date) : makeDATEFromDate(calendar.date)
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}

function view_log(classe, id) {
  url = new Url();
  url.setModuleAction("system", "view_history");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.addParam("user_id", "");
  url.addParam("type", "");
  url.popup(600, 500, "history");
}