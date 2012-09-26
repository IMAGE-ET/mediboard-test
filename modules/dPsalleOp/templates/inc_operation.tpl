{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

{{mb_script module="bloodSalvage" script="bloodSalvage"}}

{{assign var="sejour" value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}

<script type="text/javascript">
Main.add(function () {
  // Chargement de la gestion du personnel pour l'intervention
  reloadPersonnel('{{$selOp->_id}}');

  {{if $isPrescriptionInstalled}}
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('','{{$selOp->_ref_sejour->_id}}', null, null, '{{$selOp->_id}}', null, null);
  }
  {{/if}}
  
  reloadSurveillancePerop();
  loadPosesDispVasc();
  
  if($('dossier_traitement')){
    PlanSoins.loadTraitement('{{$selOp->sejour_id}}','{{$date}}','','administration');
  }
  
  if($('antecedents')){
    var url = new Url("dPcabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id","{{$selOp->sejour_id}}");
    url.requestUpdate("antecedents");
  }
  
  if($('constantes-medicales')){
    constantesMedicalesDrawn = false;
    refreshConstantesHack('{{$selOp->sejour_id}}');
  }
  
  if($('bloodsalvage_form')){
    var url = new Url("bloodSalvage", "httpreq_vw_bloodSalvage");
    url.addParam("op","{{$selOp->_id}}");
    url.requestUpdate("bloodsalvage_form");
  }
  
  if($('Imeds_tab')){
    var url = new Url("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", {{$sejour->_id}});
    url.requestUpdate('Imeds_tab');
  }
});

function printFicheBloc(interv_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", interv_id);
  url.popup(700, 700, 'FeuilleBloc');
}

var constantesMedicalesDrawn = false;
refreshConstantesHack = function(sejour_id) {
  (function(){
    if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
      refreshConstantesMedicales('CSejour-'+sejour_id);
      constantesMedicalesDrawn = true;
    }
  }).delay(0.5);
}

refreshConstantesMedicales = function(context_guid) {
  if(context_guid) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.requestUpdate("constantes-medicales");
  }
}

function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs)) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans)) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const)) {
      urlSuivi.addParam("_show_const", show_const);
    }
    urlSuivi.requestUpdate("dossier_suivi");
  }
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(sejour_id); 
    if(oForm.object_class.value != "" || oForm.libelle_ATC.value != ''){
      // Refresh de la partie administration
      PlanSoins.loadTraitement(sejour_id,'{{$date}}','','administration');
    }  
  } });
}

{{if $isPrescriptionInstalled}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null);
}
{{/if}}

function reloadSurveillancePerop(){
  if($('surveillance_perop')){
    var url = new Url("dPsalleOp", "ajax_vw_surveillance_perop");
    url.addParam("operation_id","{{$selOp->_id}}");
    url.requestUpdate("surveillance_perop");
  }
}

function loadPosesDispVasc(){
  var url = new Url("dPplanningOp", "ajax_list_pose_disp_vasc");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.addParam("sejour_id",    "{{$selOp->sejour_id}}");
  url.addParam("operateur_ids", "{{$operateurs_disp_vasc}}")
  url.requestUpdate("list-pose-dispositif-vasculaire");
}

{{if "maternite"|module_active}}
  function refreshGrossesse(operation_id) {
    var url = new Url("maternite", "ajax_vw_grossesse");
    url.addParam('operation_id', operation_id);
    url.requestUpdate('grossesse');
  }
{{/if}}

function infoAnapath(field) {
  if($V(field) == 1) {
    var url = new Url("salleOp", "ajax_info_anapath");
    url.addParam("operation_id", $V(field.form.operation_id));
    url.requestModal();
  }
  submitFormAjax(field.form, 'systemMsg');
}

function infoBacterio(field) {
  if($V(field) == 1) {
    var url = new Url("salleOp", "ajax_info_bacterio");
    url.addParam("operation_id", $V(field.form.operation_id));
    url.requestModal();
  }
  submitFormAjax(field.form, 'systemMsg');
}

</script>

