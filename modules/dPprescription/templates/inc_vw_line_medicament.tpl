{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=dosql value="do_prescription_line_medicament_aed"}}

{{if $line->inscription}}
  {{assign var=div_refresh value="inscription"}}
{{else}}
  {{assign var=div_refresh value="medicament"}}
{{/if}}

{{assign var=typeDate value="Med"}}

{{mb_include module="dPprescription" template="inc_header_line"}}

<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}" id="full_line_medicament_{{$line->_id}}">	
  <tr>
    <th colspan="2" class="text category {{if $line->traitement_personnel}}traitement{{/if}} {{if $line->perop}}perop{{/if}}">
      
      <div style="float:left;">
        <a title="Historique" class="button list notext" href="#1"
           onclick="Prescription.showLineHistory('{{$line->_guid}}')" 
           {{if !$line->inscription && $line->_ref_parent_line->_id}}
           onmouseover="ObjectTooltip.createEx(this, '{{$line->_ref_parent_line->_guid}}')"
           {{/if}}>
        </a>
    
        {{if $line->_can_select_equivalent}}
          {{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
        {{/if}}	       
        {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
				
				{{if $line->_perm_edit}}
			    <input name="perop" type="checkbox" {{if $line->perop}}checked="checked"{{/if}} onchange="submitPerop('{{$line->_class_name}}','{{$line->_id}}',this.checked)"  />
			    {{mb_label object=$line field="perop"}}
			  {{elseif !$line->_protocole}}
			    {{mb_label object=$line field="perop"}}:
			    {{if $line->perop}}Oui{{else}}Non{{/if}} 
			  {{/if}}
				
		    {{include file="../../dPprescription/templates/line/inc_vw_form_conditionnel.tpl"}}
        {{if $line->_can_vw_form_traitement}} 
          {{include file="../../dPprescription/templates/line/inc_vw_form_traitement.tpl"}}
        {{/if}} 
				<br />
				<div style="float: left">
				{{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl"}}
				</div>
				
        {{if !($line->_protocole && $line->substitute_for_id)}}
	        {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	      {{/if}}
      </div>
      
      <!-- Affichage de la signature du praticien -->
      <div class="mediuser" style="float: right; {{if !$line->_protocole}}border-color: #{{$line->_ref_praticien->_ref_function->color}};{{/if}}">
        {{if $line->_can_view_signature_praticien}}
            {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
            {{if !$mode_pharma}}
              {{if $prescription->type == "sejour"}}
		            {{if $line->valide_pharma}}
								  <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
								{{else}}
									<img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
								{{/if}}
							{{/if}}
						{{/if}}
        {{else}}
          {{if !$line->traitement_personnel && !$line->_protocole}}
            {{$line->_ref_praticien->_view}}    
          {{/if}}
        {{/if}}
      
        {{if $line->_perm_edit && $line->_protocole && !$line->substitute_for_id && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$line->_id}}','{{$line->_class_name}}')">
             Variantes
            ({{$line->_count_substitution_lines}})
          </button>
        {{/if}}
        
        <button class="lock notext" onclick="modalPrescription.close(); 
  				{{if @$mode_substitution}}
  				 Prescription.viewSubstitutionLines('{{$line->substitute_for_id}}','{{$line->substitute_for_class}}');
  				{{else}}
  				 Prescription.reload('{{$prescription->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, '');
  				{{/if}}">
				</button>
      </div>
      
      {{mb_include module=forms template=inc_widget_ex_class_register object=$line event=prescription cssStyle="float: right;"}}
      
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');">
        <strong style="font-size: 1.5em;">
          {{$line->_ucd_view}}
        </strong>
        {{if $line->_forme_galenique}}({{$line->_forme_galenique}}){{/if}}
      </a>
			
			<!-- Si seulement 1 voie possible ou affichage bloqué-->
      {{if !$line->_perm_edit}}
        {{if $line->voie}}
          {{$line->voie}}
        {{elseif $line->_ref_produit_prescription->voie}}
           {{$line->_ref_produit_prescription->voie}}
        {{/if}}
      {{else}}
        <select name="voie-{{$line->_id}}" 
                onchange="{{if $line->voie && !in_array($line->voie, $line->_ref_produit->voies) && $line->voie != 'none'}}
                            $('warning-voie-{{$line->_id}}').hide();
                            $('warning-voie-option-{{$line->_id}}').hide();
                          {{/if}}
                          return submitVoie('{{$line->_id}}',this.value);">
            <option value="none">Voie non définie</option>
          {{foreach from=$line->_ref_produit->voies item=libelle_voie}}
            <option value="{{$libelle_voie}}" {{if $libelle_voie == $line->voie}}selected="selected"{{/if}}>{{$libelle_voie}}</option>
          {{/foreach}}
          
          {{if $line->voie && !in_array($line->voie, $line->_ref_produit->voies) && $line->voie != "none"}}
            <script type="text/javascript">
              $('warning-voie-{{$line->_id}}').show();
            </script>
            <option id="warning-voie-option-{{$line->_id}}" value="{{$line->voie}}" selected="selected" style="background-color: red">
             {{$line->voie}}
            </option>
          {{/if}}
        </select>
      {{/if}}
      {{if $line->voie && !in_array($line->voie, $line->_ref_produit->voies)}}
      <div id="warning-voie-{{$line->_id}}" class="small-warning" style="display:none;">
        Attention, la voie <strong>"{{$line->voie}}"</strong> n'est plus proposée pour ce médicament
      </div>
      {{/if}}
    </th>
  </tr>
	</table>
	
	<table class="main layout">
	<!-- Association d'un produit à une perfusion-->
  {{if $line->_is_perfusable && $line->_perm_edit && !$line->inscription}}
	  <tr>
	    <td>
	    	<fieldset>
	    		<legend>
	    			Associer ce produit à une perfusion
	    		</legend>
	    		<form name="addPerfusionLine-{{$line->_id}}" action="?" method="post">
		  		  <input type="hidden" name="dosql" value="do_line_to_prescription_line_mix_aed" />
		  		  <input type="hidden" name="m" value="dPprescription" />
		  		  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		  		  <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}" />
	
		  		  <input type="hidden" name="substitute_for_id" value="{{$line->substitute_for_id}}" />
		  		  <input type="hidden" name="substitute_for_class" value="{{$line->substitute_for_class}}" />
	          <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
						
		  		  <select name="prescription_line_mix_id" onchange="toggleTypePerfusion(this.form);" style="width: 150px;">
		  		    <option value="">Nouvelle perfusion</option>
			  		  {{if $line->substitute_for_id}}
				  		  {{foreach from=$prescription->_ref_prescription_line_mixes item=_prescription_line_mix}}
				  		    {{if ($line->substitute_for_id == $_prescription_line_mix->substitute_for_id) && ($line->substitute_for_class == $_prescription_line_mix->substitute_for_class)}}
				  		    <option value="{{$_prescription_line_mix->_id}}">
				  		    {{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name=foreach_prescription_line_mix}}
									  {{$_perf_line->_ref_produit->libelle_abrege}}
										{{if !$smarty.foreach.foreach_prescription_line_mix.last}}, {{/if}}
									{{/foreach}}
									({{$_prescription_line_mix->voie}} - {{tr}}CPrescriptionLineMix.type.{{$_prescription_line_mix->type}}{{/tr}})
		              </option>
				  		    {{/if}}
				  		  {{/foreach}}
			  		  {{else}}
		            {{if isset($prescription->_ref_prescription_line_mixes_by_type.perfusion|smarty:nodefaults)}}
		  		  		  {{foreach from=$prescription->_ref_prescription_line_mixes_by_type.perfusion item=_prescription_line_mix}}
		  		  		    <option value="{{$_prescription_line_mix->_id}}">
		  		  		    	{{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name="foreach_prescription_line_mix"}}
		                  {{$_perf_line->_ref_produit->libelle_abrege}}
		  								{{if !$smarty.foreach.foreach_prescription_line_mix.last}}, {{/if}}
		                {{/foreach}}
		  						  ({{$_prescription_line_mix->voie}} - {{tr}}CPrescriptionLineMix.type.{{$_prescription_line_mix->type}}{{/tr}})
		                </option>
		  		  		  {{/foreach}}
		            {{/if}}
			  		  {{/if}}
		  		  </select>
						
					 {{assign var=types value="CPrescriptionLineMix"|static:"type_by_line"}}
	         {{assign var=types_for_line value=$types.perfusion}}
	          
						<select name="type">           
						  <option value="">&mdash; Type</option>               
		          {{foreach from=$types_for_line item=_type}}
		            <option value="{{$_type}}">{{tr}}CPrescriptionLineMix.type.{{$_type}}{{/tr}}</option> 
		          {{/foreach}}
	          </select>
						
			  		<button class="add" type="button"
	            onclick="if ($V(this.form.type) == '' && !$V(this.form.prescription_line_mix_id)) {
	               alert('{{tr}}CPrescription-alert-no-type{{/tr}}');
	               return;
	            }
	            onSubmitFormAjax(this.form, { 
			  		  onComplete: function() { 
			  		    {{if @$mode_substitution}}
			  		      Prescription.viewSubstitutionLines('{{$line->substitute_for_id}}','{{$line->substitute_for_class}}');
			  			  {{/if}}
			  		  } } );">
			  		  Ajouter à la perfusion
			  		</button>
		  		</form>
		  		<script type="text/javascript">
						Main.add( function(){
						  toggleTypePerfusion(document.forms["addPerfusionLine-{{$line->_id}}"]);
						} );
		  		</script>	
					
					{{if $line->_ref_prescription->type != "sejour"}}
	          <form name="editLineInjectable" method="post" action="">
	            <input type="hidden" name="m" value="dPprescription" />
	            <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	            <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}" />
	            {{mb_label object=$line field="injection_ide"}}
	            {{mb_field object=$line field="injection_ide" onchange="onSubmitFormAjax(this.form);"}}
	          </form>
	         {{/if}}
				</fieldset>	
	    </td>
	  </tr>
  {{/if}}
  
  <!-- Dates -->
  <tr>
    <td>
    	<fieldset>
    		<legend>Durée de la prescription</legend>
    	  {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}} 
			</fieldset> 
      <script type="text/javascript">
        var oForm;
	      if(oForm = getForm("editDates-Med-{{$line->_id}}", true)){
		      Calendar.regField(oForm.debut, dates);
		      Calendar.regField(oForm._fin, dates);
		      Calendar.regField(oForm.fin, dates);
	      }
      </script>
	  </td>
  </tr>
  
	<!-- Posologies -->
  <tr>  
	  <td class="text">		
			{{if $line->_can_modify_poso}}
			  <fieldset style="float: left; width: 48%;">
			  	<legend>
			  		Choix d'une posologie
					</legend>
	        <!-- Ajout de posologies -->             
	        {{if $line->_unites_prise|@count}}
	          {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Med"}}   
	        {{else}}
	          <div class="small-warning">
	            Ce produit ne contient les informations nécessaires pour pouvoir gérer des posologies.
	          </div>    
	        {{/if}}
				</fieldset>		
			{{/if}}

			<fieldset {{if $line->_can_modify_poso}}style="float: right; width: 48%;"{{/if}}>
        <legend>
          Posologie selectionnée
        </legend>
	      {{if $line->_can_modify_poso}}
	        <!-- Affichage des prises (modifiables) -->
	        <div id="prises-Med{{$line->_id}}">
	          {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Med"}}
	        </div>
	      {{else}}
	        <!-- Affichage des prises (non modifiables) -->
	        {{if $line->_ref_prises|@count}}
	        <ul>
	        {{foreach from=$line->_ref_prises item=prise}}
	          {{if $prise->quantite}}
	            <li>{{$prise->_view}}</li> 
	          {{/if}}
	        {{/foreach}}
	        </ul>
	        {{else}}
	          Aucune posologie
	        {{/if}}
	      {{/if}}
      </fieldset>
    </td>
  </tr> 
	
	<!-- Commentaire -->   
  <tr>  
    <td class="text">
 	    {{if $line->_protocole}}
        {{assign var=_line_praticien_id value=$app->user_id}}
      {{else}}
        {{assign var=_line_praticien_id value=$line->praticien_id}}
      {{/if}}    
			
  		 <script type="text/javascript">
          Main.add( function(){
            var oFormCommentaireElement = getForm("editCommentaire-{{$line->_guid}}");
            if (!oFormCommentaireElement.commentaire) {
              return;
            }
            new AideSaisie.AutoComplete(oFormCommentaireElement.commentaire, {
              objectClass: "{{$line->_class_name}}", 
              contextUserId: "{{$_line_praticien_id}}",
              resetSearchField: false,
  						validateOnBlur: false
            });
          });
        </script>
  
      <form name="editCommentaire-{{$line->_guid}}" method="post" action="?" onsubmit="testPharma({{$line->_id}}); return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$line}}
        
				 {{if $line->_can_modify_comment || $line->commentaire}}
				   <fieldset>
             <legend>
               {{mb_label object=$line field="commentaire"}}
             </legend>
		         {{if $line->_can_modify_comment}}
					 	  {{mb_field object=$line field="commentaire" onblur="this.form.onsubmit();"}}
		         {{else}}
		           {{if $line->commentaire}}
		             {{mb_value object=$line field="commentaire"}}
		           {{/if}}
		         {{/if}}
					 </fieldset>
				 {{/if}}
	     </form>
 	  </td>
  </tr>
	
	<!-- Variantes -->
	{{if ($line->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $line->_ref_substitution_lines.CPrescriptionLineMix|@count) &&
            !$line->_count.administration && $line->_ref_prescription->object_id && $line->_perm_edit && !$line->_protocole}}
		<tr>
			<td>
				<fieldset>
					<legend>Variantes</legend>
	        <form action="?" method="post" name="changeLine-{{$line->_guid}}">
	          <input type="hidden" name="m" value="dPprescription" />
	          <input type="hidden" name="dosql" value="do_substitution_line_aed" />
	          <select name="object_guid" style="width: 150px;" 
	                  onchange="onSubmitFormAjax(this.form, { onComplete:   
	                               Prescription.reloadLine.curry(this.value)
	                              } )">
	            <option value="">Variantes</option>
	            {{foreach from=$line->_ref_substitution_lines item=lines_subst_by_chap}}
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
	      </fieldset>
			</td>	
		</tr>	
	{{/if}}
	
	<!-- Evolution et actions -->
	{{if !$line->_protocole}}
		<tr>
			<td>
			  {{if $line->_can_vw_form_add_line_contigue}}
	        <fieldset style="float: left; width: 48%;">
	          <legend>
	            Evolution
	          </legend>      
	          <span id="stop-CPrescriptionLineMedicament-{{$line->_id}}">
	            {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineMedicament"}}
	          </span>
	          {{include file="../../dPprescription/templates/line/inc_vw_form_add_line_contigue.tpl"}}
	        </fieldset>
	      {{/if}}
			 
			  {{if $line->_can_delete_line || $mode_pharma || ($line->signee && ($app->user_id == $line->praticien_id || $line->inscription) || !$line->signee)}}
				  <fieldset style="float: right; width: 48%; vertical-align: top;">
				  	<legend>Actions</legend>
					  <!-- Suppression de la ligne -->
				    {{if $line->_can_delete_line}}
						  {{if $line->inscription}}
							  {{assign var=chapitre value="inscription"}}
							{{else}}
						    {{assign var=chapitre value="medicament"}}
						  {{/if}}
				      <button type="button" class="trash"
				        onclick="
				          if (Prescription.confirmDelLine('{{$line->_view}}')) {
				            modalPrescription.close();
				            Prescription.delLine('{{$line->_id}}', '{{$chapitre}}');
				          }">
				       {{tr}}Delete{{/tr}}
				      </button>
				    {{/if}}
			      {{if $mode_pharma}}
			        <!-- Vue pharmacie -->
			        {{include file="../../dPprescription/templates/line/inc_vw_form_accord_praticien.tpl"}}
			        {{if $line->valide_pharma}}
			          <button type="button" class="cancel" onclick="submitValidationPharmacien('{{$prescription->_id}}', '{{$line->_id}}', '0', '{{$mode_pharma}}');">Annuler la validation pharmacien</button>
			        {{else}}
			          <button type="button" class="tick" onclick="submitValidationPharmacien('{{$prescription->_id}}', '{{$line->_id}}', '1', '{{$mode_pharma}}');">Validation pharmacien</button>
			        {{/if}}
			      {{elseif $line->_can_view_form_signature_praticien}}
			        {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
			      {{/if}}	
					</fieldset>
				{{/if}}
		  </td>
		</tr>
	{{/if}}
</table>