{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPpatients" script="patient" ajax=true}}

<script>
Document.refreshList = function() {
  if (document.actionPat) {
    new Url("dPpatients", "httpreq_vw_patient").
      addParam("patient_id", document.actionPat.patient_id.value).
      requestUpdate('vwPatient');
  }
}
</script>

{{if $patient->_vip}}
  <div class="big-warning">
    Vous n'avez pas accès à l'identité de ce patient.
    Veuillez contacter un administrateur de la clinique
    pour avoir plus d'information sur ce problème.
  </div>
  {{mb_return}}
{{/if}}

{{assign var=show_patient_link value="CAppUI::conf"|static_call:"dPpatients identitovigilance show_patient_link":"CGroups-$g"}}

{{if $show_patient_link && $patient->_ref_patient_links|@count}}
  <div class="small-info">
    Patient associé avec le(s) patient(s) suivant(s) : 
    <ul>
      {{foreach from=$patient->_ref_patient_links item=_patient_link}}
        {{assign var=doubloon value=$_patient_link->_ref_patient_doubloon}}
        <li>
          {{assign var=identity_status value="CAppUI::conf"|static_call:"dPpatients CPatient manage_identity_status":"CGroups-$g"}}
          {{assign var=allowed_modify value="CAppUI::pref"|static_call:"allowed_identity_status"}}
          {{if !$identity_status || $identity_status && $allowed_modify}}
            <form name="unlink_patient_{{$doubloon->_id}}" method="post"
                  onsubmit="return onSubmitFormAjax(this, function() {
                                                                      if (window.reloadPatient) {
                                                                        reloadPatient('{{$patient->_id}}');
                                                                      }
                                                                    })">
              {{mb_key object=$_patient_link}}
              {{mb_class object=$_patient_link}}
              <input type="hidden" name="del" value="1">
              <button type="submit" class="unlink notext" title="{{tr}}Unlink{{/tr}}">
                {{tr}}Unlink{{/tr}}
              </button>
            </form>
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$doubloon->_guid}}')">
            <a href="?m=patients&tab=vw_edit_patients&patient_id={{$doubloon->_id}}">{{$doubloon->_IPP}} - {{$doubloon->_view}}</a>
          </span>
        </li>
      {{/foreach}}
    </ul>
  </div>
{{/if}}

{{mb_include module=patients template=inc_vw_identite_patient}}

<table class="form">
  <tr>
    <th class="category">Planifier</th>
  </tr>
  <tr>
    <td class="button">
      {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
      {{if $ecap_dhe}}
        {{mb_include module=ecap template=inc_button_dhe patient_id=$patient->_id praticien_id=""}}
      {{else}}
        {{if !$app->user_prefs.simpleCabinet}}
          {{if $canPlanningOp->edit}}
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
              {{tr}}COperation{{/tr}}
            </a>
          {{/if}}
          {{if $canPlanningOp->read}}
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
              Interv. hors plage
            </a>
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
              {{tr}}CSejour{{/tr}}
            </a>
          {{/if}}
        {{/if}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="button">
      {{if $canCabinet->read}}
        <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
          {{tr}}CConsultation{{/tr}}
        </a>

        {{mb_include module="cabinet" template="inc_button_consult_immediate" patient_id=$patient->_id}}
      {{/if}}
    </td>
  </tr>
</table>

<table class="form">
  {{assign var="affectation" value=$patient->_ref_curr_affectation}}
  {{if $affectation && $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{assign var="affectation" value=$patient->_ref_next_affectation}}
  {{elseif $affectation && $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_sejours || $nb_sejours_annules || $nb_ops_annulees}}
  <tr>
    <th colspan="2" class="category">
      Séjours
      {{if $nb_sejours_annules || $nb_ops_annulees}}
        (
        {{if $nb_sejours_annules}}
          {{$nb_sejours_annules}} séjour(s) annulé(s) {{if $nb_ops_annulees}}&mdash;{{/if}}
        {{/if}}
        {{if $nb_ops_annulees}}
          {{$nb_ops_annulees}} intervention(s) annulée(s)
        {{/if}}
        )
      {{/if}}
      {{if !$vw_cancelled}}
        {{if $nb_ops_annulees || $nb_sejours_annules}}
          <a class="button search" style="float: right" onclick="reloadPatient('{{$patient->_id}}', null, 1)"
             title="Voir {{if $nb_sejours_annules}}{{$nb_sejours_annules}} séjour(s) annulé(s){{if $nb_ops_annulees}} et {{/if}}{{/if}}{{if $nb_ops_annulees}}{{$nb_ops_annulees}} opération(s) annulée(s){{/if}}">
            Afficher les annulés
          </a>
        {{/if}}
      {{/if}}
    </th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=object}}
    {{mb_include module=patients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}

  {{if $patient->_ref_consultations || $nb_consults_annulees}}
  <tr>
    <th id="inc_vw_patient_th_consult" colspan="2" class="category">
      Consultations {{if $nb_consults_annulees}}({{$nb_consults_annulees}} consultation(s) annulée(s)){{/if}}
      {{if $nb_consults_annulees}}
        <a class="button search" style="float: right" onclick="reloadPatient('{{$patient->_id}}', null, 1)"
           title="Voir {{$nb_consults_annulees}} consultation(s) annulée(s))">
          Afficher les annulées
        </a>
      {{/if}}
    </th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=object}}
    {{mb_include module=patients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}

  {{if "maternite"|module_active && $patient->_ref_grossesses}}
    {{foreach from=$patient->_ref_grossesses item=grossesse}}
      <tr>
        <th colspan="2" class="category">Grossesse (terme prévu : {{$grossesse->terme_prevu|date_format:$conf.date}})</th>
      </tr>
      <tr>
        <td colspan="2">Séjours</td>
      </tr>
      {{foreach from=$grossesse->_ref_sejours item=object}}
        {{mb_include module=patients template=inc_vw_elem_dossier}}
      {{foreachelse}}
        <td colspan="2" class="empty">{{tr}}CSejour.none{{/tr}}</td>
      {{/foreach}}
      <tr>
        <td colspan="2">Consultations</td>
      </tr>
      {{foreach from=$grossesse->_ref_consultations item=object}}
        {{mb_include module=patients template=inc_vw_elem_dossier show_semaine_grossesse=1}}
      {{foreachelse}}
        <td colspan="2" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
      {{/foreach}}
    {{/foreach}}
  {{/if}}
</table>