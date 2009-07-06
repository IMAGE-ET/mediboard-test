{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{assign var=line value=$curr_line}}
{{assign var=div_refresh value="medicament"}}
{{assign var=typeDate value="Med"}}
<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}
             {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}
             {{if ($full_line_guid == $line->_guid) && $readonly}}active{{/if}}"
       id="line_medicament_{{$line->_id}}">
<tbody  class="hoverable">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="text element {{if $line->traitement_personnel}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      
      {{if !$line->_protocole}}
      <script type="text/javascript">
         Main.add( function(){moveTbody($('line_medicament_{{$line->_id}}'));});
      </script>
      {{/if}}
      <div style="float:left;">
            <!-- Fomulaire de modification de l'emplacement -->
  		  {{include file="../../dPprescription/templates/line/inc_vw_form_emplacement.tpl"}}
        {{if $line->_ref_parent_line->_id}}
          {{assign var=parent_line value=$line->_ref_parent_line}}
          <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
               class="tooltip-trigger" 
               onmouseover="ObjectTooltip.createEx(this, '{{$parent_line->_guid}}')"/>
        {{/if}}
        <!-- Selecteur equivalent -->
        {{if $line->_can_select_equivalent}}
          {{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
        {{/if}}	       
        <!-- Formulaire ALD -->
        {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
        <!-- Formulaire Conditionnel -->
		    {{include file="../../dPprescription/templates/line/inc_vw_form_conditionnel.tpl"}}

	      <!-- Formulaire Traitement -->
        {{if $line->_can_vw_form_traitement}} 
          {{include file="../../dPprescription/templates/line/inc_vw_form_traitement.tpl"}}
        {{/if}} 
      </div>
      
      <!-- AFfichage de la signature du praticien -->
      <div class="mediuser" style="float: right; {{if !$line->_protocole}}border-color: #{{$line->_ref_praticien->_ref_function->color}};{{/if}}">
        {{if $line->_can_view_signature_praticien}}
            {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
            {{if !$mode_pharma}}
              {{if $prescription_reelle->type != "externe"}}
		            {{if $line->valide_pharma}}
								 <img src="images/icons/signature_pharma.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
								{{else}}
									 <img src="images/icons/signature_pharma_barre.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
								{{/if}}
							{{/if}}
						{{/if}}
        {{else}}
          {{if !$line->traitement_personnel && !$line->_protocole}}
            {{$line->_ref_praticien->_view}}    
          {{/if}}
        {{/if}}
        {{if $mode_pharma}}
        <!-- Vue pharmacie -->
          {{if !$line->_protocole}}
            {{include file="../../dPprescription/templates/line/inc_vw_form_accord_praticien.tpl"}}
            {{if $line->valide_pharma}}
              <button type="button" class="cancel" onclick="submitValidationPharmacien('{{$prescription_reelle->_id}}', '{{$line->_id}}', '0', '{{$mode_pharma}}');">Annuler la validation pharmacien</button>
            {{else}}
              <button type="button" class="tick" onclick="submitValidationPharmacien('{{$prescription_reelle->_id}}', '{{$line->_id}}', '1', '{{$mode_pharma}}');">Validation pharmacien</button>
            {{/if}}
          {{/if}}
        {{elseif !$line->_protocole}}
        <!-- Vue normale  -->
          {{if $line->traitement_personnel}}
            {{if $line->_can_view_form_signature_praticien}}
							  {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
					  {{/if}}
          {{else}}
					  {{if !$line->valide_pharma}}
						  {{if $line->_can_view_form_signature_praticien}}
							  {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
							{{/if}}
					  {{/if}}	
			    {{/if}}
        {{/if}}
        {{if $line->_protocole && !$line->substitute_for_id && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$line->_id}}','{{$line->_class_name}}')">
             Lignes de substitution
            ({{$line->_count_substitution_lines}})
            </button>
        {{/if}}
        {{if ($line->_guid == $full_line_guid) && $readonly}} 
          <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$readonly}}', '{{$lite}}','');"></button>
        {{/if}}
      </div>
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');">
        <strong style="font-size: 1.5em;">
          {{$line->_ucd_view}}
        </strong>
        ({{$line->_forme_galenique}})
      </a>
    </th>
  </tr>
  
  {{if $line->_is_perfusable && $line->_perm_edit}}
	  <tr>
	    <td />
	    <td>
	      <div class="small-info text">Ce <strong>produit est injectable</strong>, vous pouvez l'<em>associer à une perfusion</em> existante ou une nouvelle.</div>
	    </td>
	    <td>
	    		<form name="addPerfusionLine-{{$line->_id}}">
		  		  <input type="hidden" name="dosql" value="do_line_to_perfusion_aed" />
		  		  <input type="hidden" name="m" value="dPprescription" />
		  		  <input type="hidden" name="prescription_id" value="{{$prescription_reelle->_id}}" />
		  		  <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}" />

		  		  <input type="hidden" name="substitute_for_id" value="{{$line->substitute_for_id}}" />
		  		  <input type="hidden" name="substitute_for_class" value="{{$line->substitute_for_class}}" />

		  		  <select name="perfusion_id" onchange="toggleTypePerfusion(this.form);" style="width: 150px;">
		  		    <option value="">Nouvelle perfusion</option>
		  		  
		  		  {{if $line->substitute_for_id}}
			  		  {{foreach from=$prescription->_ref_perfusions item=_perfusion}}
			  		    {{if ($line->substitute_for_id == $_perfusion->substitute_for_id) && ($line->substitute_for_class == $_perfusion->substitute_for_class)}}
			  		    <option value="{{$_perfusion->_id}}">{{$_perfusion->_view}}</option>
			  		    {{/if}}
			  		  {{/foreach}}
		  		  {{else}}
			  		  {{foreach from=$prescription->_ref_perfusions item=_perfusion}}
			  		    <option value="{{$_perfusion->_id}}">{{$_perfusion->_view}}</option>
			  		  {{/foreach}}
		  		  {{/if}}
		  		  
		  		  </select>
		  		  {{mb_field object=$perfusion field="type" defaultOption="&mdash; Type"}}
		  		  
			  		<button class="add" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { 
			  		  onComplete: function() { 
			  		    {{if @$mode_substitution}}
			  		      Prescription.viewSubstitutionLines('{{$line->substitute_for_id}}','{{$line->substitute_for_class}}');
			  		    {{else}}
			  			    Prescription.reloadPrescPerf('{{$prescription_reelle->_id}}','{{$line->_protocole}}','{{$mode_pharma}}');
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
	    </td>
	  </tr>
  {{/if}}
  
  
  <!-- Pas traitement ni protocole -->
  <tr>
    <td style="text-align: center">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
      <br />
      {{/if}}
      {{if !$line->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
        <br />
      {{/if}}  
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      <br />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      <br />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
      {{/if}}
    </td>
    
    {{if !$line->_protocole}}
    <td colspan="3">
      {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}  
      <script type="text/javascript">
        var oForm;
	      if(oForm = getForm("editDates-Med-{{$line->_id}}", true)){
		      Calendar.regField(oForm.debut, dates);
		      Calendar.regField(oForm._fin, dates);
		      Calendar.regField(oForm.fin, dates);
	      }
      </script>
	  </td>
    {{else}}
    <td colspan="3" />
    {{/if}}
  </tr> 
  
  
  <!-- Si protocole, possibilité de rajouter une durée et un decalage entre les lignes -->
  {{if $line->_protocole}}
    {{include file="../../dPprescription/templates/line/inc_vw_duree_protocole_line.tpl"}}
  {{/if}}  
  
  <tr>  
	  <td style="text-align: left">
	    <!-- Affichage des alertes -->
	    {{if !($line->_protocole && $line->substitute_for_id)}}
	      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	    {{/if}}
	  </td>
    <td colspan="3">
      <table style="width:100%">
        <tr>
          <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left; width: 50%">
			      <!-- Selection des posologies statistiques -->
			      {{if $line->_ref_prescription->object_id}}
			      {{include file="../../dPprescription/templates/line/inc_vw_form_select_poso.tpl"}}
			      {{/if}}
			      <!-- Ajout de posologies -->			       
			      {{if $line->_can_modify_poso}}
			        {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Med"}}	  
						{{/if}}
	        </td>
          <td style="border:none; padding: 0;"><img src="images/icons/a_right.png" title="" alt="" /></td>
	        <td style="border:none; text-align: left;">
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
          </td>
        </tr>
      </table>
    </td>
  </tr>    
  <tr>  
    <td>
      <!-- Suppression de la ligne -->
      {{if $line->_can_delete_line}}
        <button type="button" class="trash notext" onclick="Prescription.delLine({{$line->_id}})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    </td>
    <td colspan="4">
     {{if $prescription->type == "sortie"}}
   

      <!-- Ajouter une ligne (même dans le cas du traitement)-->
      {{if $line->_can_vw_form_add_line_contigue}}
	      <div style="float: right">
	        {{include file="../../dPprescription/templates/line/inc_vw_form_add_line_contigue.tpl"}}
	      </div>
      {{/if}}
   
    {{/if}}
    
      <!-- Insérer un commentaire dans la ligne --> 
      <form name="commentaire-{{$line->_guid}}">
	      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
	      	{{if $line->_protocole}}
	      	  {{assign var=_line_praticien_id value=$app->user_id}}
		      {{else}}
		        {{assign var=_line_praticien_id value=$line->praticien_id}}
		      {{/if}}
		      {{if $line->_perm_edit}}
		      <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this); this.form.commentaire.onchange();" style="width: 110px;">
		        <option value="">&mdash; Choisir une aide</option>
		        {{html_options options=$aides_prescription.$_line_praticien_id.CPrescriptionLineMedicament}}
		      </select>
		      <input type="hidden" name="_hidden_commentaire" value="" />
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPrescriptionLineMedicament', this.form._hidden_commentaire, 'commentaire');">
		        Nouveau
		      </button>
		      {{/if}}
	    </form>
  		
  		 <!-- Si seulement 1 voie possible ou affichage bloqué-->
  		{{if $line->voie}}
	  		{{if $line->_ref_produit->voies|@count == 1 || !$line->_perm_edit}}
	  		  {{$line->voie}}
	  		{{else}}
		  		<select name="voie-{{$line->_id}}" 
		  						onchange="{{if !in_array($line->voie, $line->_ref_produit->voies)}}
		  												$('warning-voie-{{$line->_id}}').hide();
		  												$('warning-voie-option-{{$line->_id}}').hide();
		  		                  {{/if}}
		  		                  return submitVoie('{{$line->_id}}',this.value);">
			  		{{foreach from=$line->_ref_produit->voies item=libelle_voie}}
			  		  <option value="{{$libelle_voie}}" {{if $libelle_voie == $line->voie}}selected="selected"{{/if}}>{{$libelle_voie}}</option>
			  		{{/foreach}}
			  		{{if !in_array($line->voie, $line->_ref_produit->voies)}}
			  		  <script type="text/javascript">
			  		    $('warning-voie-{{$line->_id}}').show();
			  		  </script>
			  		  <option id="warning-voie-option-{{$line->_id}}" value="{{$line->voie}}" selected="selected" style="background-color: red">
			  		    {{$line->voie}}
			  		  </option>
			  		{{/if}}
		  		</select>
	  		{{/if}}
	  		<div id="warning-voie-{{$line->_id}}" class="small-warning" style="display:none;">
  		    Attention, la voie <strong>"{{$line->voie}}"</strong> n'est plus proposée pour ce médicament
  		  </div>
  		{{else}}
  		Aucune voie
  		{{/if}}
  		
  		{{if !$line->_count.administration && $line->_ref_prescription->object_id}}
  		{{if $line->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $line->_ref_substitution_lines.CPerfusion|@count}}
		    <form action="?" method="post" name="changeLine-{{$line->_guid}}">
		      <input type="hidden" name="m" value="dPprescription" />
		      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
		      <select name="object_guid" style="width: 75px;" 
		              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
		                           Prescription.reload('{{$line->_ref_prescription->_id}}', '', 'medicament');	
		                         } } )">
		        <option value="">Conserver</option>
			      {{foreach from=$line->_ref_substitution_lines item=lines_subst_by_chap}}
			          {{foreach from=$lines_subst_by_chap item=_line_subst}}
			          <option value="{{$_line_subst->_guid}}">{{$_line_subst->_view}}
			          {{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
			        {{/foreach}}
			      {{/foreach}}
		      </select>
		    </form>
		  
			  {{if $line->_ref_substitute_for->_class_name == "CPrescriptionLineMedicament"}}
				  {{assign var=dosql value="do_prescription_line_medicament_aed"}}
				{{else}}
				  {{assign var=dosql value="do_perfusion_aed"}}
				{{/if}}

				{{if $prescription->type == "sejour"}}
	        Modif. infirmière
		      <form name="editLine" action="?" method="post">
					  <input type="hidden" name="m" value="dPprescription" />
					  <input type="hidden" name="dosql" value="{{$dosql}}" />
						<input type="hidden" name="{{$line->_ref_substitute_for->_spec->key}}" value="{{$line->_ref_substitute_for->_id}}" />
						{{mb_field object=$line->_ref_substitute_for field="substitution_plan_soin" onchange="submitFormAjax(this.form, 'systemMsg')"}}
					</form>
				{{/if}}
		  {{/if}}
      {{/if}}
    
 	  </td>
  </tr>
  {{if ($prescription->type != "sortie") && !$line->_protocole && $line->signee && ($is_praticien || @$operation_id || $can->admin)}}
  <tr>
  <td></td>
    <td style="text-align:center;">
      <div id="stop-CPrescriptionLineMedicament-{{$line->_id}}">
        {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineMedicament"}}
      </div>
    </td>
    <td colspan="2">
      <!-- Ajouter une ligne (même dans le cas du traitement)-->
      {{if $line->_can_vw_form_add_line_contigue}}
	      <div>
	        {{include file="../../dPprescription/templates/line/inc_vw_form_add_line_contigue.tpl"}}
	      </div>
      {{/if}}
      </td>
  </tr>
  {{/if}}
</tbody>
</table>