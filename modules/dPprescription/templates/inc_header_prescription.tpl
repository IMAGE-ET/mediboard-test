{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">


checkRelativeDate = function(){
  var oFormApplyProtocole = getForm("applyProtocole");
  var url = new Url("dPprescription", "ajax_check_relative_date");
	url.addParam("pack_protocole_id", $V(oFormApplyProtocole.pack_protocole_id));
	url.requestJSON(function(count) {
	  // S'il y a des N dans le protocole, ouverture de la modale puis submitProtocole
		if(count){
		  modal("datetime_now_modal");
			Calendar.regField(oFormApplyProtocole.datetime_now)
		}
		// Sinon, application du protocole directement
		else {
		  submitProtocole();
		}
	});	
}


// refresh de la liste des protocoles dans le cas des protocoles
refreshListProtocole = function(oForm){
  var oFormFilter = document.selPrat;
  oFormFilter.praticien_id.value = oForm.praticien_id.value;
  oFormFilter.function_id.value = oForm.function_id.value;
  oFormFilter.group_id.value = oForm.group_id.value;
  if(oFormFilter.praticien_id.value || oFormFilter.function_id.value || oFormFilter.group_id.value){
	  submitFormAjax(oForm, 'systemMsg', { 
	    onComplete : function() { 
	       Protocole.refreshList(oForm.prescription_id.value);
				 window.tabProtocoles.setActiveTab("creation_prot");
	    } 
	  });
  }
}

changePraticien = function(praticien_id){
  if(document.addLine){
	  var oFormAddLine = document.addLine;
    oFormAddLine.praticien_id.value = praticien_id;
  }
  if(document.add_aerosol){
    var oFormAddAerosol = document.add_aerosol;
    oFormAddAerosol.praticien_id.value = praticien_id;
  }
	if(document.addLineCommentMed){
    var oFormAddLineCommentMed = document.addLineCommentMed;	
    oFormAddLineCommentMed.praticien_id.value = praticien_id;
	}
	if(document.addLineElement){
    var oFormAddLineElement = document.addLineElement;
    oFormAddLineElement.praticien_id.value = praticien_id;
	}
}

submitProtocole = function(){
  var oForm = getForm("applyProtocole");
  if(oForm.debut_date){
	  var debut_date = oForm.debut_date.value;
	  if(debut_date != "other" && oForm.debut){
	    oForm.debut.value = debut_date;
	  }
  }
	
	{{if $prescription->type != "externe"}}
	  var oFormDateSelection = getForm("selDateLine");
    $V(oForm.debut, $V(oFormDateSelection.debut));
	  $V(oForm.time_debut, $V(oFormDateSelection.time_debut));
	{{/if}}
	 
	if(document.forms.selPraticienLine){
	  oForm.praticien_id.value = document.forms.selPraticienLine.praticien_id.value;
	  oForm.pratSel_id.value = document.forms.selPraticienLine.praticien_id.value;
  }
  return onSubmitFormAjax(oForm);
}

selectLines = function(prescription_id, protocole_id, ids, tp) {
  var oForm = getForm("applyProtocole");
  
	if(document.forms.selPraticienLine){
    oForm.praticien_id.value = document.forms.selPraticienLine.praticien_id.value;
    oForm.pratSel_id.value = document.forms.selPraticienLine.praticien_id.value;
  }
	
  // Ouverture de la modale pour choisir les lignes
  window.selectLines = new Url("dPprescription", "ajax_select_lines");
  window.selectLines.addParam("prescription_id", prescription_id);
  window.selectLines.addParam("protocole_id", protocole_id);
  window.selectLines.addParam("pratSel_id", $V(oForm.pratSel_id));
  window.selectLines.addParam("praticien_id", $V(oForm.praticien_id));
  window.selectLines.addParam("ids[]", ids);
	window.selectLines.addParam("tp", tp);
  window.selectLines.requestModal(900, 500, {showClose: false, showReload: false});
}

selectStoppedLines = function(prescription_id){
  var url = new Url("dPprescription", "ajax_stopped_lines");
	url.addParam("prescription_id", prescription_id);
	url.requestModal(900, 500, {showClose: false, showReload: false});
}

selectValidatedLines = function(prescription_id){
  var url = new Url("dPprescription", "ajax_validated_lines");
  url.addParam("prescription_id", prescription_id);
  url.requestModal(900, 500, {showClose: false, showReload: false});
}

popupTransmission = function(sejour_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.addParam("addTrans", true);
  url.addParam("with_filter", '0');
  url.popup(700, 500, "Transmissions et Observations");
}

popupTraitements = function(dossier_medical_id) {
  var url = new Url("dPprescription", "ajax_vw_traitements_personnels");
  url.addParam("dossier_medical_id", dossier_medical_id);
  url.addParam("sejour_id", '{{$prescription->object_id}}');
  url.popup(700, 400, "{{tr}}CConsultAnesth-traitements{{/tr}}");
}

changeManualDate = function(){
  var oForm = getForm('selDateLine'); 
	oForm.debut_da.value = new String;
  oForm.debut.value = '';

  if($V(oForm.debut_date) == 'other') { 
	  $('manualDate').show(); 
		$('relativeDate').hide(); 
	} else {
	  var decalage = $V(oForm.debut_date).split('_');
		var jour_decalage = decalage[0];
	  var dateTime = decalage[1];
		var operation_id;
		
		if(jour_decalage != "I"){
	    $V(oForm.unite_decalage,"jour");
	    oForm.unite_decalage.disabled = "disabled";
	  } else {
	    oForm.unite_decalage.disabled = "";
	  }
		
		if(jour_decalage == "I" && decalage[2]){
		  operation_id = decalage[2];
			$V(oForm.operation_id, operation_id);
		}
		
		$V(oForm.jour_decalage, jour_decalage);
	  
		var dDebut = Date.fromDATETIME(dateTime);  
		if($V(oForm.unite_decalage) == "jour"){
		  dDebut.addDays(parseInt(oForm.decalage_line.value));
      $V(oForm.debut, dDebut.toDATE());
		} else {
		  dDebut.addHours(parseInt(oForm.decalage_line.value));
	    $V(oForm.debut, dDebut.toDATE());
	    $V(oForm.time_debut, dDebut.toTIME());
    }
		$('manualDate').hide();
		$('relativeDate').show();
	}
}

Main.add( function(){	
  var oFormProtocole = getForm("applyProtocole");
  var praticien_id;
  if(document.forms.selPraticienLine){
    praticien_id = document.forms.selPraticienLine.praticien_id.value;
		{{if $praticien_for_prot_id}}
      document.forms.selPraticienLine.praticien_id.value = {{$praticien_for_prot_id}};   
    {{/if}}
    changePraticien(praticien_id);

		pratSelect = document.forms.selPraticienLine.praticien_id;
		if($('protocole_prat_name')){
		  $('protocole_prat_name').update('Dr '+pratSelect.options[pratSelect.selectedIndex].text);
		}
  } else {
	 {{if $conf.dPprescription.CPrescription.role_propre}}
	   praticien_id = '{{$app->user_id}}';  
   {{else}}
     praticien_id = '{{$prescription->_ref_current_praticien->_id}}';
   {{/if}}
  }
  headerPrescriptionTabs = Control.Tabs.create('header_prescription', false);

  if(oFormProtocole){
	  var url = new Url("dPprescription", "httpreq_vw_select_protocole");
	  var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete", {
		  dropdown: true,
	    minChars: 2,
      valueElement: oFormProtocole.elements.pack_protocole_id,
			updateElement: function(selectedElement) {
			  var node = $(selectedElement).down('.view');
			  $V($("applyProtocole_libelle_protocole"), (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
        
				if (autocompleter.options.afterUpdateElement) {
			    autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
			  }
			},
	    callback: 
	      function(input, queryString){
				  if(getForm("selPraticienLine")){
	          return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+$V(document.forms.selPraticienLine.praticien_id)); 
					} else {
					  return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+praticien_id); 
	        }
	      }
	  } );	
  }
  
  if($('files-{{$prescription->_id}}-CPrescription')){
	  File.refresh('{{$prescription->_id}}','{{$prescription->_class}}');
  }
} );

