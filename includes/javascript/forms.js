// $Id$

function confirmExit() {
  if(bFormsToSave)
    alert("element non sauvegardé");
}

//window.onUnload = confirmExit();

var bFormsToSave = false;

function watchFormModified(id, oldval, newval) {
  //alert("élément modifié ("+id+") de "+oldval+" à "+newval);
  bFormsToSave = true;
  return newval;
}

function getLabelFor(oElement) {
  var aLabels = document.getElementsByTagName("label");
  var iLabel = 0;
  while (oLabel = aLabels[iLabel++]) {
    if (oElement.id == oLabel.getAttribute("for")) {
      return oLabel;
    }  
  } 
  
  return null; 
}

function getCheckedValue(radioObj) {
  if(!radioObj)
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

function setRadioValue(oElement, sValue) {
  for(var i = 0;i < oElement.length; i++) {
    if(oElement[i].value == sValue)
      oElement[i].checked = true;
  }
}

function pasteHelperContent(oHelpElement) {
  var aFound = oHelpElement.name.match(/_helpers_(.*)/);
  if (aFound.length != 2) throwError(printf("Helper element '%s' is not of the form '_helpers_propname'", oHelpElement.name));
  
  var oForm = oHelpElement.form;    
  var sPropName = aFound[1];
  var oAreaField = oForm.elements[sPropName];
  if (!oAreaField) throwError(printf("Helper element '%s' has no corresponding property element '%s' in the same form", oHelpElement.name, sPropName));

  insertAt(oAreaField, oHelpElement.value + '\n')
  oHelpElement.value = 0;
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

function prepareForms() {
  // Build label targets
  aLabels = document.getElementsByTagName("label");
  iLabel = 0;
  while (oLabel = aLabels[iLabel++]) {
  	oForm = getBoundingForm(oLabel);
  	if (sFor = oLabel.getAttribute("for")) {
      oLabel.setAttribute("for", oForm.getAttribute("name") + "_" + sFor);
  	} 
  } 

  var bGiveFocus = true;

  // For each form
  var iForm = 0;
  while (oForm = document.forms[iForm++]) {
    var sFormName = oForm.getAttribute("name");
    var sFormClass = oForm.getAttribute("class");
    
    // For each element
    var iElement = 0;
    while (oElement = oForm.elements[iElement++]) {
      // Watch elements for watch class forms
      if (sFormClass == "watch") {
        //if(oElement.type == "textarea") {
        //  oElement.watch("innerHTML", watchFormModified);
        //} else {
          oElement.watch("value", watchFormModified);
          //alert("Watch sur "+oElement.name);
        //}
      }
      // Create id for each element if id is null
      if (!oElement.id) {
        oElement.id = sFormName + "_" + oElement.name;
        if (oElement.type == "radio") {
          oElement.id += "_" + oElement.value;
        }
      }

      // Label emphasized for notNull elements
      if (sPropSpec = oElement.getAttribute("title")) {
        aSpecFragments = sPropSpec.split("|");
        if (aSpecFragments.contains("notNull")) {
          if (oLabel = getLabelFor(oElement)) {
            oLabel.className = "notNull";
          }
        }
      }
      
      // Focus on first text input
      if (bGiveFocus && oElement.type == "text" && !oElement.getAttribute("readonly")) {
        // Internet Explorer will not give focus to a not visible element but will raise an error
        if (oElement.clientWidth > 0) {
          oElement.focus();
          bGiveFocus = false;
        } 
      }
    }
  }
}

function checkMoreThan(oElement, aSpecFragments) {
  if (sFragment1 = aSpecFragments[1]) {
    switch (sFragment1) {
      case "moreThan":
        var sTargetElement = aSpecFragments[2];
        var oTargetElement = oElements.form.elements[sTargetElement];

		if (!oTargetElement) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
		}
		        
		if (oElement.value <= oTargetElement.value) {
		  return 
		}

        break;
           
      case "moreEquals":
        var sTargetElement = aSpecFragments[2];
        var oTargetElement = this.form.getElement(sTargetElement);

		if (!oTargetElement) {
          return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
		}
		        
		if (oElement.value < oTargetElement.value) {
		  return 
		}

        break;
    }
  };

  return null;
}

function checkElement(oElement, aSpecFragments) {
  aSpecFragments.removeByValue("confidential");
  bNotNull = aSpecFragments.removeByValue("notNull") > 0;
  if (oElement.value == "") {
    return bNotNull ? "Ne pas peut pas être vide" : null;
  }
  
  switch (aSpecFragments[0]) {
    case "ref":
      if (isNaN(oElement.value)) {
        return "N'est pas une référence (format non numérique)";
      }

      iElementValue = parseInt(oElement.value, 10);
      
      if (iElementValue == 0 && bNotNull) {
        return "ne peut pas être une référence nulle";
      }

      if (iElementValue < 0) {
        return "N'est pas une référence (entier négatif)";
      }
				
      break;
      
    case "str":
      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "length":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length != iLength) {
              return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
            }
  
            break;
            
          case "minLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length < iLength) {
              return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
            }
  
            break;
            
          case "maxLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length > iLength) {
              return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
            }
  
            break;
  
          case "sameAs":
	        var sTargetElement = aSpecFragments[2];
	        var oTargetElement = oElement.form.elements[sTargetElement];
			
			if (!oTargetElement) {
	          return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
			}
			        
			if (oElement.value != oTargetElement.value) {
			  var oTargetLabel = getLabelFor(oTargetElement);
			  var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : oElement.name;
			  return printf("Doit être identique à %s", sTargetLabel);
			}
	
	        break;
          default:
            return "Spécification de chaîne de caractères invalide";
        }
      };
      
   	  break;

    case "num":
      if (isNaN(oElement.value)) {
        return "N'est pas une chaîne numérique";
      }

      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "length":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length != iLength) {
              return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
            }
  
            break;
            
          case "minLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length < iLength) {
              return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
            }
  
            break;
            
          case "maxLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length > iLength) {
              return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
            }
  
            break;

          case "minMax":
            var iMin = parseInt(aSpecFragments[2], 10);
            var iMax = parseInt(aSpecFragments[3], 10);
            
            if (oElement.value > iMax || oElement.value < iMin) {
              return printf("N'est pas compris entre %i et %i", iMin, iMax);
            }
            
            break;
            
  
          default:
            return "Spécification de chaîne numérique invalide";
        }
      };
      
   	  break;
    
    case "enum":
      aSpecFragments.removeByIndex(0);
      if (!aSpecFragments.contains(oElement.value)) {
        return "N'est pas une valeur possible";
      }

      break;

    case "date":
      if(!oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)) {
      	debugObject(oElement);
        return "N'as pas un format correct";
      }
      
      break;

    case "time":
      if(!oElement.value.match(/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
        return "N'as pas un format correct";
      }
      
      break;

    case "dateTime":
      if(!oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})[ \+](\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
        return "N'as pas un format correct";
      }
      
      break;
    
    case "currency":
      if (!oElement.value.match(/^(\d+)(\.\d{1,2})?$/)) {
        return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
      }
      
      break;
    
	case "text":
	  break;
	  
	case "html":
	  break;

    case "code":
      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "ccam":
            if (!oElement.value.match(/^([a-z]){4}([0-9]){3}$/i)) {
              return "Code CCAM incorrect, doit contenir 4 lettres et 3 chiffres";
            }
          
          break;

          case "cim10":
            if (!oElement.value.match(/^([a-z0-9]){0,5}$/i)) {
              return "Code CCAM incorrect, doit contenir 5 lettres maximum";
            }
            
            break;

          case "adeli":
            if (!oElement.value.match("/^([0-9]){9}$/i")) {
              return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
            }
            
            break;

          case "insee":
            aMatches = oElement.value.match(/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i);
            if (!aMatches) {
              return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
            }

            nCode = parseInt(aMatches[1], 10);
            nCle = parseInt(aMatches[2], 10);
            if (97 - (nCode % 97) != nCle) {
              return "Matricule incorrect, la clé n'est pas valide";
            }
          
            break;

          default:
            return "Spécification de code invalide";
        }
      }

      break;
    default:
      return "Spécification invalide";
  }
  
  return null;
}

