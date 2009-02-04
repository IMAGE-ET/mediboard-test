{{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{assign var=line value=$curr_line}}
{{assign var=div_refresh value="medicament"}}
{{assign var=typeDate value="Med"}}
<table {{if ($full_line_guid == $line->_guid) && $readonly}}style="border: 2px solid #6688CC"{{/if}} class="tbl {{if $line->_traitement}}traitement{{else}}med{{/if}}{{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}"
       id="line_medicament_{{$line->_id}}">
<tbody  class="hoverable">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="element {{if $line->_traitement}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole}}
         Main.add( function(){
           moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      <div style="float:left;">
            <!-- Fomulaire de modification de l'emplacement -->
  		  {{include file="../../dPprescription/templates/line/inc_vw_form_emplacement.tpl"}}
        {{if $line->_ref_parent_line->_id}}
          {{assign var=parent_line value=$line->_ref_parent_line}}
          <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
               class="tooltip-trigger" 
               onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$parent_line->_class_name}}', object_id: '{{$parent_line->_id}}' } })"/>
        {{/if}}
        {{if !$line->_traitement}}
	        <!-- Selecteur equivalent -->
	        {{if $line->_can_select_equivalent}}
	          {{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
	        {{/if}}	        
	        <!-- Formulaire ALD -->
	        {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
	        <!-- Formulaire Conditionnel -->
		    {{include file="../../dPprescription/templates/line/inc_vw_form_conditionnel.tpl"}}
		    {{/if}}  
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
          {{if !$line->_traitement && !$line->_protocole}}
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
          {{if $line->_traitement}}
            Médecin traitant
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
        {{if $line->_protocole && !$line->substitute_for && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$line->_id}}')">
             Lignes de substitution
            ({{$line->_count_substitution_lines}})
            </button>
        {{/if}}
        {{if ($line->_guid == $full_line_guid) && $readonly}} 
          <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$readonly}}', '{{$lite}}','');"></button>
        {{/if}}
      </div>
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})">
        <strong>{{$line->_ucd_view}}</strong>
      </a>
    </th>
  </tr>
  
  {{if $line->_is_perfusable && !$line->_traitement && !$line->substitute_for}}
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
		  		  <select name="perfusion_id" onchange="toggleTypePerfusion(this.form);">
		  		    <option value="">Nouvelle perfusion</option>
		  		  {{foreach from=$prescription->_ref_perfusions item=_perfusion}}
		  		    <option value="{{$_perfusion->_id}}">{{$_perfusion->_view}}</option>
		  		  {{/foreach}}
		  		  </select>
		  		  {{mb_field object=$perfusion field="type" defaultOption="&mdash; Type"}}
		  		  
			  		<button class="add" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { 
			  		  onComplete: function() { 
			  			  Prescription.reloadPrescPerf('{{$prescription_reelle->_id}}','{{$line->_protocole}}','{{$mode_pharma}}')  
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
	      if(document.forms["editDates-Med-{{$line->_id}}"]){
		      var oForm = document.forms["editDates-Med-{{$line->_id}}"];
		      prepareForm(oForm);
		      
		      if(oForm.debut){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "debut", false, dates);
		      }
		      if(oForm._fin){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "_fin", false, dates);			      
		      }
		      if(oForm.fin){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "fin", false, dates);		      
		      }
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
	    {{if !($line->_protocole && $line->substitute_for)}}
	      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	    {{/if}}
	  </td>
    <td colspan="3">
      <table style="width:100%">
        <tr>
          <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left; width: 50%">
			      <!-- Selection des posologies BCB -->
			      {{include file="../../dPprescription/templates/line/inc_vw_form_select_poso.tpl"}}
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
		      <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this); this.form.commentaire.onchange();" style="width: 110px;">
		        <option value="">&mdash; Choisir une aide</option>
		        {{html_options options=$aides_prescription.$_line_praticien_id.CPrescriptionLineMedicament}}
		      </select>
		      <input type="hidden" name="_hidden_commentaire" value="" />
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPrescriptionLineMedicament', this.form._hidden_commentaire, 'commentaire');">
		        Nouveau
		      </button>
	    </form>
  		
  		 <!-- Si seulement 1 voie possible ou affichage bloqué-->
  		{{if $line->_ref_produit->voies|@count == 1 || !$line->_perm_edit}}
  		  {{$line->voie}}
  		{{else}}
	  		<select name="voie-{{$line->_id}}" onchange="return submitVoie('{{$line->_id}}',this.value);">
		  		{{foreach from=$line->_ref_produit->voies item=libelle_voie}}
		  		  <option value="{{$libelle_voie}}" {{if $libelle_voie == $line->voie}}selected="selected"{{/if}}>{{$libelle_voie}}</option>
		  		{{/foreach}}
	  		</select>
  		{{/if}}
  	
  	  </td>
  </tr>
 
    		
  {{if $prescription->type != "sortie" && !$line->_protocole}}
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