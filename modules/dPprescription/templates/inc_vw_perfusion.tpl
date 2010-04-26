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
  var editPerfForm = getForm('editPerf-{{$_perfusion->_id}}');
  {{if $_perfusion->type == "PCA"}}
	  $("bolus-{{$_perfusion->_id}}").show();
		changeModeBolus(editPerfForm);		
  {{/if}}
	toggleContinuitePerf(editPerfForm.continuite_perf,'{{$_perfusion->_id}}');
} );

</script>

<table class="tbl {{if $full_line_guid == $_perfusion->_guid}}active{{/if}}" id="perfusion-{{$_perfusion->_id}}"> 
<tbody class="hoverable {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}line_stopped{{/if}}">
{{assign var=perfusion_id value=$_perfusion->_id}}
  <tr>
    <th colspan="9" id="th-perf-{{$_perfusion->_id}}" class="text element {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
      <div style="float: left">
				{{if $_perfusion->_ref_prescription->type != "externe"}}
				  {{if $_perfusion->_perm_edit}}
					  <form name="editCondPerf-{{$_perfusion->_id}}">
					  	<input type="hidden" name="m" value="dPprescription" />
							<input type="hidden" name="dosql" value="do_perfusion_aed" />
							<input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
				      {{mb_field object=$_perfusion field=conditionnel onchange="submitFormAjax(this.form, 'systemMsg');" typeEnum=checkbox}}
				      {{mb_label object=$_perfusion field="conditionnel"}}
						</form>
					{{else}}
				    {{mb_label object=$_perfusion field="conditionnel"}}:
				    {{if $_perfusion->conditionnel}}Oui{{else}}Non{{/if}} 
				  {{/if}}
				{{/if}}

        {{if $_perfusion->_ref_parent_line->_id}}
	        {{assign var=parent_perf value=$_perfusion->_ref_parent_line}}
	        <img src="images/icons/history.gif" title="Ligne possédant un historique" 
	             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')"/>
	      {{/if}}
      </div>
      <div style="float: right">
        {{if $_perfusion->_protocole && !$_perfusion->substitute_for_id && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$_perfusion->_id}}','{{$_perfusion->_class_name}}')">
             Lignes de substitution
            ({{$_perfusion->_count_substitution_lines}})
            </button>
        {{/if}}
        
        {{if $full_line_guid == $_perfusion->_guid}}
		      <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '');"></button>
		    {{/if}}
      </div>
			 <div style="float: right">
      <!-- Formulaire de signature du praticien -->
      {{if $_perfusion->_can_vw_form_signature_praticien}}
			  <form name="validation-{{$_perfusion->_class_name}}-{{$_perfusion->_id}}" action="" method="post">
			    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
			    <input type="hidden" name="m" value="dPprescription" />
			    <input type="hidden" name="prescription_line_guid" value="{{$_perfusion->_guid}}" />
			    <input type="hidden" name="prescription_reelle_id" value="{{$prescription_reelle->_id}}" />
			    <input type="hidden" name="mode_pharma" value="0" />
			    {{if $_perfusion->signature_prat}}
			    <input type="hidden" name="annulation" value="1" />
			    {{/if}}
			    <button type="button" class="{{if $_perfusion->signature_prat}}cancel{{else}}tick{{/if}}" onclick="submitFormAjax(this.form,'systemMsg');">{{if $_perfusion->signature_prat}}Annuler signature{{else}}Signer{{/if}}</button>
			  </form>
      {{/if}}
     
		    <!-- Signature pharmacien -->
        {{if $_perfusion->_can_vw_form_signature_pharmacien}}
      		{{if $_perfusion->signature_pharma}}
					  <button type="button" class="cancel" onclick="submitSignaturePharmacien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','0')">Annuler la validation pharmacien</button>
					{{else}}
					  <button type="button" class="tick" onclick="submitSignaturePharmacien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','1')">Validation pharmacien</button>
					{{/if}}
			  {{/if}}
		  </div>
			
		  <!-- Accord du praticien -->
			{{if $mode_pharma}}
				<div style="float: right">
					{{if !$_perfusion->signature_pharma}}
						<form action="?" method="post" name="editLineAccordPraticien-{{$_perfusion->_id}}">
						  <input type="hidden" name="m" value="dPprescription" />
						  <input type="hidden" name="dosql" value="do_perfusion_aed" />
						  <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
						  <input type="hidden" name="del" value="0" />
						  {{mb_field object=$_perfusion field="accord_praticien" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
						  {{mb_label object=$_perfusion field="accord_praticien"}}
						</form> 
					{{elseif $_perfusion->accord_praticien}}
					  En accord avec le praticien
					{{/if}}
				</div>
			{{/if}}
      <!-- Siganture du praticien -->
      {{if $_perfusion->_can_vw_signature_praticien}}
        <div class="mediuser" style="float: right; border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}};">	
          {{$_perfusion->_ref_praticien->_view}}
					{{if $_perfusion->signature_prat}}
					   <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
					{{else}}
					   <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
						{{if $_perfusion->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
        </div>
      {{/if}}
			<strong>
				Perfusion :
				{{foreach from=$_perfusion->_ref_lines item=_line name=perf_line}}
				 {{$_line->_ucd_view}} 
				  {{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td colspan="8">
      <form name="editPerf-{{$_perfusion->_id}}" action="" method="post">
	      <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_perfusion_aed" />
        <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            <td style="border:none;" rowspan="2">
              {{if $_perfusion->_can_delete_perfusion}}
	              <button type="button" class="trash notext" onclick="$V(this.form.del,'1'); return onSubmitFormAjax(this.form, { 
	                onComplete: function(){
             		    {{if @$mode_substitution}}
					  		      Prescription.viewSubstitutionLines('{{$_perfusion->substitute_for_id}}','{{$_perfusion->substitute_for_class}}');
					  		    {{else}}
					  			    Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
                    {{/if}}
			            }        
	              } );"></button>
              {{/if}}
            </td>
            <td style="border: none;">
			        Type : 
			        {{* if $_perfusion->_ref_lines|@count || !$_perfusion->_can_modify_perfusion*}}
				        {{* mb_value object=$_perfusion field="type"*}}
				      {{* else*}}
							{{if $_perfusion->_perm_edit}}
				        {{mb_field object=$_perfusion field="type" onchange="if(this.value == 'PCA'){ $('bolus-$perfusion_id').show(); changeModeBolus(this.form);} else { resetBolus(this.form); $('bolus-$perfusion_id').hide(); }; return onSubmitFormAjax(this.form);"}}
				      {{else}}
							  {{mb_value object=$_perfusion field="type"}}
              {{/if}}
							{{* /if*}}
				    </td>
						
						{{if $_perfusion->_protocole}}
              <td style="border:none;">
              <script type="text/javascript">
                            
                Main.add( function(){
                  var oForm = document.forms["editPerf-{{$_perfusion->_id}}"];
                  togglePerfDecalage(oForm);
                } );
                
              </script>
              {{mb_label object=$_perfusion field="date_debut"}}
                à {{mb_field object=$_perfusion field="jour_decalage" onchange="togglePerfDecalage(this.form); return onSubmitFormAjax(this.form);"}} 
                <span id="decalage_interv-{{$_perfusion->_id}}">{{mb_field object=$_perfusion field=decalage_interv showPlus="1" increment=1 size="2" form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}} h</span>
              </td>
            {{else}}
              <td style="border:none;">
                {{mb_label object=$_perfusion field="date_debut"}}
                {{if $_perfusion->_can_modify_perfusion}}
                  {{mb_field object=$_perfusion field=date_debut form="editPerf-$perfusion_id" onchange="changeColorPerf($perfusion_id,this.form); return onSubmitFormAjax(this.form);" register=true}}
                  {{mb_field object=$_perfusion field=time_debut form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);" register=true}}
                {{else}}
                  {{mb_value object=$_perfusion field=date_debut}}
                  {{mb_value object=$_perfusion field=time_debut}}
                {{/if}} 
              </td>       
            {{/if}} 
						
						<td style="border:none;">
             {{mb_label object=$_perfusion field=duree}}
             {{if $_perfusion->_can_modify_perfusion}}
               {{mb_field object=$_perfusion field=duree size=1 increment=1 min=0 form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}}heures
             {{else}}
               {{mb_value object=$_perfusion field=duree}}heures
             {{/if}}    
            </td>
						
						<td style="border:none;">
             {{mb_title object=$_perfusion field=quantite_totale}}
             {{if $_perfusion->_can_modify_perfusion}}
               {{mb_field object=$_perfusion field=quantite_totale size=1 increment=1 min=0 form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($perfusion_id) } );"}} ml
             {{else}}
               {{mb_value object=$_perfusion field=quantite_totale}} ml
             {{/if}}    
            </td>
					
				    <td style="border:none;">
				      {{if $_perfusion->_perm_edit}}
				        {{if $_perfusion->_voies}}
					        <select name="voie" onchange="{{if !in_array($_perfusion->voie, $_perfusion->_voies)}}
					                                        Element.hide($('warning_voie_{{$_perfusion->_id}}')); 
					                                        Element.hide($('last_option_{{$_perfusion->_id}}'));
					                                      {{/if}}
					                                      submitFormAjax(this.form, 'systemMsg');">
					          {{foreach from=$_perfusion->_voies item=_voie}}
					            <option value="{{$_voie}}" {{if $_perfusion->voie == $_voie}}selected="selected"{{/if}}>{{$_voie}}</option>
					          {{/foreach}}
					          {{if !in_array($_perfusion->voie, $_perfusion->_voies)}}
					            <option value="{{$_perfusion->voie}}" selected="selected" id="last_option_{{$_perfusion->_id}}">{{$_perfusion->voie}}</option>
					          {{/if}}
					        </select>
					         {{if !in_array($_perfusion->voie, $_perfusion->_voies)}}
					         <div class="warning" id="warning_voie_{{$_perfusion->_id}}">
					           Attention, la voie selectionnée n'est plus disponible
					         </div>
					         {{/if}}
				        {{/if}}
				      {{else}}
				        <strong>{{mb_value object=$_perfusion field="voie"}}</strong>
              {{/if}}
	          </td>
				    <td style="border:none;">
				      {{if !$_perfusion->_protocole && $_perfusion->signature_prat}}
					       <button type="button" class="new" onclick="$V(this.form._add_perf_contigue, '1');
					                                                  return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
					          } } );">Faire évoluer</button>
					        <input type="hidden" name="_add_perf_contigue" value="" />
								  <input type="hidden" name="date_arret" value="{{$_perfusion->date_arret}}" />
								  <input type="hidden" name="time_arret" value="{{$_perfusion->time_arret}}" />
						
					        {{if $_perfusion->date_arret}}
					          <button type="button" class="cancel" onclick="this.form.date_arret.value=''; this.form.time_arret.value=''; return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
					          } } );">Annuler l'arrêt</button>
					          <br />
					          {{mb_value object=$_perfusion field=date_arret}} à {{mb_value object=$_perfusion field=time_arret}}
					        {{else}}
					          <button type="button" class="stop" onclick="this.form.date_arret.value='current';this.form.time_arret.value='current'; return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
					          } } );">Arrêter</button>
					        {{/if}}
				      {{/if}}
				    </td>   
					</tr>
					<tr>
					  <td style="border: none;"></td>
            <td style="border:none;">
					    <label>
						  <input type="radio" {{if !$_perfusion->_perm_edit}}disabled="disabled"{{/if}} {{if $_perfusion->_continuite != 'discontinue'}}checked="checked"{{/if}} name="continuite_perf" onchange="toggleContinuitePerf(this, '{{$_perfusion->_id}}');" value="continue"/> 
							Continue
							</label>
              
							<label>
							<input type="radio" {{if !$_perfusion->_perm_edit}}disabled="disabled"{{/if}} {{if $_perfusion->_continuite == 'discontinue'}}checked="checked"{{/if}} name="continuite_perf" onchange="toggleContinuitePerf(this, '{{$_perfusion->_id}}');" value="discontinue"/>
							Discontinue		
					    </label>
						</td>
						<td style="border:none;" colspan="2">
					    <div style="display: none;" id="continue-{{$_perfusion->_id}}">
	              Débit
	              {{if $_perfusion->_can_modify_perfusion}}
	                {{mb_field object=$_perfusion field="vitesse" size="3" increment=1 min=0 form="editPerf-$perfusion_id" onchange="this.form.nb_tous_les.value = ''; this.form.duree_passage.value = ''; return onSubmitFormAjax(this.form);"}} ml/h
	              {{else}}
	                {{mb_value object=$_perfusion field="vitesse"}} ml/h
	              {{/if}}
							</div>
							<div style="display: none;" id="discontinue-{{$_perfusion->_id}}">
	              {{if $_perfusion->_can_modify_perfusion}}
                  A passer en 
                 {{mb_field object=$_perfusion size=2 field=duree_passage increment=1 min=0 form="editPerf-$perfusion_id" onchange="this.form.vitesse.value = ''; return onSubmitFormAjax(this.form);"}} minutes
	              {{elseif $_perfusion->duree_passage}}
								  A passer en 
                 {{mb_value object=$_perfusion field=duree_passage}} minutes
                {{/if}}
								 toutes les 
	              {{if $_perfusion->_can_modify_perfusion}}
	                {{mb_field object=$_perfusion field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$perfusion_id" onchange="this.form.vitesse.value = ''; return onSubmitFormAjax(this.form);"}} heures
	              {{else}}      
	                {{mb_value object=$_perfusion field="nb_tous_les"}} heures
	              {{/if}}
							</div>
            </td>
          </tr>
				  <tr id="bolus-{{$_perfusion->_id}}" style="display: none;">
				    <td style="border: none;" />
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="mode_bolus"}}:
							{{mb_field object=$_perfusion field="mode_bolus" onchange="changeModeBolus(this.form); return onSubmitFormAjax(this.form);"}}
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="dose_bolus"}}:
							{{mb_field object=$_perfusion field="dose_bolus" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$perfusion_id"}} mg
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="periode_interdite"}}:
							{{mb_field object=$_perfusion field="periode_interdite" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$perfusion_id"}} min
						</td>
				  </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="9">
      <table class="form group" id=lines-{{$_perfusion->_id}}>
	      {{foreach from=$_perfusion->_ref_lines item=line}}
	        {{assign var=line_id value=$line->_id}}
	        <tr>
	          <td style="border:none;">
	            <form name="editLinePerf-{{$line->_id}}" action="" method="post">
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="dosql" value="do_perfusion_line_aed" />
	              <input type="hidden" name="perfusion_line_id" value="{{$line->_id}}" />
	              <input type="hidden" name="del" value="0" />
	              <table class="form">
		              <tr>
		                <td style="border:none; width:1%;">
		                  {{if $_perfusion->_can_delete_perfusion_line}}
			                  <button class="trash notext" type="button" onclick="$V(this.form.del,'1'); submitFormAjax(this.form, 'systemMsg', { 
			                    onComplete: function(){
			                    	{{if @$mode_substitution}}
									  		      Prescription.viewSubstitutionLines('{{$_perfusion->substitute_for_id}}','{{$_perfusion->substitute_for_class}}');
									  		    {{else}}
									  			    Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$line->_protocole}}','{{$mode_pharma}}');
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
					             	{{if $_perfusion->_can_modify_perfusion_line}}
												<label>
												  {{mb_field object=$line field="solvant" typeEnum=checkbox 
													           onchange="removeSolvant(this.form.__solvant); return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($perfusion_id)} );"}} 
													Solvant
											  </label>
												{{/if}}
                        </span>
											{{mb_label object=$line field=quantite}}:
					            {{if $_perfusion->_can_modify_perfusion_line}}
												{{mb_field object=$line field=quantite size=4 increment=1 min=0 form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry($perfusion_id, $line_id) });"}}
						            <select name="unite" style="width: 12em;" onchange="return onSubmitFormAjax(this.form, { onComplete: updateSolvant.curry('{{$line->perfusion_id}}','{{$line->_id}}') });">
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
												{{if $line->solvant}}
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
            <td colspan="20"><div class="small-info">Aucun produit n'est associé à la perfusion</div></td>
          </tr>
	      {{/foreach}}
      </table>
    </td>
  </tr>
  {{if $_perfusion->_perm_edit || $_perfusion->commentaire}}
  <tr>
    <td colspan="9">
    	
			<form name="commentaire-{{$_perfusion->_guid}}">
        {{mb_label object=$_perfusion field="commentaire" size=60}}: 
	      	{{if $_perfusion->_protocole}}
	      	  {{assign var=_line_praticien_id value=$app->user_id}}
		      {{else}}
		        {{assign var=_line_praticien_id value=$_perfusion->praticien_id}}
		      {{/if}}
		      {{if $_perfusion->_perm_edit}}
		      
		      <input type="text" name="commentaire" value="{{$_perfusion->commentaire}}" size=60 onchange="{{if $_perfusion->substitute_for_id && !$_perfusion->substitution_active}}submitEditPerfCommentaireSubst('{{$_perfusion->_id}}',this.value);{{else}}submitAddComment('{{$_perfusion->_class_name}}', '{{$_perfusion->_id}}', this.value);{{/if}}" />
		      <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this); this.form.commentaire.onchange();" style="width: 110px;">
		        <option value="">&mdash; Aide</option>
		        {{html_options options=$aides_prescription.$_line_praticien_id.CPerfusion}}
		      </select>
		      <input type="hidden" name="_hidden_commentaire" value="" />
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPerfusion', this.form._hidden_commentaire, 'commentaire');">
		        Nouveau
		      </button>
		      {{else}}
		          {{mb_value object=$_perfusion field="commentaire"}}
		      {{/if}}
	    </form>
			{{if !$_perfusion->date_pose && $_perfusion->_ref_prescription->object_id && 
           (($_perfusion->_ref_substitution_lines.CPrescriptionLineMedicament|@count) || ($_perfusion->_ref_substitution_lines.CPerfusion|@count))}} 
          <form name="changeLine-{{$_perfusion->_guid}}" action="?" method="post">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_substitution_line_aed" />
            <select name="object_guid" style="width: 150px;" 
                    onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
                                 Prescription.reload('{{$_perfusion->_ref_prescription->_id}}', '', 'medicament');} } )">
              <option value="">Lignes de substitutions</option>
              {{foreach from=$_perfusion->_ref_substitution_lines item=lines_subst_by_chap}}
                  {{foreach from=$lines_subst_by_chap item=_line_subst}}
                  <option value="{{$_line_subst->_guid}}">
                     {{if $_line_subst->_class_name == "CPerfusion"}}
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