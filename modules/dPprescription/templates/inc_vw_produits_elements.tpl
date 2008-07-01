<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('prescription_tab_group', false);
} );

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

// Lancement du mode de saisie popup
viewEasyMode = function(){
  var url = new Url();
  url.setModuleAction("dPprescription","vw_easy_mode");
  url.addParam("prescription_id", '{{$prescription->_id}}');
  url.popup(900,500,"Mode grille");
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
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />  
  <input type="hidden" name="debut" value="{{$today}}" />
  <input type="hidden" name="duree" value="" />
  <input type="hidden" name="unite_duree" value="" />
  <input type="hidden" name="callback" value="" />
  <input type="hidden" name="element_prescription_id" value=""/>
  <input type="hidden" name="_category_name" value="" />
</form>



<!-- Tabulations -->
<ul id="prescription_tab_group" class="control_tabs">
  <li><a href="#div_medicament">Médicaments</a></li>

{{if !$mode_pharma}}
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_nom_chapitre}}
  <li><a href="#div_{{$_nom_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_nom_chapitre}}{{/tr}}</a></li>
  {{/foreach}}
{{/if}}
</ul>



<hr class="control_tabs" />





{{if $prescription->_can_add_line}}

  {{if !$mode_protocole}}

  <table class="form" style="float: right; width: 110px;">
    <tr>
      <td class="date">
        <form name="selDateLine" action="?" method="get" style="float: right"> 
      
        {{if $prescription->type != "externe"}}   
	        <select name="debut_date" 
					        onchange="$('selDateLine_debut_da').innerHTML = new String;
	 				                    this.form.debut.value = '';
	 				          				  if(this.value == 'other') { 
	 				          					  $('calendarProt').show();
	 				          				  } else { 			    
	 				          				    this.form.debut.value = this.value;
	 				          				    $('calendarProt').hide();
	 				          				  }">
	     				  
				    <option value="other">Autre date</option>
				    <optgroup label="Séjour">
				      <option value="{{$prescription->_ref_object->_entree|date_format:'%Y-%m-%d'}}">Entrée: {{$prescription->_ref_object->_entree|date_format:"%d/%m/%Y"}}</option>
				      <option value="{{$prescription->_ref_object->_sortie|date_format:'%Y-%m-%d'}}">Sortie: {{$prescription->_ref_object->_sortie|date_format:"%d/%m/%Y"}}</option>
				    </optgroup>
				    <optgroup label="Opération">
				    {{foreach from=$prescription->_ref_object->_dates_operations item=_date_operation}}
				      <option value="{{$_date_operation}}">{{$_date_operation|date_format:"%d/%m/%Y"}}</option>
				    {{/foreach}}
						</optgroup>
				  </select>		 				
				  <!-- Prescription externe -->
				  <div id="calendarProt" style="border:none;">
				    {{mb_field object=$filter_line field="debut" form=selDateLine}}       
				  </div>
        {{else}}
           {{mb_field object=$filter_line field="debut" form="selDateLine"}}
        {{/if}}
        
         <script type="text/javascript">
	  	   Main.add( function(){
		       prepareForm(document.selDateLine);
		       Calendar.regField("selDateLine", "debut", false);
	    	} );
        </script>	
	    </form>
	    </td>
	  </tr>
	</table>

  <select name="advAction" style="float: right">
    <option value="">&mdash; Actions spécifiques</option>
    <option value="stopPerso" onclick="Prescription.stopTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Arret des traitements perso</option>
    <option value="goPerso" onclick="Prescription.goTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Reprise des traitements perso</option>
  </select>
  {{/if}}
  <button class="new" type="button" onclick="viewEasyMode();" style="float: right">Mode grille</button>
  
{{/if}}



<!-- Declaration des divs -->
<div id="div_medicament" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_medicament.tpl"}}
</div>

{{if !$mode_pharma}}
  {{foreach from=$specs_chapitre->_list item=_nom_chapitre}}
    <div id="div_{{$_nom_chapitre}}" style="display:none;">
      {{include file="../../dPprescription/templates/inc_div_element.tpl" element=$_nom_chapitre}}
    </div>
  {{/foreach}}
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
