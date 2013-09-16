<script>
  Main.add(function(){
    $$(".result-form")[0].focusFirstElement();
  });

  submitObservationResults = function(id, obj) {
    var forms = $$(".result-form");
    forms.each(function(form){
      $V(form.observation_result_set_id, id);
    });

    Form.chainSubmit(forms, Control.Modal.close);
  };

  submitObservationResultSet = function() {
    var form = getForm('form-edit-observation-result-set');

    var check = function(){
      if (!checkForm(form)) {
        return false;
      }

      return $$(".result-form").all(function(form){
        return $V(form.elements.value) !== "";
      });
    };

    return onSubmitFormAjax(form, {check: check});
  };
</script>

<form name="form-edit-observation-result-set" method="post" action="?" onsubmit="return false;">
  {{mb_class class=CObservationResultSet}}
  {{mb_key object=$result_set}}
  {{mb_field object=$result_set field=patient_id hidden=true}}
  {{mb_field object=$result_set field=context_class hidden=true}}
  {{mb_field object=$result_set field=context_id hidden=true}}
  <input type="hidden" name="callback" value="submitObservationResults" />

  <table class="main form">
    <col style="width: 30%;" />

    <tr>
      <th colspan="2" class="title">
        {{$axis}}
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$result_set field=datetime}}
      </th>
      <td>
        {{mb_field object=$result_set field=datetime register=true form="form-edit-observation-result-set"}}
      </td>
    </tr>
  </table>
</form>

{{foreach from=$results item=_result}}
  {{assign var=_value_type value=$_result->_ref_value_type}}

  <form name="form-edit-observation-result-{{$_value_type->_id}}" method="post" action="?" class="result-form" onsubmit="submitObservationResultSet(); return false;">
    {{mb_class object=$_result}}
    {{mb_key object=$_result}}
    {{mb_field object=$_result field=value_type_id hidden=true}}
    {{mb_field object=$_result field=unit_id hidden=true}}
    {{mb_field object=$_result field=observation_result_set_id hidden=true}}

    <table class="main form">
      <col style="width: 30%;" />

      <tr>
        <th>
          <label for="value" title="{{$_value_type}}">{{$_result->_serie_title}}</label>
        </th>
        <td>
          {{if $_value_type->datatype == "NM"}}
            {{assign var=_prop value="float"}}
          {{elseif $_value_type->datatype == "ST"}}
            {{assign var=_prop value="str"}}
          {{else}}
            {{assign var=_prop value="text"}}
          {{/if}}

          {{mb_field object=$_result field=value prop="$_prop notNull"}}
          {{$_result->_ref_value_unit->desc}}
        </td>
      </tr>
    </table>
  </form>
{{/foreach}}

<table class="main form">
  <col style="width: 30%;" />

  <tr>
    <td></td>
    <td>
      <button type="submit" class="submit" onclick="submitObservationResultSet()">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>