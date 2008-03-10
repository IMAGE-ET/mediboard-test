<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = new Control.Tabs('main_tab_group');
  menuTabs.setActiveTab("div_{{$category}}");
} );

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

</script>

<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_aed" />
  <input type="hidden" name="prescription_line_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="code_cip" value=""/>
</form>

<!-- Formulaire d'ajout de ligne d'element dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="element_prescription_id" value=""/>
</form>


<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#div_medicament">Médicaments</a></li>
  <li><a href="#div_dmi">DMI</a></li>
  <li><a href="#div_anapath">Anapath</a></li>
  <li><a href="#div_biologie">Biologie</a></li>
  <li><a href="#div_imagerie">Imagerie</a></li>
  <li><a href="#div_consult">Consult</a></li>
  <li><a href="#div_kine">Kiné</a></li>
  <li><a href="#div_soin">Soin</a></li>
</ul>
<hr class="control_tabs" />

<div id="div_medicament" style="display:none;">
  {{include file="inc_div_medicament.tpl"}}
</div>
<div id="div_dmi" style="display:none;">
  {{include file="inc_div_element.tpl" element="dmi"}}
</div>
<div id="div_anapath" style="display:none;">
  {{include file="inc_div_element.tpl" element="anapath"}}
</div>
<div id="div_biologie" style="display:none;">
  {{include file="inc_div_element.tpl" element="biologie"}}
</div>
<div id="div_imagerie" style="display:none;">
  {{include file="inc_div_element.tpl" element="imagerie"}}
</div>
<div id="div_consult" style="display:none;">
{{include file="inc_div_element.tpl" element="consult"}}
</div>
<div id="div_kine" style="display:none;">
{{include file="inc_div_element.tpl" element="kine"}}
</div>
<div id="div_soin" style="display:none;">
{{include file="inc_div_element.tpl" element="soin"}}
</div>


<script type="text/javascript">
	    
  // UpdateFields de l'autocompete de medicaments
	updateFieldsMedicament = function(selected) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLine(dn[0].firstChild.nodeValue);
	  $('searchProd_produit').value = "";
	}
	
	// UpdateFields de l'autocomplete des elements
	updateFieldsElement = function(selected, formElement, element) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLineElement(dn[0].firstChild.nodeValue);
	  $(formElement+'_'+element).value = "";
	}

  // Preparation des formulaire
  prepareForm(document.addLine);
  prepareForm(document.searchProd);
 
  // Autocomplete des medicaments
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.addParam("produit_max", 10);
  urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsMedicament
  } );
     
</script>