<!-- Informations générales sur l'intervention et le patient -->
<table class="tbl">
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
      </a>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" />
       </a>
      
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient->_view}}</span>
      ({{$patient->_age}}
      {{if $patient->_annees != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      {{if $sejour->_ref_curr_affectation->_id}}- {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}{{/if}}
      <br />
      
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      <br />
      
      {{tr}}CSejour{{/tr}}
      du {{mb_value object=$sejour field=entree}}
      au 
      {{if $sejour->canEdit() || $currUser->_is_praticien}}
      {{assign var=sejour_guid value=$sejour->_guid}}
      <form name="editSortiePrevue-{{$sejour_guid}}" method="post" action="?"
            style="font-size: 0.9em;" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$sejour_guid" onchange="this.form.onsubmit()"}}
      </form>
      {{else}}
        {{mb_value object=$sejour field=sortie_prevue}}
      {{/if}}
    </th>
  </tr>
  
  {{if $conf.dPplanningOp.COperation.verif_cote && $selOp->cote_bloc && ($selOp->cote == "droit" || $selOp->cote == "gauche")}}
  <!-- Vérification du côté -->
  <tr>
    <td colspan="2">
      <strong>Côté DHE : {{mb_value object=$selOp field="cote"}}</strong> -
      <span class="{{if !$selOp->cote_admission}}warning{{elseif $selOp->cote_admission != $selOp->cote}}error{{else}}ok{{/if}}">
        Admission : {{mb_value object=$selOp field="cote_admission"}}
      </span> -
      <span class="{{if !$selOp->cote_consult_anesth}}warning{{elseif $selOp->cote_consult_anesth != $selOp->cote}}error{{else}}ok{{/if}}">
        Consult Anesth : {{mb_value object=$selOp field="cote_consult_anesth"}}
      </span> -
      <span class="{{if !$selOp->cote_hospi}}warning{{elseif $selOp->cote_hospi != $selOp->cote}}error{{else}}ok{{/if}}">
        Service : {{mb_value object=$selOp field="cote_hospi"}}
      </span> -
      <span class="{{if !$selOp->cote_bloc}}warning{{elseif $selOp->cote_bloc != $selOp->cote}}error{{else}}ok{{/if}}">
        Bloc : {{mb_value object=$selOp field="cote_bloc"}}
      </span>
    </td>
  </tr>
  {{/if}}
  
  {{assign var=consult_anesth value=$selOp->_ref_consult_anesth}}
  {{if $selOp->_ref_sejour->rques || $selOp->rques || $selOp->materiel || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
  <!-- Mise en avant du matériel et remarques -->
  <tr>
    {{if $selOp->_ref_sejour->rques || $selOp->rques || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
    {{if !$selOp->materiel}}
    <td class="text big-warning" colspan="2">
    {{else}}
    <td class="text big-warning halfPane">
    {{/if}}
      {{if $selOp->_ref_sejour->rques}}
      <strong>{{mb_label object=$selOp->_ref_sejour field=rques}}</strong>
      {{mb_value object=$selOp->_ref_sejour field=rques}}
      {{/if}}
      {{if $selOp->rques || ($consult_anesth->_id && $consult_anesth->_intub_difficile)}}
      <strong>{{mb_label object=$selOp field=rques}}</strong>
      {{/if}}
      {{if $selOp->rques}}
      {{mb_value object=$selOp field=rques}}
      {{/if}}
      {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
        <div style="font-weight: bold; color:#f00;">
          {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
        </div>
      {{/if}}
    </td>
    {{/if}}
    
    {{if $selOp->materiel}}
    {{if !$selOp->_ref_sejour->rques && !$selOp->rques}}
    <td class="text big-info" colspan="2">
    {{else}}
    <td class="text big-info halfPane">
    {{/if}}
      {{if $selOp->materiel}}
      <strong>{{mb_label object=$selOp field=materiel}}</strong>
      {{mb_value object=$selOp field=materiel}}
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
</table>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  {{if !$conf.dPsalleOp.mode_anesth && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
    <li><a href="#timing_tab">Timings</a></li>
  {{/if}}
  
  {{if $conf.dPsalleOp.enable_surveillance_perop}}
    <li onmouseup="reloadSurveillancePerop();"><a href="#surveillance_perop">Perop</a></li>
  {{/if}}

  <li><a href="#disp_vasculaire">Dispositifs vasc.</a></li>

  {{if !$conf.dPsalleOp.mode_anesth}}
    {{if (!$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $codage_prat))}}
    <li><a href="#codage_tab">Actes</a></li>
    <li><a href="#diag_tab">Diags.</a></li>
    {{/if}}
    
    {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $currUser->_is_anesth)}}
      {{assign var=callback value=refreshVisite}}
      <li onmouseup="reloadAnesth('{{$selOp->_id}}'); {{if "dPprescription"|module_active}}Prescription.updatePerop('{{$selOp->sejour_id}}');{{/if}}"><a href="#anesth_tab">Anesth.</a></li>
    {{/if}}
    {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
      <li><a href="#dossier_tab">Chir.</a></li>
    {{/if}}

    {{if $isPrescriptionInstalled}}
      <li onmouseup="PlanSoins.loadTraitement('{{$selOp->sejour_id}}','{{$date}}','','administration');"><a href="#dossier_traitement">Suivi soins</a></li>
      <li><a href="#prescription_sejour_tab">Prescription</a></li>
    {{/if}}
    
    <li onmousedown="refreshConstantesHack('{{$selOp->sejour_id}}');"><a href="#constantes-medicales">Surveillance</a></li>
    <li><a href="#antecedents">Atcd.</a></li>
  {{/if}}
  
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
  <li style="float: right">
    {{if "vivalto"|module_active && $can->edit}}
      {{mb_include module=vivalto template=inc_button_dmi operation=$selOp}}
    {{/if}}
    <button type="button" class="print" onclick="printFicheBloc('{{$selOp->_id}}');">Feuille de bloc</button>
  </li>
  
  {{if "maternite"|module_active && $sejour->grossesse_id}}
    <li onmouseup="refreshGrossesse('{{$selOp->_id}}')">
      <a href="#grossesse">{{tr}}CGrossesse{{/tr}}</a>
    </li>
  {{/if}}
</ul>
  
<hr class="control_tabs" />

<!-- Timings + Personnel -->
{{if !$conf.dPsalleOp.mode_anesth && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
<div id="timing_tab" style="display:none">
  <div id="check_lists">
    {{mb_include module=salleOp template=inc_vw_operation_check_lists}}
  </div>
  <div id="timing">
    {{mb_include module=salleOp template=inc_vw_timing}}
  </div>
  <div id="listPersonnel">
    {{* include file="inc_vw_personnel.tpl" *}}
  </div>
</div>
{{/if}}
  
{{if $conf.dPsalleOp.enable_surveillance_perop}}
  <div id="surveillance_perop" style="display:none"></div>
{{/if}}

<div id="disp_vasculaire" style="display:none">
  <fieldset style="clear: both;">
    <legend>{{tr}}CPoseDispositifVasculaire{{/tr}}</legend>
    <div id="list-pose-dispositif-vasculaire"></div>
  </fieldset>
  
  {{if $isbloodSalvageInstalled && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
    <fieldset>
      <legend>{{tr}}CCellSaver{{/tr}}</legend>
      <div id="bloodsalvage_form"></div>
    </fieldset>
  {{/if}}
</div>

{{if !$conf.dPsalleOp.mode_anesth}}

{{if (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit || $currUser->_is_praticien && $codage_prat)}}

<!-- codage des acte ccam et ngap -->
<div id="codage_tab" style="display:none">
  <form name="infoFactu" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="dosql" value="do_planning_aed" />
    <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="del" value="0" />
    <table class="form">
      <tr>
        <th style="text-align: right">
          {{mb_label object=$selOp field=anapath onclick="infoAnapath($(this.getAttribute('for')+'_1'));"}}
        </th>
        <td>
          {{mb_field object=$selOp field=anapath typeEnum="radio" onChange="infoAnapath(this);"}}
        </td>
        <th style="text-align: right">
          {{mb_label object=$selOp field=prothese}}
        </th>
        <td>
          {{mb_field object=$selOp field=prothese typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
        </td>
      </tr>
      <tr>
        <th style="text-align: right">
          {{mb_label object=$selOp field=labo onclick="infoBacterio($(this.getAttribute('for')+'_1'));"}}
        </th>
        <td style="vertical-align:middle;">
          {{mb_field object=$selOp field=labo typeEnum="radio" onChange="infoBacterio(this);"}}  
        </td>
        <td colspan="2"></td>
      </tr>
    </table>
  </form>
  
  <div id="codage_actes">
    {{mb_include template="inc_codage_actes" subject=$selOp}}
  </div> 
</div>

<!-- codage diagnostics CIM -->
<div id="diag_tab" style="display:none">
  <div id="cim">
    {{include file="inc_diagnostic_principal.tpl" modeDAS=true}}
  </div>
</div>
{{/if}}


{{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $currUser->_is_anesth)}}
<!-- Anesthesie -->
<div id="anesth_tab" style="display:none">
  {{mb_include module=salleOp template=inc_vw_info_anesth}}
</div>
{{/if}}

{{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
<!-- Documents et facteurs de risque -->
{{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
<div id="dossier_tab" style="display:none">
  <table class="form">
    <tr>
      <th class="title">Documents</th>
    </tr>
    <tr>
      <td>
        <div id="documents">
          {{mb_script module="dPcompteRendu" script="document"}}
          {{mb_script module="dPcompteRendu" script="modele_selector"}}
          {{mb_include module=planningOp template=inc_documents_operation operation=$selOp}}
        </div>
      </td>
    </tr>
  </table>
  <hr />
  <table class="tbl">
    <tr>
      <th class="title">Facteurs de risque</th>
    </tr>
  </table>
  {{include file=../../dPcabinet/templates/inc_consult_anesth/inc_vw_facteurs_risque.tpl sejour=$selOp->_ref_sejour patient=$selOp->_ref_sejour->_ref_patient}}
</div>
{{/if}}


<div id="constantes-medicales" style="display: none;"></div>
<div id="antecedents" style="display:none"></div>  

{{if $isPrescriptionInstalled}}
  <!-- Affichage de la prescription -->
  <div id="prescription_sejour_tab" style="display:none">
    <div id="prescription_sejour"></div>
  </div>
  
  <!-- Affichage du dossier de soins avec les lignes "bloc" -->
  <div id="dossier_traitement" style="display:none"></div>
{{/if}}

{{/if}}

{{if $isImedsInstalled}}
  <!-- Affichage de la prescription -->
  <div id="Imeds_tab" style="display:none"></div>
{{/if}}

{{if "maternite"|module_active && $sejour->grossesse_id}}
  <div id="grossesse" style="display: none;"></div>
{{/if}}