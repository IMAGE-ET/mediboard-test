// $Id: $

function updateFields(selected, sFormName, sFieldCP, sFieldCity) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $(sFormName + '_' + sFieldCP).value = dn[0].firstChild.firstChild.nodeValue;
  $(sFormName + '_' + sFieldCity).value = dn[2].firstChild.nodeValue;
}


function initInseeFields(sFormName, sFieldCP, sFieldCity){
  new Ajax.Autocompleter(
    sFormName + '_' + sFieldCP,
    sFieldCP + '_auto_complete',
    'index.php?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcp='+sFieldCP, {
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) { updateFields(element, sFormName, sFieldCP, sFieldCity) }
    }
  );
  new Ajax.Autocompleter(
    sFormName + '_' + sFieldCity,
    sFieldCity + '_auto_complete',
    'index.php?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete&fieldcity='+sFieldCity, {
      minChars: 4,
      frequency: 0.15,
      updateElement : function(element) { updateFields(element, sFormName, sFieldCP, sFieldCity) }
    }
  );
}