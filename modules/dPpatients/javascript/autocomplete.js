// $Id$

var InseeFields = {
	initCPVille: function(sFormName, sFieldCP, sFieldCommune, sFieldFocus) {
  	var oForm = document.forms[sFormName];
  	
		// Populate div creation for CP
    var oField = oForm.elements[sFieldCP];
		
    // Autocomplete for CP
		var url = new Url("dPpatients", "autocomplete_cp_commune");
		url.addParam("column", "code_postal");
		url.autoComplete(oField.id, null , {
			width: "250px",
			minChars: 2,
			updateElement: function(selected) {
				InseeFields.updateCPVille(selected, sFormName, sFieldCP, sFieldCommune, sFieldFocus);
			}
		} );
		
    // Populate div creation for Commune
    var oField = oForm.elements[sFieldCommune];
		
    // Autocomplete for Commune
    var url = new Url("dPpatients", "autocomplete_cp_commune");
    url.addParam("column", "commune");
    url.autoComplete(oField.id, null , {
      width: "250px",
      minChars: 3,
      updateElement: function(selected) {
        InseeFields.updateCPVille(selected, sFormName, sFieldCP, sFieldCommune, sFieldFocus);
      }
    } );
	},
	
	updateCPVille: function(selected, sFormName, sFieldCP, sFieldCommune, sFieldFocus) {
    var oForm = document.forms[sFormName];
		
		// Valuate CP and Commune
		$V(oForm.elements[sFieldCP     ], selected.select(".cp"     )[0].textContent, true);
    $V(oForm.elements[sFieldCommune], selected.select(".commune")[0].textContent, true);
	  
		// Give focus
	  if (sFieldFocus) {
	    $(oForm.elements[sFieldFocus]).focus();
	  }
	}
}

function updateFields(selected, sFormName, sFieldFocus, sFirstField, sSecondField) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $V(sFormName + '_' + sFirstField, dn[0].firstChild.firstChild.nodeValue, true);

  if(sSecondField){
    $V(sFormName + '_' + sSecondField, dn[2].firstChild.nodeValue, true);
  }
  
  if(sFieldFocus){
    $(sFormName + '_' + sFieldFocus).focus();
  }
}

function initPaysField(sFormName, sFieldPays, sFieldFocus){
  var sFieldId = sFormName + '_' + sFieldPays;
  var sCompleteId = sFieldPays + '_auto_complete';
	Assert.that($(sFieldId), "Pays field '%s'is missing", sFieldId);
	Assert.that($(sCompleteId), "Pays complete div '%s'is missing", sCompleteId);

  new Ajax.Autocompleter(
    sFieldId,
    sCompleteId,
    '?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_pays_autocomplete&fieldpays='+sFieldPays, {
      method: 'get',
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) { 
        updateFields(element, sFormName, sFieldFocus, sFieldPays) 
      }
    }
  );
}