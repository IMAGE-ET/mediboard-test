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

function throwError(sMsg) {
  var oCaller = throwError.caller;
  debug(oCaller, "Error " + sMsg + " in function");

  while (oCaller = oCaller.caller) {
    debug(oCaller, "Backtrace");
  }
}

/**
 * Assert utility object
 */ 
 
var Assert = {
  debug: function (sMsg) {
    var oCaller = this.debug.caller.caller;
    debug(oCaller.getName(), "Error " + sMsg + " in function");

    var aTraces = new Array;
    while (oCaller = oCaller.caller) {
      aTraces.push(oCaller.getName());
    }
    
    debug(aTraces.join("\n"), "Backtraces");
  },

  that: function (bPredicate, sMsg) {
    if (!bPredicate) {
      this.debug(sMsg);
    }
  }
}

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

/**
 * PairEffect Class
 */

var PairEffect = Class.create();

// PairEffect Methods
Class.extend(PairEffect, {

  // Constructor
  initialize: function(idTarget, oOptions) {
  	
    var oDefaultOptions = {
      idTrigger: idTarget + "-trigger",
      sEffect: "slide", // could be null, "appear", "slide", "blind"
      bStartVisible: false, // Make it visible at start
      bStoreInCookie: true,
      sCookieName: "effect"
	};

    oDefaultOptions.extend(oOptions);
    this.oOptions = oDefaultOptions;
    this.oTarget = $(idTarget);
    this.oTrigger = $(this.oOptions.idTrigger);

    Assert.that(this.oTarget, "Target element is undefined for id" + idTarget);
    Assert.that(this.oTrigger, "Trigger element is undefined " + this.oOptions.idTrigger);
	
    // Initialize the effect
    Event.observe(this.oTrigger, "click", this.flip.bind(this));
  
    // Initialize classnames and adapt visibility
    var aCNs = Element.classNames(this.oTrigger);
    aCNs.add(this.oOptions.bStartVisible ? "triggerHide" : "triggerShow");
    if (this.oOptions.bStoreInCookie) {
      aCNs.load(this.oOptions.sCookieName);
    }
    Element[aCNs.include("triggerShow") ? "hide" : "show"](this.oTarget);   
  },
  
  // Flipper callback
  flip: function() {
    if (this.oOptions.sEffect && BrowserDetect.browser != "Explorer") {
      new Effect.toggle(this.oTarget, this.oOptions.sEffect);
    } else {
      Element.toggle(this.oTarget);
    }
  
    var aCNs = Element.classNames(this.oTrigger);
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

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible : null, // Forces one element to start visible
      sCookieName: sTargetsClass,
    }
    
    oDefaultOptions.extend(oOptions);
    
    document.getElementsByClassName(sTargetsClass).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oElement.id == oDefaultOptions.idStartVisible;
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});

/**
 * Date utility functions
 * @todo: extend Date class
 */

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