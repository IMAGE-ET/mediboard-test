<script type="text/javascript">

var submitPatient = function() {
  oForm = getForm("editPatientFrm");
  return submitFormAjax(oForm, 'systemMsg');
}

var redirectDHEPatient = function() {
  oForm = getForm("editPatientFrm");
  url = new Url("dPplanningOp", "dhe_externe");
  url.addParam("praticien_id"                 , '{{$praticien_id}}');
  url.addParam("patient_id"                   , $V(oForm.patient_id));
  {{if isset($sejour|smarty:nodefaults)}}
  url.addParam("sejour_libelle"               , '{{$sejour->libelle}}');
  url.addParam("sejour_type"                  , '{{$sejour->type}}');
  url.addParam("sejour_entree_prevue"         , '{{$sejour->entree_prevue}}');
  url.addParam("sejour_sortie_prevue"         , '{{$sejour->sortie_prevue}}');
  url.addParam("sejour_remarques"             , '{{$sejour->rques}}');
  url.addParam("sejour_intervention"          , '{{$sejour_intervention}}');
  {{/if}}
  {{if isset($intervention|smarty:nodefaults)}}
  url.addParam("intervention_date"            , '{{$intervention->_datetime}}');
  url.addParam("intervention_duree"           , '{{$intervention->temp_operation}}');
  url.addParam("intervention_cote"            , '{{$intervention->cote}}');
  url.addParam("intervention_horaire_souhaite", '{{$intervention->horaire_voulu}}');
  url.addParam("intervention_codes_ccam"      , '{{$intervention->codes_ccam}}');
  url.addParam("intervention_materiel"        , '{{$intervention->materiel}}');
  url.addParam("intervention_remarques"       , '{{$intervention->rques}}');
  {{/if}}
  url.redirect();
}

var changePatientField = function(sField, sValue) {
  oForm = getForm("editPatientFrm");
  $V(oForm[sField], sValue);
}

</script>

<table class="main">
  <tr>
    <th class="title">Demande d'hospitalisation électronique externe</th>
  </tr>
  <tr>
    <td>
      {{if !$praticien_id}}
      <div class="error">
        Code praticien invalide
      </div>
      {{elseif $msg_error}}
      <div class="small-error">
        {{$msg_error|smarty:nodefaults}}
      </div>
      {{if isset($patient->_id|smarty:nodefaults) || isset($sejour->_id|smarty:nodefaults)}}
      <div class="small-info">
        Vous pouvez cependant effectuer les actions suivantes :
        <ul>
        {{if isset($sejour->_id|smarty:nodefaults)}}
          <li>
            <strong>Annuler ou modifier</strong>
            le {{$sejour->_view}} de {{$patient->_view}}
          </li>
          <li>
            <strong>Planifier une intervention</strong>
            au sein du {{$sejour->_view}} de {{$patient->_view}}
          </li>
          <li>
            <strong>Planifier une intervention hors plage</strong>
            au sein du {{$sejour->_view}} de {{$patient->_view}}
          </li>
        {{elseif isset($patient->_id|smarty:nodefaults)}}
          <li>
            <strong>Modifier le patient</strong>
            {{$patient->_view}}
          </li>
          <li>
            <strong>Planifier un séjour</strong>
            pour {{$patient->_view}}
          </li>
          <li>
            <strong>Planifier une intervention</strong>
            pour {{$patient->_view}}
          </li>
          <li>
            <strong>Planifier une intervention hors plage</strong>
            pour {{$patient->_view}}
          </li>
        {{/if}}
        </ul>
      </div>
      {{/if}}
      {{elseif isset($patient->_id|smarty:nodefaults)}}
      <form name="editPatientFrm" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : redirectDHEPatient});">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_purge" value="0" />
      {{mb_key object=$patient}}
      <fieldset>
        <legend>Validation du patient</legend>
        <table class="tbl">
          <tr>
            <th class="category narrow"></th>
            <th class="category" style="width: 33%">patient résultant</th>
            <th class="category" style="width: 33%">patient proposé</th>
            <th class="category" style="width: 33%">patient existant</th>
          </tr>
          {{foreach from=$list_fields key=_field item=_state}}
          <tr>
            <td style="text-align: right">{{mb_label object=$patient_resultat field=$_field}}</td>
            <td class="{{if $_state}}ok{{else}}warning{{/if}}">
              {{if $_state}}
              {{mb_value object=$patient_resultat field=$_field}}
              {{else}}
              {{mb_field object=$patient_resultat field=$_field readonly="readonly"}}
              {{/if}}
              
            </td>
            <td class="{{if $_state}}ok{{else}}warning{{/if}}">
              {{if !$_state}}
              <input type="radio" name="_choice_{{$_field}}" value="{{$patient->$_field}}" checked="checked" onchange="changePatientField('{{$_field}}', '{{$patient->$_field}}')" />
              {{/if}}
              {{mb_value object=$patient field=$_field}}
            </td>
            <td class="{{if $_state}}ok{{else}}warning{{/if}}">
              {{if !$_state}}
              <input type="radio" name="_choice_{{$_field}}" value="{{$patient_existant->$_field}}" onchange="changePatientField('{{$_field}}', '{{$patient_existant->$_field}}')" />
              {{/if}}
              {{mb_value object=$patient_existant field=$_field}}
            </td>
          </tr>
          <tr>
          {{/foreach}}
          <tr>
          	<td></td>
            <td colspan="3" class="button">
              <button type="button" class="submit" onclick="this.form.onsubmit()">{{tr}}Submit{{/tr}}</button>
            </td>
          </tr>
        </table>
      </fieldset>
      </form>
      {{/if}}
    </td>
  </tr>
</table>
