{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $_perfusion->type == "PCA"}}
<script type="text/javascript">
Main.add( function(){
  $("bolus-{{$_perfusion->_id}}").show();
  changeModeBolus(document.forms['editPerf-{{$_perfusion->_id}}']);
} );
</script>
{{/if}}

<table class="tbl {{if ($full_line_guid == $_perfusion->_guid) && $readonly}}active{{/if}}" id="perfusion-{{$_perfusion->_id}}"> 
<tbody class="hoverable {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}line_stopped{{/if}}">
{{assign var=perfusion_id value=$_perfusion->_id}}
  <tr>
    <th colspan="9" id="th-perf-{{$_perfusion->_id}}" class="text element {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
      <div style="float: left">
        {{if $prescription->type != "externe" && $_perfusion->_perm_edit}}
	        <form name="editEmplacement" method="post" action="?">
	          <input type="hidden" name="m" value="dPprescription" />
	          <input type="hidden" name="dosql" value="do_perfusion_aed" />
	          <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
	          {{mb_field object=$_perfusion field=emplacement onchange="submitFormAjax(this.form, 'systemMsg');"}}
	        </form>
	      {{else}}
	        {{mb_value object=$_perfusion field=emplacement}}
	      {{/if}}
        {{if $_perfusion->_ref_parent_line->_id}}
	        {{assign var=parent_perf value=$_perfusion->_ref_parent_line}}
	        <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
	             class="tooltip-trigger" 
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
        
        {{if ($full_line_guid == $_perfusion->_guid) && $readonly}}
		      <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, {{$readonly}}, {{$lite}},'');"></button>
		    {{/if}}
      </div>
      <!-- Formulaire de signature du praticien -->
      {{if $_perfusion->_can_vw_form_signature_praticien}}
			  <div style="float: right">
					{{if $_perfusion->signature_prat}}
					  <button type="button" class="cancel" onclick="submitSignaturePraticien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','0')">Annuler la signature</button>
					{{else}}
					  <button type="button" class="tick" onclick="submitSignaturePraticien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','1')">Signer</button>
					{{/if}}
				</div>
      {{/if}}
      <div style="float: right">
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
					   <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
					{{else}}
					   <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
						{{if $_perfusion->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
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
            <td style="border:none;">
			        {{mb_label object=$_perfusion field="type"}} : 
			        {{if $_perfusion->_ref_lines|@count || !$_perfusion->_can_modify_perfusion}}
				        {{mb_value object=$_perfusion field="type"}}
				      {{else}}
				        {{mb_field object=$_perfusion field="type" onchange="if(this.value == 'PCA'){ $('bolus-$perfusion_id').show(); changeModeBolus(this.form);} else { resetBolus(this.form); $('bolus-$perfusion_id').hide(); }; return onSubmitFormAjax(this.form);"}}
				      {{/if}}
				    </td>
				    <td style="border:none;">
				      {{mb_label object=$_perfusion field="vitesse"}}
				      {{if $_perfusion->_can_modify_perfusion}}
				        {{mb_field object=$_perfusion field="vitesse" size="3" increment=1 min=0 form="editPerf-$perfusion_id" onchange="this.form.nb_tous_les.value = ''; return onSubmitFormAjax(this.form);"}} ml/h
				      {{else}}
				        {{mb_value object=$_perfusion field="vitesse"}} ml/h
				      {{/if}}
				      / toutes les 
					    {{if $_perfusion->_can_modify_perfusion}}
						    {{mb_field object=$_perfusion field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$perfusion_id" onchange="this.form.vitesse.value = ''; return onSubmitFormAjax(this.form);"}} heures
						  {{else}}      
						    {{mb_value object=$_perfusion field="nb_tous_les"}}
				      {{/if}} 
				    </td>
				    <td style="border:none;">
	            <strong>{{mb_value object=$_perfusion field="voie"}}</strong>
            </td>
				    <td style="border:none;">
				      {{if !$_perfusion->_protocole && $_perfusion->signature_prat && ($is_praticien || @$operation_id || $can->admin)}}
					      <!-- Modification de la ligne -->
					      {{if $_perfusion->_can_vw_form_add_perf_contigue}}
					        <button type="button" class="new" onclick="$V(this.form._add_perf_contigue, '1');
					                                                      return onSubmitFormAjax(this.form, { onComplete: function(){ 
					            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
					          } } );">Faire évoluer</button>
					        <input type="hidden" name="_add_perf_contigue" value="" />
					      {{/if}}
								<input type="hidden" name="date_arret" value="{{$_perfusion->date_arret}}" />
								<input type="hidden" name="time_arret" value="{{$_perfusion->time_arret}}" />
							  <!-- Arret de ligne -->
					      {{if $_perfusion->_can_vw_form_stop_perf}}
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
				      {{/if}}
				    </td>   
					</tr>
					<tr>
            {{if $_perfusion->_protocole}}
						  <td style="border:none;">
						  {{mb_label object=$_perfusion field="date_debut"}}
						    à I {{mb_field object=$_perfusion field=decalage_interv showPlus="1" increment=1 size="2" form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}} h
						  </td>
						{{else}}  
	        		<td class="date"  style="border:none;">
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
          </tr>
				  <tr id="bolus-{{$_perfusion->_id}}" style="display: none;">
				    <td style="border: none;" />
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="mode_bolus"}}:
							{{mb_field object=$_perfusion field="mode_bolus" onchange="changeModeBolus(this.form); return onSubmitFormAjax(this.form);"}}
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="dose_bolus"}}:
							{{mb_field object=$_perfusion field="dose_bolus" onchange="return onSubmitFormAjax(this.form);" size="3"}} mg
				    </td>
				    <td style="border: none;">
				    	{{mb_label object=$_perfusion field="periode_interdite"}}:
							{{mb_field object=$_perfusion field="periode_interdite" onchange="return onSubmitFormAjax(this.form);" size="3"}} min
						</td>
				  </tr>
        </table>
      </form>
    </td>
    <td>
	  {{if !$_perfusion->date_pose && $_perfusion->_ref_prescription->object_id}} 
	  	 {{if ($_perfusion->_ref_substitution_lines.CPrescriptionLineMedicament|@count) || ($_perfusion->_ref_substitution_lines.CPerfusion|@count)}}
			    <form name="changeLine-{{$_perfusion->_guid}}" action="?" method="post">
			      <input type="hidden" name="m" value="dPprescription" />
			      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
			      <select name="object_guid" style="width: 75px;" 
			              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
			                           Prescription.reload('{{$_perfusion->_ref_prescription->_id}}', '', 'medicament');} } )">
			        <option value="">Conserver</option>
				      {{foreach from=$_perfusion->_ref_substitution_lines item=lines_subst_by_chap}}
				          {{foreach from=$lines_subst_by_chap item=_line_subst}}
				          <option value="{{$_line_subst->_guid}}">{{$_line_subst->_view}}{{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
				        {{/foreach}}
				      {{/foreach}}
			      </select>
			    </form>
			    <br />
			
			    {{if $_perfusion->_ref_substitute_for->_class_name == "CPrescriptionLineMedicament"}}
					  {{assign var=dosql value="do_prescription_line_medicament_aed"}}
					{{else}}
					  {{assign var=dosql value="do_perfusion_aed"}}
					{{/if}}
					{{if $prescription->type == "sejour"}}
			       Modif. infirmière
			      <form name="editLine" action="?" method="post">
						  <input type="hidden" name="m" value="dPprescription" />
						  <input type="hidden" name="dosql" value="{{$dosql}}" />
							<input type="hidden" name="{{$_perfusion->_ref_substitute_for->_spec->key}}" value="{{$_perfusion->_ref_substitute_for->_id}}" />
							{{mb_field object=$_perfusion->_ref_substitute_for field="substitution_plan_soin" onchange="submitFormAjax(this.form, 'systemMsg')"}}
						</form>
					{{/if}}
	    {{/if}}
	  {{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="9">
      <table class="form">
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
									      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
									    {{/if}}  
									    {{if !$line->_ref_produit->inT2A}}
								        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
								      {{/if}}
									    {{if $line->_can_vw_generique}}
									      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
									    {{/if}} 
                      {{if $line->_ref_produit->_supprime}}
                        <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
                      {{/if}}
					            <strong>{{$line->_ucd_view}}
					                <span style="font-size: 0.8em; opacity: 0.7">({{$_line->_forme_galenique}})</span>
					             </strong>
					          </td>
			              <td style="border:none; width: 30%;">
					            {{mb_label object=$line field=quantite}}:
					            {{if $_perfusion->_can_modify_perfusion_line}}
					              {{mb_field object=$line field=quantite size=4 increment=1 min=0 form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form);"}}
						            <select name="unite" style="width: 75px;" onchange="return onSubmitFormAjax(this.form);">
											    {{foreach from=$line->_unites_prise item=_unite}}
											      <option value="{{$_unite}}" {{if $line->unite == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
											    {{/foreach}}
											  </select>
											{{else}}
					              {{mb_value object=$line field=quantite}}
					              {{mb_value object=$line field=unite}}			            
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
		        <option value="">&mdash; Choisir une aide</option>
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
    </td>
  </tr>
  {{/if}}
</tbody>
</table>