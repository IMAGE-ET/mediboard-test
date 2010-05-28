{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add( function(){
  var editPerfForm = getForm('editPerf-{{$_prescription_line_mix->_id}}');
  {{if $_prescription_line_mix->type == "PCA"}}
	  $("bolus-{{$_prescription_line_mix->_id}}").show();
		changeModeBolus(editPerfForm);		
  {{/if}}
	toggleContinuiteLineMix(editPerfForm.continuite_perf,'{{$_prescription_line_mix->_id}}');
} );

</script>

<table class="tbl {{if $full_line_guid == $_prescription_line_mix->_guid}}active{{/if}}" id="prescription_line_mix-{{$_prescription_line_mix->_id}}"> 
<tbody class="hoverable {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}line_stopped{{/if}}">
{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
  <tr>
    <th colspan="9" id="th-perf-{{$_prescription_line_mix->_id}}" class="text element {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}arretee{{/if}}">
      <div style="float: left">
				{{if $_prescription_line_mix->_ref_prescription->type != "externe"}}
				  {{if $_prescription_line_mix->_perm_edit}}
					  <form name="editCondPerf-{{$_prescription_line_mix->_id}}">
					  	<input type="hidden" name="m" value="dPprescription" />
							<input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
							<input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
				      {{mb_field object=$_prescription_line_mix field=conditionnel onchange="submitFormAjax(this.form, 'systemMsg');" typeEnum=checkbox}}
				      {{mb_label object=$_prescription_line_mix field="conditionnel"}}
						</form>
					{{else}}
				    {{mb_label object=$_prescription_line_mix field="conditionnel"}}:
				    {{if $_prescription_line_mix->conditionnel}}Oui{{else}}Non{{/if}} 
				  {{/if}}
				{{/if}}

        {{if $_prescription_line_mix->_ref_parent_line->_id}}
	        {{assign var=parent_perf value=$_prescription_line_mix->_ref_parent_line}}
	        <img src="images/icons/history.gif" title="Ligne possédant un historique" 
	             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')"/>
	      {{/if}}
      </div>
      <div style="float: right">
        {{if $_prescription_line_mix->_protocole && !$_prescription_line_mix->substitute_for_id && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$_prescription_line_mix->_id}}','{{$_prescription_line_mix->_class_name}}')">
             Lignes de substitution
            ({{$_prescription_line_mix->_count_substitution_lines}})
            </button>
        {{/if}}
        
        {{if $full_line_guid == $_prescription_line_mix->_guid}}
		      <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '');"></button>
		    {{/if}}
      </div>
			 <div style="float: right">
      <!-- Formulaire de signature du praticien -->
      {{if $_prescription_line_mix->_can_vw_form_signature_praticien}}
			  <form name="validation-{{$_prescription_line_mix->_class_name}}-{{$_prescription_line_mix->_id}}" action="" method="post">
			    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
			    <input type="hidden" name="m" value="dPprescription" />
			    <input type="hidden" name="prescription_line_guid" value="{{$_prescription_line_mix->_guid}}" />
			    <input type="hidden" name="prescription_reelle_id" value="{{$prescription_reelle->_id}}" />
			    <input type="hidden" name="mode_pharma" value="0" />
			    {{if $_prescription_line_mix->signature_prat}}
			    <input type="hidden" name="annulation" value="1" />
			    {{/if}}
			    <button type="button" class="{{if $_prescription_line_mix->signature_prat}}cancel{{else}}tick{{/if}}" onclick="submitFormAjax(this.form,'systemMsg');">{{if $_prescription_line_mix->signature_prat}}Annuler signature{{else}}Signer{{/if}}</button>
			  </form>
      {{/if}}
     
		    <!-- Signature pharmacien -->
        {{if $_prescription_line_mix->_can_vw_form_signature_pharmacien}}
      		{{if $_prescription_line_mix->signature_pharma}}
					  <button type="button" class="cancel" onclick="submitSignaturePharmacien('{{$_prescription_line_mix->_id}}','{{$_prescription_line_mix->prescription_id}}','0')">Annuler la validation pharmacien</button>
					{{else}}
					  <button type="button" class="tick" onclick="submitSignaturePharmacien('{{$_prescription_line_mix->_id}}','{{$_prescription_line_mix->prescription_id}}','1')">Validation pharmacien</button>
					{{/if}}
			  {{/if}}
		  </div>
			
		  <!-- Accord du praticien -->
			{{if $mode_pharma}}
				<div style="float: right">
					{{if !$_prescription_line_mix->signature_pharma}}
						<form action="?" method="post" name="editLineAccordPraticien-{{$_prescription_line_mix->_id}}">
						  <input type="hidden" name="m" value="dPprescription" />
						  <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
						  <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
						  <input type="hidden" name="del" value="0" />
						  {{mb_field object=$_prescription_line_mix field="accord_praticien" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
						  {{mb_label object=$_prescription_line_mix field="accord_praticien"}}
						</form> 
					{{elseif $_prescription_line_mix->accord_praticien}}
					  En accord avec le praticien
					{{/if}}
				</div>
			{{/if}}
      <!-- Siganture du praticien -->
      {{if $_prescription_line_mix->_can_vw_signature_praticien}}
        <div class="mediuser" style="float: right; border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}};">	
          {{$_prescription_line_mix->_ref_praticien->_view}}
					{{if $_prescription_line_mix->signature_prat}}
					   <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
					{{else}}
					   <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
						{{if $_prescription_line_mix->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
        </div>
      {{/if}}
			<strong>
				{{foreach from=$_prescription_line_mix->_ref_lines item=_line name=perf_line}}
				  <a href="#produit{{$_line->_guid}}" onclick="Prescription.viewProduit(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');" style="display: inline;">
     		    {{$_line->_ucd_view}} 
					</a>
				  {{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td colspan="8">
    	{{assign var=type_line value=$_prescription_line_mix->type_line}}
      <form name="editPerf-{{$_prescription_line_mix->_id}}" action="" method="post">
	      <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
        <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            <td style="border:none;" rowspan="2">
              {{if $_prescription_line_mix->_can_delete_prescription_line_mix}}
	              <button type="button" class="trash notext" onclick="$V(this.form.del,'1'); return onSubmitFormAjax(this.form, { 
	                onComplete: function(){
             		    {{if @$mode_substitution}}
					  		      Prescription.viewSubstitutionLines('{{$_prescription_line_mix->substitute_for_id}}','{{$_prescription_line_mix->substitute_for_class}}');
					  		    {{else}}
					  			    Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
                    {{/if}}
			            }        
	              } );"></button>
              {{/if}}
            </td>
            <td style="border: none;">
			        {{mb_label object=$_prescription_line_mix field="type"}}:
							{{if $_prescription_line_mix->_perm_edit}}
							  {{assign var=types value="CPrescriptionLineMix"|static:"type_by_line"}}
								{{assign var=types_for_line value=$types.$type_line}}
								<select name="type" 
								        onchange="{{if $_prescription_line_mix->type_line == "perfusion"}}
												          if(this.value == 'PCA'){ 
												            $('bolus-{{$prescription_line_mix_id}}').show(); 
																		changeModeBolus(this.form);
																	} else { 
																	  resetBolus(this.form); 
																		$('bolus-{{$prescription_line_mix_id}}').hide(); 
																	}; 
																	{{/if}}
																	return onSubmitFormAjax(this.form);">
																		
																		
								  {{foreach from=$types_for_line item=_type}}
								    <option value="{{$_type}}" {{if $_type == $_prescription_line_mix->type}}selected="selected"{{/if}}>{{tr}}CPrescriptionLineMix.type.{{$_type}}{{/tr}}</option>	
									{{/foreach}}
								</select>
							{{else}}
							  {{mb_value object=$_prescription_line_mix field="type"}}
              {{/if}}
						
						</td>
						
						{{if $_prescription_line_mix->_protocole}}
              <td style="border:none;">
              <script type="text/javascript">
                            
                Main.add( function(){
                  var oForm = document.forms["editPerf-{{$_prescription_line_mix->_id}}"];
                  togglePerfDecalage(oForm);
                } );
                
              </script>
              {{mb_label object=$_prescription_line_mix field="date_debut"}}
                à {{mb_field object=$_prescription_line_mix field="jour_decalage" onchange="togglePerfDecalage(this.form); return onSubmitFormAjax(this.form);"}} 
                <span id="decalage_interv-{{$_prescription_line_mix->_id}}">{{mb_field object=$_prescription_line_mix field=decalage_interv showPlus="1" increment=1 size="2" form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}} h</span>
              </td>
            {{else}}
              <td style="border:none;">
                {{mb_label object=$_prescription_line_mix field="date_debut"}}
                {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
                  {{mb_field object=$_prescription_line_mix field=date_debut form="editPerf-$prescription_line_mix_id" onchange="changeColorPerf($prescription_line_mix_id,this.form); return onSubmitFormAjax(this.form);" register=true}}
                  {{mb_field object=$_prescription_line_mix field=time_debut form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);" register=true}}
                {{else}}
                  {{mb_value object=$_prescription_line_mix field=date_debut}}
                  {{mb_value object=$_prescription_line_mix field=time_debut}}
                {{/if}} 
              </td>       
            {{/if}} 
						
						<td style="border:none;">
             {{mb_label object=$_prescription_line_mix field=duree}}
             {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
               {{mb_field object=$_prescription_line_mix field=duree size=1 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}heures
             {{else}}
               {{mb_value object=$_prescription_line_mix field=duree}}heures
             {{/if}}    
            </td>
						
						<td style="border:none;">
						  {{if $_prescription_line_mix->type_line == "perfusion"}}
	              {{mb_title object=$_prescription_line_mix field=quantite_totale}}
	              {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
	                {{mb_field object=$_prescription_line_mix field=quantite_totale size=1 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($prescription_line_mix_id) } );"}} ml
	              {{else}}
	                {{mb_value object=$_prescription_line_mix field=quantite_totale}} ml
	              {{/if}}    
						  {{/if}}
            </td>
					
				    <td style="border:none;">
				      {{if $_prescription_line_mix->_perm_edit}}
				        {{if $_prescription_line_mix->_voies}}
					        <select name="voie" onchange="{{if !in_array($_prescription_line_mix->voie, $_prescription_line_mix->_voies)}}
					                                        Element.hide($('warning_voie_{{$_prescription_line_mix->_id}}')); 
					                                        Element.hide($('last_option_{{$_prescription_line_mix->_id}}'));
					                                      {{/if}}
					                                      submitFormAjax(this.form, 'systemMsg');">
					          {{foreach from=$_prescription_line_mix->_voies item=_voie}}
					            <option value="{{$_voie}}" {{if $_prescription_line_mix->voie == $_voie}}selected="selected"{{/if}}>{{$_voie}}</option>
					          {{/foreach}}
					          {{if !in_array($_prescription_line_mix->voie, $_prescription_line_mix->_voies)}}
					            <option value="{{$_prescription_line_mix->voie}}" selected="selected" id="last_option_{{$_prescription_line_mix->_id}}">{{$_prescription_line_mix->voie}}</option>
					          {{/if}}
					        </select>
					         {{if !in_array($_prescription_line_mix->voie, $_prescription_line_mix->_voies)}}
					         <div class="warning" id="warning_voie_{{$_prescription_line_mix->_id}}">
					           Attention, la voie selectionnée n'est plus disponible
					         </div>
					         {{/if}}
				        {{/if}}
				      {{else}}
				        <strong>{{mb_value object=$_prescription_line_mix field="voie"}}</strong>
              {{/if}}
	          </td>
				    <td style="border:none;">
				      {{if !$_prescription_line_mix->_protocole && $_prescription_line_mix->signature_prat}}
					       <button type="button" class="new" onclick="$V(this.form._add_perf_contigue, '1');
					                                                  return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
					          } } );">Faire évoluer</button>
					        <input type="hidden" name="_add_perf_contigue" value="" />
								  <input type="hidden" name="date_arret" value="{{$_prescription_line_mix->date_arret}}" />
								  <input type="hidden" name="time_arret" value="{{$_prescription_line_mix->time_arret}}" />
						
					        {{if $_prescription_line_mix->date_arret}}
					          <button type="button" class="cancel" onclick="this.form.date_arret.value=''; this.form.time_arret.value=''; return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
					          } } );">Annuler l'arrêt</button>
					          <br />
					          {{mb_value object=$_prescription_line_mix field=date_arret}} à {{mb_value object=$_prescription_line_mix field=time_arret}}
					        {{else}}
					          <button type="button" class="stop" onclick="this.form.date_arret.value='current';this.form.time_arret.value='current'; return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
					          } } );">Arrêter</button>
					        {{/if}}
				      {{/if}}
				    </td>   
					</tr>
					<tr>
					  <td style="border: none;"></td>
            <td style="border:none;">
					    <label>
							  <input type="radio" name="continuite_perf" 
								                    {{if !$_prescription_line_mix->_perm_edit}}disabled="disabled"{{/if}} 
								                    {{if $_prescription_line_mix->_continuite != 'discontinue'}}checked="checked"{{/if}} 
																		onchange="toggleContinuiteLineMix(this, '{{$_prescription_line_mix->_id}}');
																		{{if $_prescription_line_mix->type_line == 'oxygene'}}
																		  this.form.nb_tous_les.value = ''; 
																			this.form.duree_passage.value = '';
																		{{/if}}" value="continue"/> 
								Continue
							</label>
							<label>
								<input type="radio" name="continuite_perf"
                                    {{if !$_prescription_line_mix->_perm_edit}}disabled="disabled"{{/if}} 
								                    {{if $_prescription_line_mix->_continuite == 'discontinue'}}checked="checked"{{/if}} 
																		onchange="toggleContinuiteLineMix(this, '{{$_prescription_line_mix->_id}}');" value="discontinue"/>
								Discontinue		
					    </label>
						</td>
						<td style="border:none;" colspan="2">
					    
							{{if $_prescription_line_mix->type_line == "perfusion"}}
								<div style="display: none;" id="continue-{{$_prescription_line_mix->_id}}">
	                {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
	                Débit
	                {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
	                  {{mb_field object=$_prescription_line_mix field="vitesse" size="3" increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="this.form.nb_tous_les.value = ''; this.form.duree_passage.value = ''; return onSubmitFormAjax(this.form);"}}
	                {{else}}
	                  {{mb_value object=$_prescription_line_mix field="vitesse"}}
	                {{/if}}
	                <!-- Affichage de l'unite de prise -->
	                {{$types.$type_line}}
	              </div>
	
	              <div style="display: none;" id="discontinue-{{$_prescription_line_mix->_id}}">
	                {{if $_prescription_line_mix->type_line == "perfusion"}}
	                  {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
	                    A passer en
	                   {{mb_field object=$_prescription_line_mix size=2 field=duree_passage increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="this.form.vitesse.value = ''; return onSubmitFormAjax(this.form);"}} minutes
	                  {{elseif $_prescription_line_mix->duree_passage}}
	                    A passer en
	                    {{mb_value object=$_prescription_line_mix field=duree_passage}} minutes
	                  {{/if}}
	                {{/if}}
	                
									 toutes les 
	                {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
	                  {{mb_field object=$_prescription_line_mix field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="this.form.vitesse.value = ''; return onSubmitFormAjax(this.form);"}} heures
	                {{else}}      
	                  {{mb_value object=$_prescription_line_mix field="nb_tous_les"}} heures
	                {{/if}}
	              </div>
							{{/if}}
							
							{{if $_prescription_line_mix->type_line == "oxygene"}}
						      {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
                  Débit
                  {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
                    {{mb_field object=$_prescription_line_mix field="vitesse" size="3" increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
                  {{else}}
                    {{mb_value object=$_prescription_line_mix field="vitesse"}}
                  {{/if}}
									
                  <!-- Affichage de l'unite de prise -->
                  {{$types.$type_line}}
							
		              <span style="display: none;" id="continue-{{$_prescription_line_mix->_id}}"></span>
		              
		              <span style="display: none;" id="discontinue-{{$_prescription_line_mix->_id}}">
		                {{if $_prescription_line_mix->type_line == "oxygene"}}
		                  {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
		                  
		                  {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
		                   pendant {{mb_field object=$_prescription_line_mix size=2 field=duree_passage increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}} heures
		                  {{elseif $_prescription_line_mix->duree_passage}}
		                   pendant {{mb_value object=$_prescription_line_mix field=duree_passage}} heures
		                  {{/if}}
		                {{/if}}
		                 toutes les 
		                {{if $_prescription_line_mix->_can_modify_prescription_line_mix}}
		                  {{mb_field object=$_prescription_line_mix field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}} heures
		                {{else}}      
		                  {{mb_value object=$_prescription_line_mix field="nb_tous_les"}} heures
		                {{/if}}
		              </span>
              {{/if}}

            </td>
          </tr>
				  <tr id="bolus-{{$_prescription_line_mix->_id}}" style="display: none;">
				    <td style="border: none;" />
				    <td style="border: none;">
				    	{{mb_label object=$_prescription_line_mix field="mode_bolus"}}:
							{{mb_field object=$_prescription_line_mix field="mode_bolus" onchange="changeModeBolus(this.form); return onSubmitFormAjax(this.form);"}}
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_prescription_line_mix field="dose_bolus"}}:
							{{mb_field object=$_prescription_line_mix field="dose_bolus" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} mg
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_prescription_line_mix field="periode_interdite"}}:
							{{mb_field object=$_prescription_line_mix field="periode_interdite" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} min
						</td>
				  </tr>
        </table>
      </form>
    </td>
  </tr>
	{{if $_prescription_line_mix->type_line == "perfusion"}}
                     
  <tr>
    <td colspan="9">
      <table class="form group" id=lines-{{$_prescription_line_mix->_id}}>
	      {{foreach from=$_prescription_line_mix->_ref_lines item=line}}
	        {{assign var=line_id value=$line->_id}}
	        <tr>
	          <td style="border:none;">
	            <form name="editLinePerf-{{$line->_id}}" action="" method="post">
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="dosql" value="do_prescription_line_mix_item_aed" />
	              <input type="hidden" name="prescription_line_mix_item_id" value="{{$line->_id}}" />
	              <input type="hidden" name="del" value="0" />
	              <table class="form">
		              <tr>
		                <td style="border:none; width:1%;">
		                  {{if $_prescription_line_mix->_can_delete_prescription_line_mix_item && $_prescription_line_mix->type_line != "oxygene"}}
			                  <button class="trash notext" type="button" onclick="$V(this.form.del,'1'); submitFormAjax(this.form, 'systemMsg', { 
			                    onComplete: function(){
			                    	{{if @$mode_substitution}}
									  		      Prescription.viewSubstitutionLines('{{$_prescription_line_mix->substitute_for_id}}','{{$_prescription_line_mix->substitute_for_class}}');
									  		    {{else}}
									  			    Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$line->_protocole}}','{{$mode_pharma}}');
			                      {{/if}}
									  			}
			                  } );"></button>
		                  {{/if}}
		                </td>
					          <td style="width: 20%; border:none; vertical-align:middle;" class="text">
					            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
					            {{if $line->_can_vw_livret_therapeutique}}
									      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
									    {{/if}}  
									    {{if !$line->_ref_produit->inT2A}}
								        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
								      {{/if}}
									    {{if $line->_can_vw_generique}}
									      <img src="images/icons/generiques.gif" title="Produit générique" />
									    {{/if}} 
                      {{if $line->_ref_produit->_supprime}}
                        <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
                      {{/if}}
					            <strong>{{$line->_ucd_view}}
					                <span style="font-size: 0.8em; opacity: 0.7">({{$_line->_forme_galenique}})</span>
					             </strong>
					          </td>
			              <td style="border:none; width: 30%;">
					             <span style="float: right">
					             	{{if $_prescription_line_mix->_can_modify_prescription_line_mix_item && $_prescription_line_mix->type_line == "perfusion"}}
												<label>
												  {{mb_field object=$line field="solvant" typeEnum=checkbox 
													           onchange="removeSolvant(this.form.__solvant); return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($prescription_line_mix_id)} );"}} 
													Solvant
											  </label>
												{{/if}}
                        </span>
												
										   		
											{{mb_label object=$line field=quantite}}:
					            {{if $_prescription_line_mix->_can_modify_prescription_line_mix_item}}
												{{mb_field object=$line field=quantite size=4 increment=1 min=0 form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($prescription_line_mix_id, $line_id) });"}}

                        <!-- Unite de prise -->
													<select name="unite" style="width: 12em;" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry('{{$line->prescription_line_mix_id}}','{{$line->_id}}') });">
													  {{if $line->_ref_produit_prescription->_id}}
														   <option value="{{$line->_ref_produit_prescription->unite_prise}}">{{$line->_ref_produit_prescription->unite_prise}}</option>
														{{else}}
													    {{foreach from=$line->_unites_prise item=_unite}}
													      <option value="{{$_unite}}" {{if $line->unite == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
													    {{/foreach}}
														{{/if}}
												  </select>
											{{else}}
					              {{mb_value object=$line field=quantite}}
					              {{mb_value object=$line field=unite}}	
												{{if $line->solvant && $_prescription_line_mix->type_line == "perfusion"}}
												  (Solvant)
												{{/if}}		            
										  {{/if}}
											
											
					     		  </td>
			            </tr>
		            </table>
	            </form>  
	          </td>
	        </tr>
	      {{foreachelse}}
          <tr>
            <td colspan="20">
            	<div class="small-info">Aucun produit</div>
						</td>
          </tr>
	      {{/foreach}}
      </table>
    </td>
  </tr>
	{{/if}}
  {{if $_prescription_line_mix->_perm_edit || $_prescription_line_mix->commentaire}}
  <tr>
    <td colspan="9">
			<form name="commentaire-{{$_prescription_line_mix->_guid}}">
        {{mb_label object=$_prescription_line_mix field="commentaire" size=60}}: 
	      	{{if $_prescription_line_mix->_protocole}}
	      	  {{assign var=_line_praticien_id value=$app->user_id}}
		      {{else}}
		        {{assign var=_line_praticien_id value=$_prescription_line_mix->praticien_id}}
		      {{/if}}
		      {{if $_prescription_line_mix->_perm_edit}}
		      
					<input type="text" name="commentaire" value="{{$_prescription_line_mix->commentaire}}" size=60 onchange="{{if $_prescription_line_mix->substitute_for_id && !$_prescription_line_mix->substitution_active}}submitEditPerfCommentaireSubst('{{$_prescription_line_mix->_id}}',this.value);{{else}}submitAddComment('{{$_prescription_line_mix->_class_name}}', '{{$_prescription_line_mix->_id}}', this.value);{{/if}}" />
          <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this); this.form.commentaire.onchange();" style="width: 110px;">
		        <option value="">&mdash; Aide</option>
		        {{html_options options=$aides_prescription.$_line_praticien_id.CPrescriptionLineMix}}
		      </select>
		      <input type="hidden" name="_hidden_commentaire" value="" />
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPrescriptionLineMix', this.form._hidden_commentaire, 'commentaire');">
		        Nouveau
		      </button>
		      {{else}}
		          {{mb_value object=$_prescription_line_mix field="commentaire"}}
		      {{/if}}
	    </form>
			{{if !$_prescription_line_mix->date_pose && $_prescription_line_mix->_ref_prescription->object_id && 
           (($_prescription_line_mix->_ref_substitution_lines.CPrescriptionLineMedicament|@count) || ($_prescription_line_mix->_ref_substitution_lines.CPrescriptionLineMix|@count))}} 
          <form name="changeLine-{{$_prescription_line_mix->_guid}}" action="?" method="post">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_substitution_line_aed" />
            <select name="object_guid" style="width: 150px;" 
                    onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
                                 Prescription.reload('{{$_prescription_line_mix->_ref_prescription->_id}}', '', 'medicament');} } )">
              <option value="">Lignes de substitutions</option>
              {{foreach from=$_prescription_line_mix->_ref_substitution_lines item=lines_subst_by_chap}}
                  {{foreach from=$lines_subst_by_chap item=_line_subst}}
                  <option value="{{$_line_subst->_guid}}">
                     {{if $_line_subst->_class_name == "CPrescriptionLineMix"}}
                       {{$_line_subst->_short_view}}
                     {{else}}
                       {{$_line_subst->_view}}
                     {{/if}}
                     {{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
                {{/foreach}}
              {{/foreach}}
            </select>
          </form>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</tbody>
</table>