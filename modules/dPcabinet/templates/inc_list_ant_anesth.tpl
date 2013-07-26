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
};

copyAntecedent = function(antecedent_id){
  var oForm = document.frmCopyAntecedent;
  oForm.antecedent_id.value = antecedent_id;
   onSubmitDossierMedical(oForm);
};

copyTraitement = function(traitement_id){
  var oForm = document.frmCopyTraitement;
  oForm.traitement_id.value = traitement_id;
  onSubmitDossierMedical(oForm);
};

toggleCancelledAnesth = function(list) {
  $(list).select('.cancelled').each(Element.show);
}

</script>

{{if $dossier_medical->_count_cancelled_antecedents}}
<button class="search" style="float: right" onclick="Antecedent.toggleCancelled('antecedents-{{$dossier_medical->_guid}}')">
  Afficher les {{$dossier_medical->_count_cancelled_antecedents}} antécédents annulés
</button>
{{/if}}

<strong {{if $dossier_medical->_count_cancelled_antecedents}}style="line-height: 22px;"{{/if}}>Antécédents significatifs</strong>

<ul id="antecedents-{{$dossier_medical->_guid}}">
  {{if $dossier_medical->_count_antecedents || $dossier_medical->_count_cancelled_antecedents}}
  {{foreach from=$dossier_medical->_ref_antecedents_by_type key=_type item=list_antecedent}}
  {{foreach from=$list_antecedent item=_antecedent}}
  <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
    <!-- Seulement si l'utilisateur est le créateur -->
    {{if $_antecedent->_ref_first_log && $_antecedent->_ref_first_log->user_id == $app->user_id}}
    <form name="Del-{{$_antecedent->_guid}}" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      {{mb_key object=$_antecedent}}

      <input type="hidden" name="annule" value="" />

      <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, DossierMedical.reloadDossierSejour)">
        {{tr}}Delete{{/tr}}
      </button>
    </form>
    {{/if}}

    <span onmouseover="ObjectTooltip.createEx(this, '{{$_antecedent->_guid}}')">
      <strong>
        {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
        {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
      </strong>
      {{if $_antecedent->date}}
        [{{mb_value object=$_antecedent field=date}}] :
      {{/if}}
      {{$_antecedent->rques|nl2br}}
    </span>
  </li>
  {{/foreach}}
  {{/foreach}}
  {{else}}
    <li class="empty">{{tr}}CAntecedent.none{{/tr}}</li>
  {{/if}}
</ul>

{{if $dossier_medical->_count_cancelled_traitements}}
<button class="search" style="float: right" onclick="Traitement.toggleCancelled('traitements-{{$dossier_medical->_guid}}')">
  Afficher les {{$dossier_medical->_count_cancelled_traitements}} traitements stoppés
</button>
{{/if}}
      
{{if is_array($dossier_medical->_ref_traitements)}}
<!-- Traitements -->
<strong>Traitements significatifs</strong>
<ul id="traitements-{{$dossier_medical->_guid}}">
  {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
  <li {{if $curr_trmt->annule}}class="cancelled" style="display: none;"{{/if}}>
    <form name="delTrmtFrm-{{$curr_trmt->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    <input type="hidden" name="traitement_id" value="{{$curr_trmt->_id}}" />
    <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierSejour)">
        {{tr}}delete{{/tr}}
    </button>
    {{if $curr_trmt->fin}}
      Depuis {{mb_value object=$curr_trmt field=debut}} 
      jusqu'à {{mb_value object=$curr_trmt field=fin}} :
    {{elseif $curr_trmt->debut}}
      Depuis {{mb_value object=$curr_trmt field=debut}} :
    {{/if}}
     <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_trmt->_guid}}')">
       {{$curr_trmt->traitement|nl2br}}
     </span>
    </form>
  </li>
  {{foreachelse}}
  <li class="empty">Pas de traitements</li>
  {{/foreach}}
</ul>
{{/if}}

<!-- Traitements -->
{{if is_array($lines_tp)}}
<strong>Traitements personnels de la prescription de séjour</strong>
<ul>
  {{foreach from=$lines_tp item=_line}}
  <li>
    <form name="delTraitementDossierMedPat-{{$_line->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="prescription_line_medicament_id" value="{{$_line->_id}}" />
      {{if !$_line->signee}}
        <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierSejour)">
          {{tr}}delete{{/tr}}
        </button>
      {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
        <a href="#1" onclick="Prescription.showMonographyMedicament(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
          {{$_line->_ucd_view}} ({{$_line->_forme_galenique}})</a>
      </span>
    </form>
  </li>
  {{foreachelse}}
  <li class="empty">Pas de traitements personnels</li>
  {{/foreach}}
</ul>
{{/if}}

<strong>Diagnostics significatifs de l'intervention</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimAnesthField.remove('{{$_code->code}}')">
      {{tr}}delete{{/tr}}
    </button>
    {{$_code->code}}: {{$_code->libelle}}
  </li>
  {{foreachelse}}
  <li class="empty">Pas de diagnostic</li>
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
Main.add(function(){
  oCimAnesthField = new TokenField(getForm("editDiagAnesthFrm").codes_cim, { 
    confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
    onChange : updateTokenCim10Anesth
  });
});
</script>
{{else}}
<div class="empty">Aucun séjour sélectionné</div>
{{/if}}