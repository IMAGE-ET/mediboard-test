<script>
  checkPlage = function(oform) {

    if (oform.chir_id.value == "" && oform.spec_id.value == "") {
      alert("Merci de choisir un chirurgien ou une spécialité");
      oform.chir_id.focus();
      return false;
    }

    return onSubmitFormAjax(oform, {onComplete: Control.Modal.close});
  };

  toggleDel = function(input) {
    if (input.disabled) {
      input.enable();
    }
    else {
      input.disable();
    }
    input.up('span').toggleClassName('opacity-40');
  };

  refreshFunction = function(chir_id) {
    var url = new Url("dPcabinet", "ajax_refresh_secondary_functions");
    url.addParam("chir_id"   , chir_id);
    url.addParam("field_name", "secondary_function_id");
    url.addParam("empty_function_principale", 1);
    url.addParam("change_active", 0);
    url.requestUpdate("secondary_functions");
  };

  Main.add(function() {
    var options = {
      exactMinutes: false,
      minInterval: {{"CPlageOp"|static:minutes_interval}},
      minHours: {{"CPlageOp"|static:hours_start|intval}},
      maxHours: {{"CPlageOp"|static:hours_stop|intval}}
    };
    var form = getForm('editFrm');
    Calendar.regField(form.debut, null, options);
    Calendar.regField(form.fin  , null, options);
  });
</script>

{{mb_script module=bloc script=edit_planning}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkPlage(this)" class="{{$plagesel->_spec}}">
<input type="hidden" name="m" value="dPbloc"/>
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$plagesel}}

