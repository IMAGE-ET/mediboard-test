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


function initInseeFields(sFormName, sFieldCP, sFieldCity, sFieldFocus){
  new Ajax.Autocompleter(
    sFormName + '_' + sFieldCP,
    sFieldCP + '_auto_complete',
    'index.php?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcp='+sFieldCP, {
      method: 'get',
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) { updateFields(element, sFormName, sFieldFocus, sFieldCP, sFieldCity) }
    }
  );
  new Ajax.Autocompleter(
    sFormName + '_' + sFieldCity,
    sFieldCity + '_auto_complete',
    'index.php?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcity='+sFieldCity, {
      method: 'get',
      minChars: 4,
      frequency: 0.15,
      updateElement : function(element) { updateFields(element, sFormName, sFieldFocus, sFieldCP, sFieldCity) }
    }
  );
}

function initPaysField(sFormName, sFieldPays, sFieldFocus){
  new Ajax.Autocompleter(
    sFormName + '_' + sFieldPays,
    sFieldPays + '_auto_complete',
    'index.php?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_pays_autocomplete&fieldpays='+sFieldPays, {
      method: 'get',
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) { updateFields(element, sFormName, sFieldFocus, sFieldPays) }
    }
  );
}