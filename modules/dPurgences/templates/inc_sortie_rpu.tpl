{{assign var=sejour_id value=$sejour->_id}}

{{assign var=rpu value=$sejour->_ref_rpu}}
{{assign var=rpu_id value=$rpu->_id}}

{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=atu value=$sejour->_ref_consult_atu}}

{{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
{{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}

<td {{if $sejour->annule}}class="cancelled"{{/if}}>
  
  <form name="validCotation-{{$atu->_id}}" action="" method="post"> 
    <input type="hidden" name="dosql" value="do_consultation_aed" />
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="consultation_id" value="{{$atu->_id}}" />
    <input type="hidden" name="valide" value="1" />    
  </form>

  {{mb_include template=inc_rpu_patient}}
</td>

{{if $sejour->annule}}
<td class="cancelled" colspan="10">
  {{if $rpu->mutation_sejour_id}}
  Hospitalisation
  <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
    dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
  </a> 
  {{else}}
  {{tr}}Cancelled{{/tr}}
  {{/if}}
</td>

{{else}}
{{if $dPconfig.dPurgences.responsable_rpu_view}}
<td>
  <a href="{{$rpu_link}}">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </a>
</td>
{{/if}}

<td class="button">
  {{include file="inc_pec_praticien.tpl"}}
</td>

<td>
  <!-- Vérification des champs semi obligatoires -->
  {{if !$rpu->ccmu           }}<div class="warning">Champ manquant {{mb_label object=$rpu field=ccmu           }}</div>{{/if}}
  {{if !$rpu->gemsa          }}<div class="warning">Champ manquant {{mb_label object=$rpu field=gemsa          }}</div>{{/if}}
	
  {{if $dPconfig.dPurgences.check_cotation}}
	  {{if !$rpu->_ref_consult->_ref_actes}}<div class="warning">Codage des actes manquant</div>{{/if}}
	  {{if $sejour->sortie_reelle && !$rpu->_ref_consult->valide}}<div class="warning">La cotation n'est pas validée</div>{{/if}}
  {{/if}}

  {{if $dPconfig.dPurgences.old_rpu == "1"}}
  {{if !$rpu->type_pathologie}}<div class="warning">Champ manquant {{mb_label object=$rpu field=type_pathologie}}</div>{{/if}}
  {{if !$rpu->urtrau         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urtrau         }}</div>{{/if}}
  {{if !$rpu->urmuta         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urmuta         }}</div>{{/if}}
  {{/if}}
        
  {{if $sejour->sortie_reelle}}
     {{if $rpu->destination}}
       <strong>{{tr}}CRPU-destination{{/tr}}:</strong>
       {{mb_value object=$rpu field="destination"}} <br />
     {{/if}}
     {{if $rpu->orientation}}
       <strong>{{tr}}CRPU-orientation{{/tr}}:</strong>
       {{mb_value object=$rpu field="orientation"}}      
     {{/if}}
  {{else}}
    <form name="editRPU-{{$rpu->_id}}" method="post" action="?">
      <input type="hidden" name="m" value="dPurgences" />
      <input type="hidden" name="dosql" value="do_rpu_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
      
      {{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination" onchange="submitFormAjax(this.form, 'systemMsg');"}}<br />
      {{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation" onchange="submitFormAjax(this.form, 'systemMsg');"}}
    </form>
  {{/if}}
</td>

<td>
  {{if $can->edit}}
  <a style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
    <img src="images/icons/planning.png" alt="Planifier"/>
  </a>
  {{/if}}

  <form name="editSejour-{{$sejour->_id}}" action="?m=dPurgences" method="post" onsubmit="return checkForm(this)"> 
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    
    <table>
    <!-- Annulation de la sortie -->
      {{if $sejour->sortie_reelle}}
      <tr>
        <td>
          {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
          {{if $sejour->mode_sortie}}
            {{mb_value object=$sejour field=mode_sortie}}
            {{if $sejour->mode_sortie == "transfert" && $sejour->etablissement_transfert_id}}
              {{assign var=etab_externe_id value=$sejour->etablissement_transfert_id}}
              {{assign var=etab_externe value=$listEtab.$etab_externe_id}}
              <br />vers {{$etab_externe->_view}}<br />
            {{/if}}
          {{/if}}
          {{mb_value object=$sejour field=sortie_reelle}}<br />
          
          <input type="hidden" name="mode_sortie" value="" />
          <input type="hidden" name="etablissement_transfert_id" value="" />
          <input type="hidden" name="sortie_reelle" value="" />
          <button class="cancel" type="button" onclick="onSubmitFormAjax(this.form, {onComplete: refreshSortie.curry(this, '{{$rpu->_id}}')});">
            Annuler la sortie
           </button>
         </td>
       </tr>
        
       <!-- Sortie à effectuer -->
       {{else}}
       <tr>
         <td class="text">
           {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
           <br />
          {{assign var=rpu_id value=$rpu->_id}}
          {{assign var=sejour_id value=$sejour->_id}}
          
          {{mb_field object=$sejour field="mode_sortie" onchange="initFields($rpu_id,$sejour_id,this.value);"}}
          <input type="hidden" name="_modifier_sortie" value="1" />
          <button class="tick" type="button" onclick="{{if $atu->_id}}validCotation('{{$atu->_id}}');{{/if}} onSubmitFormAjax(this.form, {onComplete: refreshSortie.curry(this, '{{$rpu_id}}')});">
            Effectuer la sortie
          </button>
         </td>
       </tr>
       <tr>
        <td>
					<div id="etablissement_sortie_transfert_{{$sejour->_id}}" {{if $sejour->mode_sortie != 'transfert'}}style="visibility: hidden"{{/if}}>
            {{mb_field object=$sejour field="etablissement_transfert_id" form="editSejour-$sejour_id" autocomplete="true,1,50,true,true"}}
          </div>
				</td>
       </tr>
      {{/if}}
      </table>
    </form>
  </td>
  <td id="rpu-{{$rpu->_id}}" 
	  {{if !$sejour->sortie_reelle}}
		  class="{{if !$rpu->sortie_autorisee}}arretee{{/if}} {{if $rpu->_can_leave_error}}error{{elseif $rpu->_can_leave_warning}}warning{{else}}ok{{/if}}"{{/if}}>
    {{if $sejour->sortie_reelle}}
      
    {{elseif $rpu->_can_leave == -1}}
		  {{if !$atu->_id}} 
        Pas encore de prise en charge <br />
 		  {{else}}
        {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.48{{/tr}} <br />
		  {{/if}}
      {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
    {{elseif $rpu->_can_leave != -1 && !$rpu->sortie_autorisee}}
      {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.64{{/tr}} <br />
      {{tr}}CRPU-sortie_assuree.0{{/tr}}
    {{else}}
      {{if $rpu->_can_leave_since}}
        {{tr}}CRPU-_can_leave_since{{/tr}}
      {{/if}}
      {{if $rpu->_can_leave_about}}
        {{tr}}CRPU-_can_leave_about{{/tr}}
      {{/if}}
      <span title="{{$sejour->sortie_prevue}}">{{mb_value object=$rpu field="_can_leave"}}</span><br />
      {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
    {{/if}}
  </td>
{{/if}}
