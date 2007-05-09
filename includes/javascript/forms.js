// $Id$

function addHelp(sClass, oField, sName) {
  url = new Url;
  url.setModuleAction("dPcompteRendu", "edit_aide");
  url.addParam("class", sClass);
  url.addParam("field", sName || oField.name);
  url.addParam("text", oField.value);
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
  //Assert.that(oAreaField == "toto", "Helper element '%s' has no corresponding text area '%s' in the same form", oHelpElement.name, sPropName);

  var sValue = oHelpElement.value;
  oHelpElement.value = "";
  insertAt(oAreaField, sValue + '\n')
  oAreaField.scrollTop = oAreaField.scrollHeight;
}

function putHelperContent(oElem, sFieldSelect){
  var oForm       = oElem.form;
  var sValue      = oElem.options[oElem.selectedIndex].innerHTML;
  
  for(var i=0; i< oForm.elements.length; i++){
    var element = oForm.elements[i];
    
    var aFound  = element.name.match(/_helpers_(.*)/);
    var aFoundOk = aFound != null && aFound.length == 2;
    
    if (element.nodeType == 1 && element.nodeName == "SELECT" && aFoundOk) {
      var aFieldsDepend = aFound[1].split("-");
      if(aFieldsDepend[0] == sFieldSelect){
        if(!aFieldsDepend[1]){
          aFieldsDepend[1] = "";
        }
        if(aFieldsDepend[1] == sValue){
          $(element).show();
        }else{
          $(element).hide();
        }
      }
    }
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

function prepareForm(oForm) {
  var sFormName = oForm.getAttribute("name");

  // Build label targets
  aLabels = oForm.getElementsByTagName("label");
  iLabel = 0;
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
  while (oElement = oForm.elements[iElement++]) {
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
      if (aSpecFragments.contains("notNull")) {
        notNullOK(oElement);
        Element.addEventHandler(oElement, "change", notNullOK);
      }
    }else if (sPropSpec = oElement.className) {
      aSpecFragments = sPropSpec.split(" ");
      if (aSpecFragments.contains("notNull")) {
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
  }
}

function prepareForms() {
  // For each form
  var iForm = 0;
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
  
  url = new Url;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    if (oElement.type != "radio" || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  var oDefaultOptions = {
    method : "post"
  };
  Object.extend(oDefaultOptions, oOptions);

  url.requestUpdate(ioTarget, oDefaultOptions);
}

function submitFormAjaxOffline(oForm, ioTarget, oOptions) {
  if (oForm.attributes.onsubmit) {
    if (oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
      if (!oForm.onsubmit()) {
        return;
      }
    }  
  }
  
  url = new Url;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    if (oElement.type != "radio" || oElement.checked) {
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

function followUp(field, sFollowFieldName, iLength) {
  if (field.value.length == iLength) {
    fieldFollow = field.form.elements[sFollowFieldName];
    fieldFollow.focus();
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