function checkForm(oForm) {
  var oElementFocus = null;
  var aMsgFailed = new Array;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    if (sPropSpec = oElement.getAttribute("title")) {
      var aSpecFragments = sPropSpec.split("|");
      var oLabel = getLabelFor(oElement);
      if (sMsg = checkElement(oElement, aSpecFragments)) {
        var sLabelTitle = oLabel ? oLabel.getAttribute("title") : null;
        var sMsgFailed = sLabelTitle ? sLabelTitle : printf("%s (val:'%s', spec:'%s')", oElement.name, oElement.value, sPropSpec);
        sMsgFailed += "\n => " + sMsg;
        aMsgFailed.push("- " + sMsgFailed);
        
        if (!oElementFocus) {
          oElementFocus = oElement;
        }
      }

      if (oLabel) {
        oLabel.style.color = sMsg ? "#f00" : "#000";
      }
    }
  }

  if (aMsgFailed.length) {
  	var sMsg = "Merci de remplir/corriger les champs suivants : \n";
  	sMsg += aMsgFailed.join("\n")
    alert(sMsg);
    
    if (oElementFocus) {
    oElementFocus.focus();
      if (sDoubleClickAction = oElementFocus.getAttribute("ondblclick")) {
        eval(sDoubleClickAction);
      }
    }
    
    return false;
  }
  
  return true;
}

function submitFormAjax(oForm, ioTarget, oOptions) {
  if(oForm.attributes.onsubmit) {
  if(oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
    sEventCode = oForm.attributes.onsubmit.nodeValue;
    sEventCode = sEventCode.replace(/(\W)this(\W)/g, "$1oForm$2");
    if(!eval(sEventCode))
      return;
  } }
  urlTarget = new Url;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    urlTarget.addParam(oElement.name, oElement.value);
  }

  var oDefaultOptions = {
    method : "post"
  };
  Object.extend(oDefaultOptions, oOptions);

  urlTarget.requestUpdate(ioTarget, oDefaultOptions);
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