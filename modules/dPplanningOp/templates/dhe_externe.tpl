<script type="text/javascript">

var submitPatient = function() {
  oForm = getForm("editPatientFrm");
  return submitFormAjax(oForm, 'systemMsg');
}

var redirectDHE = function() {
  oForm = getForm("editPatientFrm");
  url = new Url("dPplanningOp", "vw_edit_planning");
  url.addParam("chir_id", '{{$praticien_id}}');
  url.addParam("pat_id", oForm.patient_id.value);
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
      {{elseif $msg_patient}}
      <div class="small-error">
        {{$msg_patient|smarty:nodefaults}}
      </div>
      {{else}}
      <form name="editPatientFrm" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : redirectDHE});">
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
