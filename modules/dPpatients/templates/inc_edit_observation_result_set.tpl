<script>
  Main.add(function(){
    {{if $result_id}}
      $$("form[data-result_id={{$result_id}}]")[0].focusFirstElement();
    {{else}}
      $$(".result-form")[0].focusFirstElement();
    {{/if}}

    // For IE8
    $$("div.outlined input").each(function(input){
      input.observe("click", function(){
        input.form.select("div.outlined input.checked").invoke("removeClassName", "checked");
        input.addClassName("checked");

        var form = input.form;
        $V(form.elements.value, "FILE");
      });
    });
  });

  resetPicture = function(radio) {
    $V(radio.form.elements.value,'');
    $V(radio.form.elements.file_id,'');
    radio.form.select("div.outlined input.checked").invoke("removeClassName", "checked");
  };

  submitObservationResults = function(id, obj) {
    var forms = $$(".result-form").filter(function(form){ return $V(form.elements.value) !== ""; });

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

      return $$(".result-form").any(function(form){
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
    <col style="width: 12em;" />

    <tr>
      <th colspan="2" class="title">
        {{$pack}}
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

{{foreach from=$pack->_ref_graph_links item=_link}}
  {{assign var=_graph value=$_link->_ref_graph}}

  <table class="main form">
    <tr>
      <th class="category">{{$_graph}}</th>
    </tr>
  </table>

  {{if $_graph instanceof CSupervisionGraph}}
    {{foreach from=$_graph->_ref_axes item=_axis}}
      {{foreach from=$_axis->_ref_series item=_serie}}
        {{assign var=_result value=$_serie->_result}}
        {{assign var=_value_type value=$_result->_ref_value_type}}
        {{unique_id var=uid_form}}

        <form name="form-edit-observation-{{$uid_form}}" method="post" action="?"
              class="result-form" onsubmit="submitObservationResultSet(); return false;" data-result_id="{{$_result->_id}}">
          {{mb_class object=$_result}}
          {{mb_key object=$_result}}
          {{mb_field object=$_result field=value_type_id hidden=true}}
          {{mb_field object=$_result field=unit_id hidden=true}}
          {{mb_field object=$_result field=observation_result_set_id hidden=true}}

          <table class="main form">
            <col style="width: 12em;" />

            <tr>
              <th>
                <label for="value" title="{{$_value_type}}">{{$_result->_serie_title}}</label>
              </th>
              <td>
                {{if $_axis->_labels|@count}}
                  <input type="hidden" name="value" value="{{$_result->value}}" />

                  <select name="label_id" onchange="$V(this.form.elements.value, this.selectedIndex ? this.options[this.selectedIndex].get('value') : '')">
                    <option value="">&ndash; Valeur</option>
                    {{foreach from=$_axis->_ref_labels item=_label}}
                      <option
                        value="{{$_label->_id}}"
                        data-value="{{$_label->value}}"
                        {{if $_result->label_id == $_label->_id}}selected{{/if}}>
                        {{$_label->title}}
                      </option>
                    {{/foreach}}
                  </select>
                {{else}}
                  {{assign var=_prop value="float"}}

                  {{mb_field object=$_result field=value prop="$_prop"}}
                  {{$_result->_ref_value_unit->desc}}
                {{/if}}
              </td>
            </tr>
          </table>
        </form>
      {{/foreach}}
    {{/foreach}}

  {{elseif $_graph instanceof CSupervisionTimedData}}
    {{assign var=_result value=$_graph->_result}}
    {{assign var=_value_type value=$_result->_ref_value_type}}
    {{unique_id var=uid_form}}

    <form name="form-edit-observation-{{$uid_form}}" method="post" action="?"
          class="result-form" onsubmit="submitObservationResultSet(); return false;" data-result_id="{{$_result->_id}}">
      {{mb_class object=$_result}}
      {{mb_key object=$_result}}
      {{mb_field object=$_result field=value_type_id hidden=true}}
      {{mb_field object=$_result field=unit_id hidden=true}}
      {{mb_field object=$_result field=observation_result_set_id hidden=true}}

      <table class="main form">
        <tr>
          <td>
            {{mb_field object=$_result field=value style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;"}}
          </td>
        </tr>
      </table>
    </form>

  {{elseif $_graph instanceof CSupervisionTimedPicture}}
    {{assign var=_result value=$_graph->_result}}
    {{assign var=_value_type value=$_result->_ref_value_type}}
    {{unique_id var=uid_form}}

    <form name="form-edit-observation-{{$uid_form}}" method="post" action="?"
          class="result-form" onsubmit="submitObservationResultSet(); return false;" data-result_id="{{$_result->_id}}">
      {{mb_class object=$_result}}
      {{mb_key object=$_result}}
      {{mb_field object=$_result field=value_type_id hidden=true}}
      {{mb_field object=$_result field=observation_result_set_id hidden=true}}
      {{mb_field object=$_result field=value hidden=true}}

      <button type="button" class="cancel notext" onclick="resetPicture(this)"></button>
      {{foreach from=$_graph->_ref_files item=_file}}
        {{if !$_file->annule || $_file->_id == $_result->file_id}}
          <div class="outlined">
            <input type="radio" name="file_id" value="{{$_file->_id}}" {{if $_file->_id == $_result->file_id}}checked class="checked"{{/if}} />
            <label for="file_id_{{$_file->_id}}" ondblclick="this.form.onsubmit()">
              <div style="background: no-repeat center center url(?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_file->_id}}&amp;phpThumb=1&amp;h=80&amp;w=80&amp;q=95); height: 80px; width: 80px;"></div>
              {{$_file->_no_extension}}
            </label>
          </div>
        {{/if}}
      {{/foreach}}
    </form>
  {{/if}}
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