<script type="text/javascript">
checkPlage = function() {
  var form = getForm('editFrm');
  
  if (!checkForm(form)) {
    return false;
  }
    
  if (form.chir_id.value == "" && form.spec_id.value == "") {
    alert("Merci de choisir un chirurgien ou une spécialité");
    form.chir_id.focus();
    return false;
  }
  
  return true;
}

toggleDel = function(input) {
  if (input.disabled) {
    input.enable();
  }
  else {
    input.disable();
  }
  input.up('span').toggleClassName('opacity-40');
}

refreshFunction = function(chir_id) {
  var url = new Url("dPcabinet", "ajax_refresh_secondary_functions");
  url.addParam("chir_id"   , chir_id);
  url.addParam("field_name", "secondary_function_id");
  url.addParam("empty_function_principale", 1);
  url.addParam("change_active", 0);
  url.requestUpdate("secondary_functions");
}

Main.add(function(){
  var oForm = getForm('editFrm');
  Calendar.regField(oForm.date);
  Calendar.regField(oForm.temps_inter_op);
  var options = {
    exactMinutes: false, 
    minInterval: {{"CPlageOp"|static:minutes_interval}},
    minHours: {{"CPlageOp"|static:hours_start|intval}},
    maxHours: {{"CPlageOp"|static:hours_stop|intval}}
  };
  Calendar.regField(oForm.debut, null, options);
  Calendar.regField(oForm.fin  , null, options);
});
</script>

