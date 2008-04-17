// $Id$

function addHelp(sClass, oField, sName, sDepend) {
  url = new Url;
  if(!sDepend) {
    sDepend = null;
  }
  url.setModuleAction("dPcompteRendu", "edit_aide");
  url.addParam("class"       , sClass);
  url.addParam("field"       , sName || oField.name);
  url.addParam("text"        , oField.value);
  url.addParam("depend_value", sDepend);
  url.popup(600, 300, "AidesSaisie");
}

function confirmDeletion(oForm, oOptions, oOptionsAjax) {
  oDefaultOptions = {
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : 0,
    target  : "systemMsg"
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

function confirmDeletionOffline(oForm, oFct, oOptions, oOptionsAjax) {
  oDefaultOptions = {
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : 0,
    target  : "systemMsg"
  }
  
  Object.extend(oDefaultOptions, oOptions);
  
  if (oDefaultOptions.objName.length) oDefaultOptions.objName = " '" + oDefaultOptions.objName + "'";
  if (confirm(oDefaultOptions.msg + oDefaultOptions.typeName + " " + oDefaultOptions.objName + " ?" )) {
    oForm.del.value = 1;
    oFct();
  }
}

function getLabelFor(oElement) {
  var aLabels = oElement.form.getElementsByTagName("label");
  var iLabel = 0;
  while (oLabel = aLabels[iLabel++]) {
    if (oElement.id == oLabel.htmlFor) {
      return oLabel;
    }  
  } 
  
  return null; 
}

function getCheckedValue(radioObj) {
  if (!radioObj)
    return "";
  var radioLength = radioObj.length;
  if(radioLength == undefined)
    if(radioObj.checked)
      return radioObj.value;
    else
      return "";
  for(var i = 0; i < radioLength; i++) {
    if(radioObj[i].checked) {
      return radioObj[i].value;
    }
  }
  return "";
}

function setCheckedValue(oRadio, sValue) {
  if (!oRadio) {
    return;
  }
  
  for (var i = 0; i < oRadio.length; i++) {
    if (oRadio[i].value == sValue) {
      oRadio[i].checked = true;
    }
  }
}


function setRadioValue(oElement, sValue) {
  for(var i = 0;i < oElement.length; i++) {
    if(oElement[i].value == sValue)
      oElement[i].checked = true;
  }
}

function pasteHelperContent(oHelpElement) {
  var aFound = oHelpElement.name.match(/_helpers_(.*)/);
  Assert.that(aFound.length == 2, "Helper element '%s' is not of the form '_helpers_propname'", oHelpElement.name);
  
  var oForm       = oHelpElement.form; 
  var aFieldFound = aFound[1].split("-");
  
  var sPropName = aFieldFound[0];
  var oAreaField = oForm.elements[sPropName];

  var sValue = oHelpElement.value;
  oHelpElement.value = "";
  insertAt(oAreaField, sValue + '\n')
  oAreaField.scrollTop = oAreaField.scrollHeight;
}

function putHelperContent(oElem, sFieldSelect) {
  var oForm      = oElem.form;
  var sDependsOn = oElem.options[oElem.selectedIndex].value;

  // Search for helpers elements in same form
  for (var i=0; i< oForm.elements.length; i++) {
    var element = oForm.elements[i];
    
    // Filter helper elements
    var aFound = element.name.match(/_helpers_(.*)/);
    if (!aFound) {
    	continue;
    }
    
    Assert.that(aFound.length == 2, "Helper field name '%s' incorrect", element.name);
    Assert.that(element.nodeName == "SELECT", "Helper field name '%s' should be a select", element.name);
    
    
    // Check correspondance
		var aHelperParts = aFound[1].split("-");
		Assert.that(aHelperParts[0] == sFieldSelect, "Helper Field '%s' should target '%s' field",  element.name, sFieldSelect);
    
    // Show/Hide helpers
    var sHelperDependsOn = aHelperParts[1]; 
    if (sHelperDependsOn == "no_enum") {
    	sHelperDependsOn = "";
    }
    
    $(element)[sHelperDependsOn == sDependsOn ? "show" : "hide"]();
  }
}

function notNullOK(oElement) {
  if (oLabel = getLabelFor(oElement)) {
    oLabel.className = oElement.value ? "notNullOK" : "notNull";
  } 
}

function getBoundingForm(oElement) {
  if (!oElement) {
    return null;
  }
  
  if (oElement.nodeName.match(/^form$/i)) {
    return oElement;
  }
  
  return getBoundingForm(oElement.parentNode);
}

var bGiveFormFocus = true;

var FormObserver = {
  changes        : 0,
  lastFCKChange  : 0,
  fckEditor      : null,
  checkChanges : function() {
    if(this.changes) {
      return false;
    } else {
      return true;
    }
  },
  elementChanged : function() {
    this.changes++;
  },
  FCKChanged : function(timmer) {
    if(this.lastFCKChange < timmer) {
      this.elementChanged();
    }
    this.lastFCKChange = timmer;
  }
}

Element.addMethods({
  setResizable: function (oElement) {
    // oGrippie is the draggable element
    var oGrippie = new Element('div');
    
    // We remove the margin between the textarea and the grippie
    oElement.style.marginBottom = '0';
  
    /*var cookie = new CookieJar({
      expires: 3600*24,
      path: '/'
    }); 
    
    var entry = cookie.get('ElementHeight');
    
    if (entry) {
      var h = entry.(oElement.id).height;
      oElement.setStyle({height: h+'px'});
    }*/
    
    // grippie's class and style
    oGrippie.addClassName('grippie-h');
    oGrippie.setStyle({
      marginLeft: oElement.style.marginLeft,
      marginRight: oElement.style.marginRight,
      opacity: 0.5
    });
    
    // When the mouse is pressed on the grippie, we begin the drag
    oGrippie.onmousedown = startDrag;
    
    var staticOffset = null;
    
    function startDrag(e) {
      staticOffset = oElement.getHeight() - Event.pointerY(e); 
      oElement.setStyle({opacity: 0.4});
      document.onmousemove = performDrag;
      document.onmouseup = endDrag;
      return false;
    }
  
    function performDrag(e) {
      var lineHeight = oElement.getStyle('line-height');
      lineHeight = lineHeight.substr(0, lineHeight.length - 2);
      
      var h = Math.max(lineHeight*2, staticOffset + Event.pointerY(e)) - oGrippie.getHeight()/2;
      
      h = Math.round(h / lineHeight)*lineHeight;
      oElement.setStyle({height: null});
      oElement.rows = Math.round(h / lineHeight)-1;
      return false;
    }
  
    function endDrag(e) {
      oElement.setStyle({opacity: 1});
      document.onmousemove = null;
      document.onmouseup = null;
      /*var data  = (oElement.id): oElement.getHeight();
      cookie.put('ElementHeight', data);*/
      return false;
    }
  
    Element.insert(oElement, { after: oGrippie });
  }
} );

function prepareForm(oForm) {
  oForm = $(oForm);
  // Event Observer
  if(oForm.classNames().include("watched")) {
    new Form.Observer(oForm, 1, function() { FormObserver.elementChanged(); });
  }
  // Form preparation
  var sFormName = oForm.getAttribute("name");
  oForm.lockAllFields = (oForm._locked && oForm._locked.value) == "1"; 

  // Build label targets
  var aLabels = oForm.getElementsByTagName("label");
  var iLabel = 0;
  var oLabel = null;
  var sFor = null;
  while (oLabel = aLabels[iLabel++]) {
    // oLabel.getAttribute("for") is not accessible in IE
    if (sFor = oLabel.htmlFor) { 
      if (sFor.indexOf(sFormName) != 0) {
        oLabel.htmlFor = sFormName + "_" + sFor;
      }
    } 
  }

  // For each element
  var iElement = 0;
  var oElement = null;
  var sPropSpec = null;
  var aSpecFragments = null;
  while (oElement = oForm.elements[iElement++]) {
  	// Locked object
  	if (oForm.lockAllFields) {
  		oElement.disabled = true;
  	}
  	
    // Create id for each element if id is null
    if (!oElement.id) {
      oElement.id = sFormName + "_" + oElement.name;
      if (oElement.type == "radio") {
        oElement.id += "_" + oElement.value;
      }
    }
  
    //  Label emphasized for notNull elements
    if (sPropSpec = oElement.getAttribute("title")) {
      aSpecFragments = sPropSpec.split(" ");
      if (aSpecFragments.indexOf("notNull") != -1) {
        notNullOK(oElement);
        Element.addEventHandler(oElement, "change", notNullOK);
      }
    }else if (sPropSpec = oElement.className) {
      aSpecFragments = sPropSpec.split(" ");
      if (aSpecFragments.indexOf("notNull") != -1) {
        notNullOK(oElement);
        Element.addEventHandler(oElement, "change", notNullOK);
      }
    }
   
    // Focus on first text input
    if (bGiveFormFocus && oElement.type == "text" && !oElement.getAttribute("readonly")) {
      // Internet Explorer will not give focus to a not visible element but will raise an error
      if (oElement.clientWidth > 0) {
        oElement.focus();
        bGiveFormFocus = false;
      } 
    }
    
    if (oElement.type == "textarea") {
      oElement.setResizable();
    }
  }
}

function prepareForms() {
  // For each form
  var iForm = 0;
  var oForm = null;
  while (oForm = document.forms[iForm++]) {
    prepareForm(oForm);
  }
}

function submitFormAjax(oForm, ioTarget, oOptions) {
  if (oForm.attributes.onsubmit) {
    if (oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
      if (!oForm.onsubmit()) {
        return;
      }
    }  
  }

  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  var oDefaultOptions = {
    method : oForm.method
  };
 
  Object.extend(oDefaultOptions, oOptions);

  url.requestUpdate(ioTarget, oDefaultOptions);
}

/**
 * Submit a form in Ajax mode
 * New version to plage in onsubmit event of the form
 * @param oForm Form element
 * @return false to prevent page reloading
 */
function onSubmitFormAjax(oForm, oUserOptions) {
  var oOptions = {
    method : oForm.method,
    check : checkForm
  };
  
  Object.extend(oOptions, oUserOptions);
  
  // Check the form
  if (!oOptions.check(oForm)) {
    return false;;
  }

	// Build url
  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

	// Launch
  url.requestUpdate(SystemMessage.id, oOptions);
  
  // return
  return false;
}


function submitFormAjaxOffline(oForm, ioTarget, oOptions) {
  if (oForm.attributes.onsubmit) {
    if (oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
      if (!oForm.onsubmit()) {
        return;
      }
    }  
  }
  
  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  var oDefaultOptions = {
    method : "post"
  };
  Object.extend(oDefaultOptions, oOptions);

  url.requestUpdateOffline(ioTarget, oDefaultOptions);
}

function setSelectionRange(textarea, selectionStart, selectionEnd) {
  if (textarea.setSelectionRange) {
    textarea.focus();
    textarea.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (textarea.createTextRange) {
    var range = textarea.createTextRange();
    textarea.collapse(true);
    textarea.moveEnd('character', selectionEnd);
    textarea.moveStart('character', selectionStart);
    textarea.select();
  }
}

function setCaretToPos (textarea, pos) {
  setSelectionRange(textarea, pos, pos);
}

function insertAt(textarea, str) {
  // Inserts given text at selection or cursor position

  if (textarea.setSelectionRange) {
    // Mozilla UserAgent Gecko-1.4
    var scrollTop = textarea.scrollTop;

    var selStart = textarea.selectionStart;
    var selEnd   = textarea.selectionEnd  ;
		
    var strBefore = textarea.value.substring(0, selStart);
    var strAfter  = textarea.value.substring(selEnd);

    textarea.value = strBefore + str + strAfter;
		
    var selNewEnd = selStart + str.length;
    if (selStart == selEnd) { 
      // No selection: move caret
      setCaretToPos(textarea, selNewEnd);
    } else  {
      // Selection: re-select insertion
      setSelectionRange(textarea, selStart, selNewEnd);
    }
		
    textarea.scrollTop = scrollTop;
  } else if (document.selection) {
    // UserAgent IE-6.0
    textarea.focus();
    var range = document.selection.createRange();
    if (range.parentElement() == textarea) {
      var hadSel = range.text.length > 0;
      range.text = str;
      if (hadSel)  {
        range.moveStart('character', -range.text.length);
        range.select();
      }
    }
  } else { 
    // UserAgent Gecko-1.0.1 (NN7.0)
    textarea.value += str;
  }
}

function followUp(event) {
	// IE won't have a event target if handler is defined as an HTML attribute
	if (!event.target) {
		return;
	}
	
	// Redirect to next field
  var field = event.target;
  if (field.value.length == field.maxLength) {
    field.next().focus();
    setSelectionAll(field.next());
  }  
}

Object.extend(Form, {
  toObject: function (oForm) {
    var aFieldsForm  = Form.getElements(oForm);
    var oDataForm = {};
    //  Récupération des données du formualaire
    aFieldsForm.each(function (value) {
      var sNameElement  = value["name"];
      var sValueElement = Form.Element.getValue(value);
      oDataForm[sNameElement] = sValueElement;
      }
    );
    return oDataForm;
  },
  fromObject: function(oForm, oObject){
    $H(oObject).each(function (pair) {
      var oField = oForm[pair.key];
      if(oField){
        oField.value = pair.value;
      }
    });
  }
} );


function NumericField (form, element, step, min, max) {
    this.sField = form + "_" + element;
    this.min  = (min  != undefined) ? min  : null;
    this.max  = (max  != undefined) ? max  : null;
    this.step = (step != undefined) ? step : null;
}

NumericField.prototype = {
  // Increment function
  inc: function () {
    var oField = $(this.sField);
    var step = Number(this.getStep());
    var result = (parseInt(Number(oField.value) / step) + 1) * step;
    result = (result <= this.max || this.max == null) ? result : this.max;
    Form.Element.setValue(oField, result);
//    Form.Element.Selection.setAll(oField);
//    oField.focus();
  },

  // Decrement function
  dec: function () {
    var oField = $(this.sField);
    var step = Number(this.getStep());
    var result = (parseInt(Number(oField.value) / step) - 1) * step;
 	  result = (result >= this.min || this.min == null) ? result : this.min;
    Form.Element.setValue(oField, result);
//    Form.Element.Selection.setAll(oField);
//    oField.focus();
  },
  
  // Calculate appropriate step
  getStep: function () {
    var oField = $(this.sField);
    if (this.step == null) {
      var value = Math.abs(oField.value);
      if (value < 10)  return 1;
      if (value < 50)  return 5;
      if (value < 100) return 10;
      if (value < 500) return 50;
      if (value < 1000) return 100;
      if (value < 5000) return 500;
      return 1000;
    } else {
      return this.step;
    }
  }
}
