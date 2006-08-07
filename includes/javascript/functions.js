// $Id$

function main() {
  prepareForms();
  initFCKEditor();
  BrowserDetect.init();
  pageMain();
}

function pageMain() {
}

function initFCKEditor() {}

/**
 * Element.ClassNames class
 */

Class.extend(Element.ClassNames, {
  load: function (sCookieName) {
    var oCookie = new CJL_CookieUtil(sCookieName);
    if (sValue = oCookie.getSubValue(this.element.id)) {
      this.set(sValue);
    }
  },
  
  save: function (sCookieName) {
    var oCookie = new CJL_CookieUtil(sCookieName);
    oCookie.setSubValue(this.element.id, this.toString());
  }
});

function initElementClass(idElement, sCookieName) {
  var oCookie = new CJL_CookieUtil(sCookieName);
  sValue = cookie.oCookie(idElement);
  if (sValue) {
	var oElement = $(idElement);
	if (!oElement) {
		throwError(printf("Element with id '%s' doesn't exist", idElements));
	}
    oElement.className = sValue;
  }
}

function flipEffectElementPlus(idTarget, idTrigger, oOptions) {
  if (oOptions.sEffect && BrowserDetect.browser != "Explorer") {
    new Effect.toggle(idTarget, oOptions.sEffect);
  } else {
    Element.toggle(idTarget);
  }
  
  var aCNs = Element.classNames(idTrigger);
  aCNs.flip("triggerShow", "triggerHide");
  if (oOptions.bStore) {
    aCNs.save(oOptions.sCookieName);
  }
}

function initEffectGroupPlus(classTarget, oOptions) {
  oOptions.sCookieName = classTarget;
  document.getElementsByClassName(classTarget).each( 
    function(oElement) {
      initEffectClassPlus(oElement.id, null, oOptions);
    }
  );
}

function initEffectClassPlus(idTarget, idTrigger, oOptions) {
  if (!idTrigger) {
    idTrigger = idTarget + "-trigger";
  }
  
  var oDefaultOptions = {
    sEffect: null, // could be "appear", "slide", "blind"
    bStartVisible: false,
    bStore: true,
    sCookieName: "effect"
  };
  
  oDefaultOptions.extend(oOptions);

  var oTarget = $(idTarget);
  var oTrigger = $(idTrigger);
  
  // Initialize the effect
  Event.observe(oTrigger, "click",
    function () { 
      flipEffectElementPlus(idTarget, idTrigger, oDefaultOptions);
    }
  );
  
  // Initialize classnames and adapt visibility
  var aCNs = Element.classNames(oTrigger);
  aCNs.add(oDefaultOptions.bStartVisible ? "triggerHide" : "triggerShow");
  if (oDefaultOptions.bStore) {
    aCNs.load(oDefaultOptions.sCookieName);
  }
  Element[aCNs.include("triggerShow") ? "hide" : "show"](oTarget);   
}

function throwError(sMsg) {
  var oCaller = throwError.caller;
  debug(oCaller, "Error " + sMsg + " in function");

  while (oCaller = oCaller.caller) {
    debug(oCaller, "Backtrace");
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
      daFormat    : "%d/%m/%Y" + (bTime ? " %H:%M" : ""),
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