</script>


{{assign var=is_executant_prescription value=$current_user->isExecutantPrescription()}}

{{assign var=praticien value=$prescription->_ref_praticien}}
{{if !$prescription->praticien_id ||
  ($prescription->praticien_id && ($prescription->praticien_id==$current_user->_id) || !$is_praticien)}}
  {{assign var=can_edit_protocole value=1}}
{{else}}
  {{assign var=can_edit_protocole value=0}}
{{/if}}

{{if $mode_protocole}}
<form name="addLibelle-{{$prescription->_id}}" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />

	<table class="form">
	  <tr>
	  	<th class="title" colspan="2">
        <button style="float: left" type="button" class="hslip notext" onclick="$('list_protocoles').toggle();" title="Afficher/cacher la colonne de gauche"></button>
			  <span style="float: right">
		      <button type="button" class="add" onclick="Protocole.duplicate('{{$prescription->_id}}')">Dupliquer</button> 
						{{if $can->admin}}
	           <button class="tick notext" type="button" onclick="Protocole.exportProtocole('{{$prescription->_id}}')">{{tr}}CPrescription.export_protocole{{/tr}}</button>
	        {{/if}}
	        {{if $can_edit_protocole}}
	          <button class="trash notext" type="button" onclick="if (confirm('{{tr}}CProtocole-confirm-deletion{{/tr}}{{$prescription->libelle|smarty:nodefaults|JSAttribute}}?'))Protocole.remove('{{$prescription->_id}}')">Supprimer</button>
	        {{/if}}
		    </span>

		    Modification du protocole
		  </th>
	  </tr>
	  <tr>
	    <th style="width:7em">{{mb_title object=$prescription field=libelle}}</th>
	    <td>
        <input type="text" name="libelle" value="{{$prescription->libelle}}"
          onchange="refreshListProtocole(this.form);"
          {{if !$can_edit_protocole}}
            readonly="readonly"
          {{/if}}/>
        <button class="tick notext" type="button"></button>
	    </td>
	  </tr>
	   <tr>
	    <th style="width:7em">{{mb_title object=$prescription field=_owner}}</th>
	     <td class="text">
         <!-- Modification du pratcien_id / user_id -->
         <select name="praticien_id" onchange="this.form.function_id.value=''; this.form.group_id.value=''; refreshListProtocole(this.form)"
         {{if !$can_edit_protocole}}
            disabled="disabled"
         {{/if}}>
           <option value="">&mdash; Choix d'un praticien</option>
 	        {{foreach from=$praticiens item=praticien}}
 	        <option class="mediuser" 
 	                style="border-color: #{{$praticien->_ref_function->color}};" 
 	                value="{{$praticien->_id}}"
 	                {{if $praticien->_id == $prescription->praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
 	        </option>
 	        {{/foreach}}
 	      </select>
 	      <select name="function_id" onchange="this.form.praticien_id.value='';this.form.group_id.value=''; refreshListProtocole(this.form)"
          {{if !$can_edit_protocole}}
            disabled="disabled"
          {{/if}}>
           <option value="">&mdash; Choix du cabinet</option>
           {{foreach from=$functions item=_function}}
           <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" 
           {{if $_function->_id == $prescription->function_id}}selected=selected{{/if}}>{{$_function->_view}}</option>
           {{/foreach}}
         </select>
         <select name="group_id" onchange="this.form.praticien_id.value='';this.form.function_id.value=''; refreshListProtocole(this.form)"
         {{if !$can_edit_protocole}}
            disabled="disabled"
         {{/if}}>
           <option value="">&mdash; Choix d'un etablissement</option>
           {{foreach from=$groups item=_group}}
           <option value="{{$_group->_id}}" 
           {{if $_group->_id == $prescription->group_id}}selected=selected{{/if}}>{{$_group->_view}}</option>
           {{/foreach}}
         </select>
	     </td>
		 </tr>
		 {{if $prescription->type != "externe"}}
		 <tr>
			 <th style="width:7em">{{mb_label object=$protocole field="type"}}</th>
			 <td>
		      <select name="type" onchange="refreshListProtocole(this.form);"
            {{if !$can_edit_protocole}}
              disabled="disabled"
            {{/if}}>
		        <option value="pre_admission" {{if $prescription->type == "pre_admission"}}selected="selected"{{/if}}>Pré-admission</option>
		        <option value="sejour" {{if $prescription->type == "sejour"}}selected="selected"{{/if}}>Séjour</option>
		        <option value="sortie" {{if $prescription->type == "sortie"}}selected="selected"{{/if}}>Sortie</option>
		      </select>  
			  </td>
		  </tr>
	   {{/if}}
		 <tr>
		 	 <th>{{mb_label object=$prescription field="fast_access"}}</th>
			 <td>
         {{if $can_edit_protocole}}
           {{mb_field object=$prescription field="fast_access" onchange="onSubmitFormAjax(this.form);"}}
         {{else}}
           {{mb_value object=$prescription field="fast_access"}}
         {{/if}}
       </td>
		 </tr>
     <tr>
       <th>{{mb_label object=$prescription field="checked_lines"}}</th>
       <td>
         {{if $can_edit_protocole}}
           {{mb_field object=$prescription field="checked_lines" onchange="onSubmitFormAjax(this.form);"}}
         {{else}}
           {{mb_value object=$prescription field="checked_lines"}}
         {{/if}}
       </td>
     </tr>
  </table>
</form>
<table class="form">
	<tr>
    <th style="width: 101px;">Fichiers</th>
    <td id="files-{{$prescription->_id}}-CPrescription"></td>
   </tr>
</table>
{{/if}}

<form name="moment_unitaire" action="?" method="get">
  <select name="moment_unitaire_id" style="width: 150px; display: none;">  
    <option value="">&mdash; Moment</option>
   {{foreach from=$moments key=type_moment item=_moments}}
   <optgroup label="{{$type_moment}}">
   {{foreach from=$_moments item=moment}}
   {{if $type_moment == "Complexes"}}
     <option value="complexe-{{$moment->code_moment_id}}">{{$moment->_view}}</option>
   {{else}}
     <option value="unitaire-{{$moment->_id}}">{{$moment->_view}}</option>
   {{/if}}
   {{/foreach}}
   </optgroup>
   {{/foreach}}
  </select>
</form>
       
<table class="form">
  {{if !$mode_protocole}}
  <tr>
    <th class="title text" colspan="3">			
      <!-- Selection du praticien prescripteur de la ligne -->
       {{if $mode_pharma}}
         <button style="float: left" type="button" class="hslip notext" onclick="$('left-column').toggle();" title="Afficher/cacher la colonne de gauche"></button>
        {{/if}}
			 
      <div style="float: right; text-align: right;">
      	<button type="button" class="print"
          onclick="Prescription.printOrdonnance('{{$prescription->_id}}');" />Ordonnance</button>
        {{if !$hide_header}}
				<br />
				{{/if}}
				
       	{{if !$is_praticien && !$mode_protocole && ($operation_id || $can->admin || $mode_pharma || ($is_executant_prescription && !$conf.dPprescription.CPrescription.role_propre))}}
				<form name="selPraticienLine" action="?" method="get">
				  <select style="font-size: 0.8em; width: 15em;" name="praticien_id" onchange="changePraticienMed(this.value); {{if !$mode_pharma}}changePraticienElt(this.value);{{/if}} if($('protocole_prat_name')) { $('protocole_prat_name').update('Dr '+this.options[this.selectedIndex].text); }">
						<optgroup label="Responsables">
				      <option class="mediuser" style="border-color: #{{$prescription->_ref_current_praticien->_ref_function->color}};" 
						          value="{{$prescription->_ref_current_praticien->_id}}"
						          {{if $prescription->_ref_current_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$prescription->_ref_current_praticien->_view}}</option>
				      {{if @$operation->_ref_anesth->_id}}
				        <option  class="mediuser" style="border-color: #{{$operation->_ref_anesth->_ref_function->color}};" 
				                 value="{{$operation->_ref_anesth->_id}}"
				                 {{if $operation->_ref_anesth->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$operation->_ref_anesth->_view}}</option>
				      {{/if}}
				    </optgroup>
				    <optgroup label="Tous les praticiens">
				      {{foreach from=$listPrats item=_praticien}}
                <option class="mediuser" 
						            style="border-color: #{{$_praticien->_ref_function->color}};" 
						            value="{{$_praticien->_id}}"
						            {{if $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
						    </option>
              {{/foreach}}
				    </optgroup>
				  </select>
				</form>
				{{/if}}
      </div>
			
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
			  {{mb_include module=system template=inc_object_notes object=$prescription->_ref_object float=left}}
      {{/if}}
			
      {{if !$mode_protocole && !$hide_header}}
        {{if $prescription->type == "externe"}}
         <span style="float: left">
         {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$prescription->_ref_patient size=42}}
         </span>
        {{else}}
         <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$prescription->_ref_patient->_id}}"'>
          {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$prescription->_ref_patient size=42}}
         </a>
       {{/if}}
      {{/if}}
			
			
      {{if !$mode_protocole && !$hide_header}}
			  <h2 style="color: #fff; font-weight: bold;">
          {{$prescription->_ref_patient->_view}}  
					
					{{if $prescription->type != "externe"}}
	          <span style="font-size: 0.7em;"> - {{$prescription->_ref_object->_shortview|replace:"Du":"Séjour du"}}</span>
          {{/if}}
				 
          <span id="antecedent_allergie">
	          {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
	          {{assign var=sejour_id value=$prescription->object_id}}
	          {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl" nodebug=true}}    
	        </span> 
				</h2>
				
	    {{/if}}
    </th>
  </tr>
	{{if !$hide_header}}
	<tr>
		<td>
	 	<table class="form">
	 		{{assign var=patient value=$prescription->_ref_patient}}
	 		{{mb_include module=dPprescription template=inc_infos_patients_soins}}
	 	</table>
		<hr />
		</td>
  </tr>
	{{/if}}
  {{/if}}
  
	{{assign var=easy_mode value=$app->user_prefs.easy_mode}}
	<tr>
  	<td colspan="3">
			<ul id="header_prescription" class="control_tabs small">
				{{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin || $is_executant_prescription)}}
				<li><a href="#div_protocoles">Protocoles <span id="protocole_prat_name"></span></a></li>
				{{/if}}
				
				<li>
				<button type="button" onclick="modalWindowTools = modal($('modal-outils'));" class="search">
					Outils
				</button>
				{{if !$prescription->_protocole_locked}}
          <button class="new" type="button" onclick="viewEasyMode('{{$mode_protocole}}','{{$mode_pharma}}', menuTabs.activeContainer.id);">Mode grille</button>
        {{/if}}
				</li>
				
				<li style="float: right;">
					{{if $prescription->object_id && ($is_praticien || $mode_protocole || @$operation_id || $can->admin || $is_executant_prescription)}}
		        {{if !$mode_pharma}}
		          {{if $is_praticien || ($is_executant_prescription && $conf.dPprescription.CPrescription.role_propre)}}
		            <form name="signaturePrescription" method="post" action="">
		              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		              <input type="hidden" name="chapitre" value="all" />
		              <input type="hidden" name="del" value="0" />
		              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
		              <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg');" style="margin:0px">Tout signer</button>
		            </form>
		          
		            <form name="removeSignaturePrescription" method="post" action="">
		              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		              <input type="hidden" name="chapitre" value="all" />
		              <input type="hidden" name="annulation" value="1" />
		              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
		              <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg');" style="margin:0px">Annuler signatures</button>
		            </form>
								
							  <form name="removeLines" method="post" action="">
	                <input type="hidden" name="dosql" value="do_remove_lines" />
	                <input type="hidden" name="m" value="dPprescription" />
	                <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	                <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	                <button type="button" class="trash" style="margin:0px" onclick="if(confirm('Etes vous sur de vouloir supprimer vos lignes non signées ?')){
	                  submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reloadPrescSejour('{{$prescription->_id}}'); } } )
	                }">Supprimer</button>
               </form>
		          {{else}}
		            <!-- Validation de la prescription -->
		            <button type="button" class="tick" onclick="Prescription.valideAllLines('{{$prescription->_id}}');" style="margin:0px">
		              Tout signer
		            </button>
		            <button type="button" class="cancel" onclick="Prescription.valideAllLines('{{$prescription->_id}}','1')" style="margin:0px">
		            	Annuler signatures
								</button>
								
								{{if $is_executant_prescription || @$operation_id}}
									<form name="removeLines" method="post" action="">
		                  <input type="hidden" name="dosql" value="do_remove_lines" />
		                  <input type="hidden" name="m" value="dPprescription" />
		                  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
											{{if @$operation_id}}
											<input type="hidden" name="operation_id" value="{{$operation_id}}" />
                      {{/if}}
		                  <input type="hidden" name="praticien_id" value="" />
		                  <button type="button" class="trash" style="margin:0px" onclick="$V(this.form.praticien_id, $V(getForm('selPraticienLine').praticien_id)); if(confirm('Etes vous sur de vouloir supprimer les lignes non signées du praticien selectionné ?')){
		                    submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reloadPrescSejour('{{$prescription->_id}}'); } } )
		                  }">Supprimer</button>
		               </form>
								 {{/if}}
		          {{/if}}
		        {{/if}}
		      {{/if}}			
				</li>
			</ul>
			<hr class="control_tabs" />
		</td>
  </tr>
  <tr>
  {{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin || $is_executant_prescription)}}
   <td id="div_protocoles" colspan="3">
     {{if $protocoles_ids|@count}}
        <span style="float: right;" href="#1" onmouseover="ObjectTooltip.createDOM(this, 'protocoles_ids_hist')">
          {{$protocoles_ids|@count}} protocole(s) appliqué(s)
        </span>
        <div style="display: none;" id="protocoles_ids_hist">
          <ul>
          {{foreach from=$protocoles_ids item=_protocole}}
            <li>{{$_protocole->libelle}}</li>
          {{/foreach}}
          </ul>
        </div>
      {{/if}}
      
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?" onsubmit="return false;">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	      <input type="hidden" name="pratSel_id" value="" />
	      <input type="hidden" name="pack_protocole_id" value="" />
				<input type="hidden" name="time_debut" value="" />
        
				<span id="datetime_now_modal" style="display: none;">
				  <table class="form">
				  	<tr>
				  		<th class="title">
				  			Sélection de la date relative de prescription (M)
				  		</th>
						</tr>
						<tr>
							<td>
								<input type="hidden" name="datetime_now" value="{{$now}}" class="dateTime" />
							  <button type="button" onclick="submitProtocole(); Control.Modal.close();" class="submit">Appliquer</button>
						  </td>
					  </tr>
          </table> 	
        </span>
				
	      <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 300px;" />
	      <div style="display:none; width: 350px;" class="autocomplete" id="protocole_auto_complete"></div>
 				{{if $prescription->type != "externe"}}
					<input type="hidden" name="debut" value="" />
	        
 				  {{if $prescription->_dates_dispo}}
	 				  {{if $prescription->_dates_dispo|@count == 1}}
						<input type="hidden" name="operation_id" value="{{$prescription->_dates_dispo|@array_keys|@reset}}" />
						{{else}}
						Intervention
	 				  <select name="operation_id">
	 				    {{foreach from=$prescription->_dates_dispo key=_operation_id item=_date_operation}}
	 				      <option value="{{$_operation_id}}" {{if $operation_id == $_operation_id}}selected == "selected"{{/if}}>{{$_date_operation|date_format:$conf.datetime}}</option>
	 				    {{/foreach}}
							</select>
						{{/if}}
				  {{/if}}
 				{{else}}
 				  <!-- Prescription externe -->
					{{mb_field object=$protocole_line field="debut" form=applyProtocole}}       
	 				<script type="text/javascript">
	 				  dates = {
					    current: {
					      start: "{{$today}}",
					      stop: ""
					    }
					  }
	 				  Main.add( function(){
	            Calendar.regField(getForm("applyProtocole").debut, dates);
	          } );
	 				</script>				 				
 				{{/if}}
        <button type="button" class="submit oneclick" onclick="checkRelativeDate();">Ouvrir/Appliquer</button>
		 </form>
    </td>  
  {{/if}}
  </tr>  
