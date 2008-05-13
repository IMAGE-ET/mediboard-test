<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('main_tab_group', false);
} );

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

// Lancement du mode de saisie popup
viewEasyMode = function(){
  var url = new Url();
  url.setModuleAction("dPprescription","vw_easy_mode");
  url.popup(850,500,"Mode de saisie simplifié");
}

</script>

<form name="addPriseElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="CPrescriptionLineElement" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="nb_fois" value="" />
  <input type="hidden" name="unite_fois" value="" />
  <input type="hidden" name="moment_unitaire_id" value="" />
  <input type="hidden" name="nb_tous_les" value="" />
  <input type="hidden" name="unite_tous_les" value="" />
  <input type="hidden" name="category_name" value="" />
</form>
	    
<!-- Formulaire d'ajout de ligne d'element dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="debut" value="" />
  <input type="hidden" name="duree" value="" />
  <input type="hidden" name="unite_duree" value="" />
  <input type="hidden" name="callback" value="" />
  <input type="hidden" name="element_prescription_id" value=""/>
  <input type="hidden" name="_category_name" value="" />
</form>

<div id="mode" style="position: absolute; right: 10px;">
  <button class="new" type="button" onclick="viewEasyMode();">Mode de saisie simplifié</button>
</div>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#div_medicament">Médicaments</a></li>

{{if !$mode_pharma}}
  <li><a href="#div_dmi">DMI</a></li>
  <li><a href="#div_anapath">Anapath</a></li>
  <li><a href="#div_biologie">Biologie</a></li>
  <li><a href="#div_imagerie">Imagerie</a></li>
  <li><a href="#div_consult">Consult</a></li>
  <li><a href="#div_kine">Kiné</a></li>
  <li><a href="#div_soin">Soin</a></li>
{{/if}}
</ul>
<hr class="control_tabs" />

<!-- Declaration des divs -->
<div id="div_medicament" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_medicament.tpl"}}
</div>
{{if !$mode_pharma}}
<div id="div_dmi" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="dmi"}}
</div>
<div id="div_anapath" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="anapath"}}
</div>
<div id="div_biologie" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="biologie"}}
</div>
<div id="div_imagerie" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="imagerie"}}
</div>
<div id="div_consult" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="consult"}}
</div>
<div id="div_kine" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="kine"}}
</div>
<div id="div_soin" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_element.tpl" element="soin"}}
</div>
{{/if}}



<script type="text/javascript">
	    	
// UpdateFields de l'autocomplete des elements
updateFieldsElement = function(selected, formElement, element) {
	Element.cleanWhitespace(selected);
	dn = selected.childNodes;
  Prescription.addLineElement(dn[0].firstChild.nodeValue, dn[1].firstChild.nodeValue);
  $(formElement+'_'+element).value = "";
}
     
</script>