<table class="form">
{{mb_include module=system template=inc_form_table_header object=$plagesel}}
  <tr>
    <td colspan="2">
      <fieldset>
        <legend>Attributs de la plage</legend>
        <table class="form">
          <tr>
            <th style="width: 15%;">{{mb_label object=$plagesel field="chir_id"}}</th>
            <td style="width: 45%;">
              <select name="chir_id" class="{{$plagesel->_props.chir_id}}" style="width: 15em;" onchange="refreshFunction(this.value)">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{if $chirs|@count}}
                <optgroup label="Chirurgiens">
                </optgroup>
                {{mb_include module=mediusers template=inc_options_mediuser selected=$plagesel->chir_id list=$chirs}}
                {{/if}}
                {{if $anesths|@count}}
                <optgroup label="Anesthésistes"></optgroup>
                {{mb_include module=mediusers template=inc_options_mediuser selected=$plagesel->chir_id list=$anesths}}
                {{/if}}
              </select>
            </td>
            <th style="width: 15%;">{{mb_label object=$plagesel field="salle_id"}}</th>
            <td style="width: 25%;">
              <select name="salle_id" class="{{$plagesel->_props.salle_id}}" style="width: 15em;">
                <option value="">&mdash;  {{tr}}Choose{{/tr}}</option>
                  {{foreach from=$listBlocs item=_bloc}}
                    <optgroup label="{{$_bloc}}">
                    {{foreach from=$_bloc->_ref_salles item=_salle}}
                      <option value="{{$_salle->_id}}" {{if $_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                        {{$_salle}}
                      </option>
                    {{foreachelse}}
                      <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                    {{/foreach}}
                    </optgroup>
                  {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th>{{mb_label class=CMediusers field=function_id}}</th>
            <td id="secondary_functions" class="text">
              {{assign var=chir value=$plagesel->_ref_chir}}
              {{assign var=selected value=$plagesel->secondary_function_id}}
              {{mb_include module=cabinet template=inc_refresh_secondary_functions field_name=secondary_function_id empty_function_principale=1 type_onchange="" change_active=0}}
            </td>
            <th>{{mb_label object=$plagesel field=date}}</th>
            <td>
              {{if $plagesel->_count_all_operations}}
                {{mb_value object=$plagesel field=date}} ({{$plagesel->_count_all_operations}} interventions)
              {{else}}
                {{mb_field object=$plagesel field=date form=editFrm register=true}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="spec_id"}}</th>
            <td>
              <select name="spec_id" class="{{$plagesel->_props.spec_id}}" style="width: 15em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$specs item=spec}}
                  <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
                  {{if $spec->function_id == $plagesel->spec_id}}selected="selected"{{/if}}>
                    {{$spec->text}}
                  </option>
                {{/foreach}}
              </select>
            </td>
            <th>{{mb_label object=$plagesel field="debut"}}</th>
            <td>{{mb_field object=$plagesel field="debut"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="anesth_id"}}</th>
            <td>
              <select name="anesth_id" style="width: 15em;">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser selected=$plagesel->anesth_id list=$anesths}}
              </select>
            </td>
            <th>{{mb_label object=$plagesel field=fin}}</th>
            <td>{{mb_field object=$plagesel field=fin}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$plagesel field="unique_chir"}}</th>
            <td>{{mb_field object=$plagesel field="unique_chir"}}</td>
            <th>{{mb_label object=$plagesel field="temps_inter_op"}}</th>
            <td>{{mb_field object=$plagesel field="temps_inter_op" form=editFrm register=true}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="max_intervention"}}</th>
            <td>{{mb_field object=$plagesel field="max_intervention" size=1 increment=true form="editFrm" min=0}}</td>
            <th>* {{mb_label object=$plagesel field="verrouillage"}}</th>
            <td>{{mb_field object=$plagesel field="verrouillage"}}</td>
          </tr>

          {{if $plagesel->_id}}
            <tr>
              <td colspan="4">
                <div class="small-info">Cette plage contient {{$plagesel->_count_operations}} intervention(s) et {{$plagesel->_count_operations_annulees}} intervention(s) annulée(s)</div>
              </td>
            </tr>
          {{/if}}
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Répétition</legend>
        <table class="form">
          <tr>
            <th class="narrow">
              <label for="_repeat" title="Nombre de semaines de répétition">Nombre de semaines</label>
            </th>
            <td class="narrow">
              <input type="text" class="notNull num min|1" name="_repeat" size="1" value="1" />
            </td>
            <td rowspan="2" class="text">
              <div class="small-info">
                Pour modifier plusieurs plages (nombre de semaines > 1),
                veuillez <strong>ne pas changer les champs début et fin en même temps</strong>.
                <br />
                * Cette valeur ne sera pas propagée sur les plages suivantes.
              </div>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="_type_repeat"}}</th>
            <td>{{mb_field object=$plagesel field="_type_repeat" style="width: 15em;" typeEnum="select"}}</td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Personnel en salle</legend>
        <table class="form">
          <tr>
            {{mb_include module=bloc template=inc_edit_plage_personnel  list=$listPers.iade         type="iade"}}
            {{mb_include module=bloc template=inc_edit_plage_personnel  list=$listPers.sagefemme    type="sagefemme"}}
          </tr>
          <tr>
            {{mb_include module=bloc template=inc_edit_plage_personnel  list=$listPers.op           type="op"}}
            {{mb_include module=bloc template=inc_edit_plage_personnel  list=$listPers.manipulateur type="manipulateur"}}
          </tr>
          <tr>
            {{mb_include module=bloc template=inc_edit_plage_personnel  list=$listPers.op_panseuse  type="op_panseuse"}}
            <td colspan="2"></td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr style="display: none;">
    <td>
      <fieldset>
        <legend>Remplacement</legend>
        <table class="form">
          <tr>
            <th>{{mb_label object=$plagesel field="delay_repl"}}</th>
            <td>{{mb_field object=$plagesel field="delay_repl" size=1 increment=true form="editFrm" min=0}} jours</td>
            <th>{{mb_label object=$plagesel field="spec_repl_id"}}</th>
            <td>
              <select name="spec_repl_id" class="{{$plagesel->_props.spec_repl_id}}" style="width: 15em;">
                <option value="">&mdash; Spécialité de remplacement</option>
                {{foreach from=$specs item=spec}}
                  <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
                  {{if $spec->function_id == $plagesel->spec_repl_id}}selected="selected"{{/if}}>
                    {{$spec->text}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
      {{if $plagesel->_id}}
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la plage opératoire',objName:'{{$plagesel->_view|smarty:nodefaults|JSAttribute}}', ajax:1}, {onComplete:Control.Modal.close})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>
</table>
</form>