</table>
  
<table class="form" id="modal-outils" style="display: none; width: 400px;">
	<tr>
    <th class="title">
        <button class="cancel notext" onclick="modalWindowTools.close();" style="float: right"></button>
        Outils
    </th>
  </tr>
	
	{{if !$mode_protocole && ($is_praticien || @$operation_id || $can->admin || $is_executant_prescription)}}
	<tr>
		<th class="category">
			 Début de la ligne
		</th>
	</tr>
	<tr>
		<td>
			 <form name="selDateLine" action="?" method="get"> 
        {{if $prescription->type != "externe"}} 
          <select name="debut_date" onchange="changeManualDate();">
            <option value="other">Autre date</option>
            <optgroup label="Intervention">
            {{foreach from=$prescription->_dates_dispo key=_operation_id item=_date_operation}}
              <option value="I_{{$_date_operation}}_{{$_operation_id}}">Intervention - {{$_date_operation|date_format:$conf.datetime}}</option>
            {{/foreach}}
            </optgroup>
            <optgroup label="Séjour">
              <option value="E_{{$prescription->_ref_object->_entree}}">Entrée - {{$prescription->_ref_object->_entree|date_format:$conf.datetime}}</option>
              <option value="S_{{$prescription->_ref_object->_sortie}}">Sortie - {{$prescription->_ref_object->_sortie|date_format:$conf.datetime}}</option>
            </optgroup>
          </select>           
          <!-- Selection manuelle de la date -->
          <span id="manualDate" style="border:none; margin-right: 60px">
            {{mb_field object=$filter_line field="debut" form=selDateLine}}
            {{mb_field object=$filter_line field="time_debut" form=selDateLine}}
          </span>
          <!-- Selection relative de la date -->
          <span id='relativeDate' style="border:none; margin-right: 60px; display: none;">
            {{mb_field object=$filter_line field="decalage_line" form=selDateLine showPlus=1 increment=1 size="3" onchange="changeManualDate()"}}
            {{mb_field object=$filter_line field="unite_decalage" onchange="changeManualDate()"}}
          </span>
          <input type="hidden" name="jour_decalage" value="" />
          <input type="hidden" name="operation_id" value="" />
        {{else}}
           {{mb_field object=$filter_line field="debut" form="selDateLine"}}
        {{/if}}
        
         <script type="text/javascript">
         Main.add( function(){
           Calendar.regField(getForm("selDateLine").debut);
        } );
        </script> 
      </form>
		</td>
	</tr>
	{{/if}}
	
  <tr>
    <th class="category">
       Impressions
    </th>
  </tr>
	
	<tr>
		<td class="button">
			<button type="button" class="print" onclick="Prescription.printOrdonnance('{{$prescription->_id}}');" />Ordonnance</button>
      {{if $prescription->object_id && $prescription->object_class == "CSejour"}}
        <button type="button" class="print" onclick="PlanSoins.printBons('{{$prescription->_id}}');" title="{{tr}}Print{{/tr}}">Bons</button>
      {{/if}}
		</td>
	</tr>
{{if !$mode_protocole && $prescription->type == "sejour"}}
	<tr>
		<th class="category">
			Actions
		</th>
	</tr>
	<tr>
	  <td class="button">
			<button type="button" class="cancel" onclick="selectValidatedLines('{{$prescription->_id}}')">Tout arrêter</button>
			{{if $is_praticien}}
			  <button type="button" class="tick" onclick="selectStoppedLines('{{$prescription->_id}}');">Tout reprendre</button>
      {{/if}}
		</td>	
	</tr>
{{/if}}	
	{{if $prescription->object_id && ($is_praticien || $mode_protocole || @$operation_id || $can->admin) && $prescription->type != "externe"}}
	<tr>
		<th class="category">Traitements personnels</th>
	</tr>	
	<tr>
		<td class="button">
			{{if $prescription->type == "sejour" && $dossier_medical->_id}}
        <button type="button" class="new" onclick="popupTraitements('{{$dossier_medical->_id}}')">{{tr}}CConsultAnesth-traitements{{/tr}}</button>
			{{/if}}
			<button type="button" class="cancel" onclick="Prescription.stopTraitementPerso('{{$prescription->_id}}','{{$mode_pharma}}')">Arrêter</button>
			<button type="button" class="tick" onclick="Prescription.goTraitementPerso('{{$prescription->_id}}','{{$mode_pharma}}')">Reprendre</button>
    </td>
  </tr>	
	{{/if}}
	
	<tr>
	  <th class="category">Informations</th>	
	</tr>
	<tr>
		<td class="button">
			{{if !$mode_protocole && $prescription->type == "sejour"}}
        <button type="button" class="search" onclick="popupTransmission('{{$prescription->object_id}}');">Transmissions</button>
      {{/if}} 
			
			{{if @$modules.dPImeds->mod_active && $prescription->object_class == "CSejour"}}
			  <button type="button" class="search" onclick="Prescription.popupLabo('{{$prescription->object_id}}');">Labo</button>
      {{/if}}

			<button type="button" class="search" onclick="Prescription.popup('{{$prescription->_id}}', 'viewAlertes');">Alertes</button>
			{{if $prescription->object_id}}
			<button type="button" class="search" onclick="Prescription.popup('{{$prescription->_id}}', 'viewHistorique');">Historique</button>
      <button type="button" class="search" onclick="Prescription.popup('{{$prescription->_id}}', 'viewSubstitutions');">Substitutions</button>
      {{/if}}
		</td>
	</tr>	
		      
  {{if !$mode_protocole && $can->admin && $prescription->type == "sejour"}}
	<tr>
		<th class="category">Planifications systèmes</th>
	</tr>
	<tr>
		<td class="button">
    <form name="removePlanifsSystemes" action="?" method="post">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_aed" />
      <input type="hidden" name="del" value="dPprescription" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="_purge_planifs_systemes" value="true" />
      <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg');">Suppression des planifs systemes</button>
    </form>
		</td>
	</tr>	
  {{/if}}
</table>
    
<hr class="control_tabs" />