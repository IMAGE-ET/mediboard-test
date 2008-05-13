<!-- Initialisation des variables -->
{{*if (($curr_line->signee == 0 || $mode_pharma) && !$curr_line->valide_pharma)*}}

{{if (($curr_line->praticien_id == $app->user_id || $mode_pharma) && !$curr_line->valide_pharma && (!$curr_line->signee || $mode_pharma))}}
  {{assign var=perm_edit value=1}}
{{else}}
  {{assign var=perm_edit value=0}}
{{/if}}


{{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{assign var=line value=$curr_line}}
{{assign var=div_refresh value="medicament"}}
{{assign var=typeDate value="Med"}}


<tbody id="line_medicament_{{$curr_line->_id}}" class="hoverable">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$curr_line->_id}}" class="{{if $curr_line->_traitement}}traitement{{elseif $curr_line->date_arret}}arretee{{/if}}">
      <div style="float:left">
        {{if !$curr_line->_traitement}}
	        <!-- Selecteur equivalent -->
	        {{if $perm_edit}}
	          {{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
	        {{/if}}
	       
	        <!-- Formulaire ALD -->
		      {{if !$curr_line->_protocole}}
	            {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
		      {{/if}}
		    {{/if}}  
	     
	      <!-- Formulaire Traitement -->
        {{if !$curr_line->_protocole && !$mode_pharma && $perm_edit}} 
          {{include file="../../dPprescription/templates/line/inc_vw_form_traitement.tpl"}}
        {{/if}}  
      </div>
      
      <!-- AFfichage de la signature du praticien -->
      <div style="float: right">
        {{if ($curr_line->praticien_id != $app->user_id) && !$curr_line->_traitement && !$curr_line->_protocole}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else}}
          {{if !$curr_line->_traitement}}
            {{$curr_line->_ref_praticien->_view}}    
          {{/if}}
        {{/if}}
      
        <!-- Mode prescription -->
        {{if !$mode_pharma}}
	        {{if !$curr_line->_traitement}}
		          {{if !$curr_line->_protocole}}  
						    {{if !$curr_line->valide_pharma}}
						      {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
						    {{else}}
							    (Validé par le pharmacien)
							  {{/if}}
			      {{/if}}
	        {{else}}
	          Médecin traitant (Créé par {{$curr_line->_ref_praticien->_view}})
	        {{/if}}
        {{else}}
          {{if !$curr_line->_protocole}} 
            {{if !$curr_line->valide_pharma && !$curr_line->_traitement}}
              {{include file="../../dPprescription/templates/line/inc_vw_form_accord_praticien.tpl"}}
	          {{else}}
	            {{if $curr_line->accord_praticien}}
	              En accord avec le praticien
	            {{/if}}
	          {{/if}}
	          {{if !$curr_line->_traitement}}
	            {{include file="../../dPprescription/templates/line/inc_vw_form_validation_pharma.tpl"}}
	          {{/if}}
          {{/if}}
        {{/if}}
      </div>
      <a href="#produit{{$curr_line->_id}}" onclick="Prescription.viewProduit({{$curr_line->_ref_produit->code_cip}})">
        {{$curr_line->_view}}
      </a>
    </th>
  </tr>
  
  <!-- Pas traitement ni protocole -->
  {{if !$curr_line->_traitement && !$curr_line->_protocole}}
  <tr>  
    <td style="text-align: center">
    {{if !$curr_line->_ref_produit->inLivret && $prescription->type == "sejour"}}
        <img src="images/icons/warning.png" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
        <br />
    {{/if}}  
    {{if $curr_line->_ref_produit->hospitalier && $prescription->type == "sortie"}}
        <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
        <br />
    {{/if}}
    {{if $curr_line->_ref_produit->_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      <br />
    {{/if}}
    
    </td>
    <td colspan="2">
	    {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}  
	    {{if $perm_edit}}
		    <script type="text/javascript">
		      prepareForm(document.forms["editDates-Med-{{$curr_line->_id}}"]);
		      /*
		      Calendar.regField('editDates-Med-{{$curr_line->_id}}', "debut", false, dates);
	        Calendar.regField('editDates-Med-{{$curr_line->_id}}', "_fin", false, dates);
		      */
		      regFieldCalendar('editDates-Med-{{$curr_line->_id}}', "debut");
	        regFieldCalendar('editDates-Med-{{$curr_line->_id}}', "_fin");
		    </script>
	    {{/if}}          
		</td>
    <td>
      <!-- Formulaire permettant de stopper la prise (seulement si type == "sejour" ou si type == "pre_admission" )-->
      {{if $prescription->type == "sejour" || $prescription->type == "pre_admission"}}
        <div id="stop-CPrescriptionLineMedicament-{{$curr_line->_id}}">
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineMedicament"}}
        </div>
      {{/if}}
    </td>
  </tr>
  {{/if}} 
  
  <tr>  
	  <td style="text-align: center">
	    <!-- Affichage des alertes -->
	    {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$curr_line}}
	  </td>  
    <td colspan="3">
      <table style="width:100%">
        <tr>
          <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
			      <!-- Selection des posologies BCB -->
			      {{include file="../../dPprescription/templates/line/inc_vw_form_select_poso.tpl"}}
			      <!-- Ajout de posologies -->
			      {{if $perm_edit}}
			        {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Med"}}	  
						{{/if}}
	        </td>
          <td style="border:none; padding: 0;"><img src="images/icons/a_right.png" title="" alt="" /></td>
	        <td style="border:none; text-align: left;">
	          {{if $perm_edit}}
              <!-- Affichage des prises (modifiables) -->
              <div id="prises-Med{{$curr_line->_id}}">
                {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Med"}}
              </div>
            {{else}}
              <!-- Affichage des prises (non modifiables) -->
              {{foreach from=$curr_line->_ref_prises item=prise}}
                {{if $prise->quantite}}
                  {{$prise->_view}}, 
                {{/if}}
              {{/foreach}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>    
  
  <tr>  
    <td>
      <!-- Suppression de la ligne -->
      {{if $perm_edit}}
        <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    </td>
    <td colspan="4">
      <!-- Ajouter une ligne (même dans le cas du traitement)-->
      <div style="float: right;">
        {{include file="../../dPprescription/templates/line/inc_vw_form_add_line_contigue.tpl"}}
      </div>
      
      {{if $curr_line->_traitement && $prescription->object_id}}
        <!-- Stopper une ligne -->
        <div id="stop-CPrescriptionLineMedicament-{{$curr_line->_id}}" style="float: right">
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineMedicament"}}
        </div>
      {{/if}}
      
      <!-- Insérer un commentaire dans la ligne -->
      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
    </td>
  </tr>
</tbody>