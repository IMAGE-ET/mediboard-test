{{assign var=dossier_medical value=$sejour->_ref_dossier_medical}}

{{if $sejour->_id}}
<form name="frmCopyAntecedent" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_copy_antecedent" />
  <input type="hidden" name="antecedent_id" value="" />
  <input type="hidden" name="_sejour_id" value="{{$sejour->_id}}" />
</form>

<form name="frmCopyTraitement" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_copy_traitement" />
  <input type="hidden" name="traitement_id" value="" />
  <input type="hidden" name="_sejour_id" value="{{$sejour->_id}}" />
</form>

<script type="text/javascript">
var cancelledAnesthVisible = true;

onSubmitDossierMedical = function(oForm) {
	return onSubmitFormAjax(oForm, { 
		onComplete : DossierMedical.reloadDossierSejour 
	} );
}

copyAntecedent = function(antecedent_id){
  var oForm = document.frmCopyAntecedent;
  oForm.antecedent_id.value = antecedent_id;
 	onSubmitDossierMedical(oForm);
}

copyTraitement = function(traitement_id){
  var oForm = document.frmCopyTraitement;
  oForm.traitement_id.value = traitement_id;
  onSubmitDossierMedical(oForm);
}

toggleCancelledAnesth = function(list) {
  $(list).select('.cancelled').each(Element.show);
}

</script>

{{if $dossier_medical->_count_cancelled_antecedents}}
<button class="search" style="float: right" onclick="toggleCancelledAnesth('antecedents-{{$dossier_medical->_guid}}')">
  Afficher les {{$dossier_medical->_count_cancelled_antecedents}} annulés
</button>
{{/if}}

<strong>Antécédents significatifs</strong>

<ul id="antecedents-{{$dossier_medical->_guid}}">
	{{if $dossier_medical->_count_antecedents}}
  {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
  {{foreach from=$list_antecedent item=curr_antecedent}}
  <li {{if $curr_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
    <form name="delAntFrm-{{$curr_antecedent->_id}}" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="antecedent_id" value="{{$curr_antecedent->_id}}" />
      <input type="hidden" name="annule" value="" />
             
      <!-- Seulement si l'utilisateur est le créateur -->
      {{if $curr_antecedent->_ref_first_log && $curr_antecedent->_ref_first_log->user_id == $app->user_id}}
      <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, DossierMedical.reloadDossierSejour)">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </form>
    
    {{if $curr_antecedent->date}}
      {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
    {{/if}}

    <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_antecedent->_guid}}')">
	    <strong>
	    	{{if $curr_antecedent->type    }} {{mb_value object=$curr_antecedent field=type    }} {{/if}}
	    	{{if $curr_antecedent->appareil}} {{mb_value object=$curr_antecedent field=appareil}} {{/if}}
	    </strong>
      {{if $curr_antecedent->date}}
        [{{$curr_antecedent->date|date_format:"%Y"}}] 
      {{/if}}
      : {{$curr_antecedent->rques}}
    </span>
  </li>
  {{/foreach}}
  {{/foreach}}
	{{else}}
		<li><em>{{tr}}CAntecedent.none{{/tr}}</em></li>
	{{/if}}
</ul>
      
{{if is_array($dossier_medical->_ref_traitements)}}
<!-- Traitements -->
<strong>Traitements significatifs</strong>
<ul>
  {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
  <li>
    <form name="delTrmtFrm-{{$curr_trmt->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    <input type="hidden" name="traitement_id" value="{{$curr_trmt->_id}}" />
    <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierSejour)">
        {{tr}}delete{{/tr}}
    </button>
    {{if $curr_trmt->fin}}
      Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
    {{elseif $curr_trmt->debut}}
      Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
    {{/if}}
     <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_trmt->_guid}}', 'objectViewHistory')">
       {{$curr_trmt->traitement}}
     </span>
    </form>
  </li>
  {{foreachelse}}
  <li><em>Pas de traitements</em></li>
  {{/foreach}}
</ul>
{{/if}}

<strong>Diagnostics significatifs de l'intervention</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimAnesthField.remove('{{$curr_code->code}}')">
      {{tr}}delete{{/tr}}
    </button>
    {{$curr_code->code}}: {{$curr_code->libelle}}
  </li>
  {{foreachelse}}
  <li><em>Pas de diagnostic</em></li>
  {{/foreach}}
</ul>

<form name="editDiagAnesthFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="tab" value="edit_consultation" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="codes_cim" value="{{$dossier_medical->codes_cim}}" />
</form>

<script type="text/javascript">
oCimAnesthField = new TokenField(document.editDiagAnesthFrm.codes_cim, { 
  confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
  onChange : updateTokenCim10Anesth
} );
</script>
{{else}}
Aucun séjour sélectionné
{{/if}}