{{mb_script module=bloc script=edit_planning}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkPlage()" class="{{$plagesel->_spec}}">
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" />

<table class="form">
  <tr>
    {{if $plagesel->plageop_id}}
    <th class="title modify" colspan="2">
      
      {{mb_include module=system template=inc_object_idsante400 object=$plagesel}}
      {{mb_include module=system template=inc_object_history object=$plagesel}}
      {{tr}}CPlageOp-title-modify{{/tr}}
    {{else}}
    <th class="title" colspan="2">
      {{tr}}CPlageOp-title-create{{/tr}}
    {{/if}}
    </th>
  </tr>
  <tr>
    <td colspan="2">
      <fieldset>
        <legend>Attributs de la plage</legend>
        <table class="form">
          <tr>
            <th>{{mb_label object=$plagesel field="chir_id"}}</th>
            <td>
              <select name="chir_id" class="{{$plagesel->_props.chir_id}}" style="width: 15em;" onchange="refreshFunction(this.value)">
                <option value="">&mdash; Choisir un chirurgien</option>
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
            <th>{{mb_label object=$plagesel field="salle_id"}}</th>
            <td>
              <select name="salle_id" class="{{$plagesel->_props.salle_id}}" style="width: 15em;">
                <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
                {{if $plagesel->_id}}
                  {{foreach from=$listBlocs item=curr_bloc}}
                    <optgroup label="{{$curr_bloc->_view}}">
                    {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                      <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                        {{$curr_salle}}
                      </option>
                    {{foreachelse}}
                      <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                    {{/foreach}}
                    </optgroup>
                  {{/foreach}}
                {{else}}
                  {{foreach from=$bloc->_ref_salles item=curr_salle}}
                    <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                      {{$curr_salle}}
                    </option>
                  {{foreachelse}}
                    <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                  {{/foreach}}
                {{/if}}
              </select>
            </td>
          </tr>
          <tr>
            <th>
              {{mb_label class=CMediusers field=function_id}}
            </th>
            <td id="secondary_functions">
              {{assign var=chir value=$plagesel->_ref_chir}}
              {{assign var=selected value=$plagesel->secondary_function_id}}
              {{mb_include module=cabinet template=inc_refresh_secondary_functions field_name=secondary_function_id empty_function_principale=1 type_onchange="" change_active=0}}
            </td>
            <td colspan="2" style="min-width: 50%"></td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="spec_id"}}</th>
            <td>
              <select name="spec_id" class="{{$plagesel->_props.spec_id}}" style="width: 15em;">
                <option value="">&mdash; Choisir une spécialité</option>
                {{foreach from=$specs item=spec}}
                  <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
                  {{if $spec->function_id == $plagesel->spec_id}}selected="selected"{{/if}}>
                    {{$spec->text}}
                  </option>
                {{/foreach}}
              </select>
            </td>
            <th>{{mb_label object=$plagesel field="date"}}</th>
            <td>
              {{if $plagesel->plageop_id}}
              <input type="hidden" name="date" value="{{$plagesel->date}}" />
              {{else}}
              <input type="hidden" name="date" value="{{$date}}" />
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="anesth_id"}}</th>
            <td>
              <select name="anesth_id" style="width: 15em;">
                <option value="">&mdash; Choisir un anesthésiste</option>
                {{mb_include module=mediusers template=inc_options_mediuser selected=$plagesel->anesth_id list=$anesths}}
            </select>
            </td>
            <th>{{mb_label object=$plagesel field="debut"}}</th>
            <td>{{mb_field object=$plagesel field="debut"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$plagesel field="unique_chir"}}</th>
            <td>{{mb_field object=$plagesel field="unique_chir"}}</td>
            <th>{{mb_label object=$plagesel field="fin"}}</th>
            <td>{{mb_field object=$plagesel field="fin" }}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="max_intervention"}}</th>
            <td>{{mb_field object=$plagesel field="max_intervention" size=1 increment=true form="editFrm" min=0}}</td>
            <th>{{mb_label object=$plagesel field="temps_inter_op"}}</th>
            <td>{{mb_field object=$plagesel field="temps_inter_op"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plagesel field="verrouillage"}}</th>
            <td>{{mb_field object=$plagesel field="verrouillage"}}</td>
            <th></th>
            <td></td>
          </tr>
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
                veuillez ne pas changer les champs début et fin en même temps
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
            <td class="narrow">
              <select name="_iade_id" style="width: 10em;">
                <option value="">&mdash; {{tr}}CPersonnel.emplacement.iade{{/tr}}</option>
                {{foreach from=$listPersIADE item=_personnelBloc}}
                  <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
                {{/foreach}}
              </select>
            </td>
            <td class="text">
              {{foreach from=$plagesel->_ref_affectations_personnel.iade item=_affectation_personnel}}
                {{assign var=personnel value=$_affectation_personnel->_ref_personnel}}
                <span style="white-space: nowrap;">
                  <input type="hidden" name="_del_iade_ids[{{$personnel->_id}}]" value="{{$personnel->_id}}" disabled/>
                  <button type="button" class="cancel notext"
                    onclick="toggleDel(this.form.elements['_del_iade_ids[{{$personnel->_id}}]'])"></button>
                 {{$personnel->_ref_user}}
                 </span>
              {{/foreach}}
            </td>
          </tr>
          <tr>
            <td>
              <select name="_op_id" style="width: 10em;">
                <option value="">&mdash; {{tr}}CPersonnel.emplacement.op{{/tr}}</option>
                {{foreach from=$listPersAideOp item=_personnelBloc}}
                  <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
                {{/foreach}}
              </select>
            </td>
            <td class="text">
              {{foreach from=$plagesel->_ref_affectations_personnel.op item=_affectation_personnel}}
                {{assign var=personnel value=$_affectation_personnel->_ref_personnel}}
                <span style="white-space: nowrap;">
                  <input type="hidden" name="_del_op_ids[{{$personnel->_id}}]" value="{{$personnel->_id}}" disabled/>
                  <button type="button" class="cancel notext"
                    onclick="toggleDel(this.form.elements['_del_op_ids[{{$personnel->_id}}]'])"></button>
                 {{$personnel->_ref_user}}
                 </span>
              {{/foreach}}
            </td>
          </tr>
          <tr>
            <td>
              <select name="_op_panseuse_id" style="width: 10em;">
                <option value="">&mdash; {{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</option>
                {{foreach from=$listPersPanseuse item=_personnelBloc}}
                  <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
                {{/foreach}}
              </select>
            </td>
            <td class="text">
              {{foreach from=$plagesel->_ref_affectations_personnel.op_panseuse item=_affectation_personnel}}
                {{assign var=personnel value=$_affectation_personnel->_ref_personnel}}
                <span style="white-space: nowrap;">
                  <input type="hidden" name="_del_op_panseuse_ids[{{$personnel->_id}}]" value="{{$personnel->_id}}" disabled/>
                  <button type="button" class="cancel notext"
                    onclick="toggleDel(this.form.elements['_del_op_panseuse_ids[{{$personnel->_id}}]'])"></button>
                 {{$personnel->_ref_user}}
                 </span>
              {{/foreach}}
            </td>
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
    {{if $plagesel->plageop_id}}
      <button type="submit" class="modify">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la plage opératoire',objName:'{{$plagesel->_view|smarty:nodefaults|JSAttribute}}'})">
        Supprimer
      </button>
    {{else}}
      <button type="submit" class="new">Ajouter</button>
    {{/if}}
    </td>
  </tr>
</table>
</form>