// $Id: $

function updateFields(selected, sFormName, sFieldFocus, sFirstField, sSecondField) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $(sFormName + '_' + sFirstField).value = dn[0].firstChild.firstChild.nodeValue;
  if(sSecondField){
    $(sFormName + '_' + sSecondField).value = dn[2].firstChild.nodeValue;
  }
  if(sFieldFocus){
    $(sFormName + '_' + sFieldFocus).focus();
  }
}

function initInseeFields(sFormName, sFieldCP, sFieldCity, sFieldFocus) {
  var sFieldId = sFormName + '_' + sFieldCP;
  var sCompleteId = sFieldCP + '_auto_complete';
	Assert.that($(sFieldId), "CP field '%s'is missing", sFieldId);
	Assert.that($(sCompleteId), "CP complete div '%s'is missing", sCompleteId);

  new Ajax.Autocompleter(
    sFieldId,
    sCompleteId,
    '?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcp='+sFieldCP, {
      method: 'get',
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) { 
      	updateFields(element, sFormName, sFieldFocus, sFieldCP, sFieldCity) 
      }
    }
  );
  
  var sFieldId = sFormName + '_' + sFieldCity;
  var sCompleteId = sFieldCity + '_auto_complete';
	Assert.that($(sFieldId), "City field '%s'is missing", sFieldId);
	Assert.that($(sCompleteId), "City complete div '%s'is missing", sCompleteId);

  new Ajax.Autocompleter(
    sFieldId,
    sCompleteId,
    '?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcity='+sFieldCity, {
      method: 'get',
      minChars: 4,
      frequency: 0.15,
      updateElement : function(element) { 
        updateFields(element, sFormName, sFieldFocus, sFieldCP, sFieldCity) 
      }
    }
  );
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