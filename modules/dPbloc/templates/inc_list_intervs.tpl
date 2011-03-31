<script type="text/javascript">
  
  Main.add(function(){
    var oForm = getForm("editPlageTiming");
    var options = {
      exactMinutes: false, 
      minInterval : {{$conf.dPplanningOp.COperation.min_intervalle}},
      minHours    : {{$conf.dPplanningOp.COperation.duree_deb}},
      maxHours    : {{$conf.dPplanningOp.COperation.duree_fin}}
    };
    {{foreach from=$intervs item=curr_op}}
    oForm = getForm("edit-interv-{{$curr_op->operation_id}}");
    Calendar.regField(oForm.temp_operation, null, options);
    if(oForm.pause) {
      Calendar.regField(oForm.pause);
    }
    {{/foreach}}
  });

</script>

{{if $list_type == "left"}}
<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      {{if $conf.dPplanningOp.COperation.horaire_voulu}}
      <form name="editOrderVoulu" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_order_voulu_op" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="del" value="0" />
        <button type="button" class="tick" style="float: right;">
          Utiliser les horaires souhaités
        </button>
      </form>
      {{/if}}
      Patients à placer
    </th>
  </tr>
{{else}}
<table class="tbl">
  <tr>
    <th class="title" colspan="3">Ordre des interventions</th>
  </tr>
{{/if}}

{{foreach from=$intervs item=curr_op}}
  <tr>
    <td class="text" style="vertical-align: top;">
      {{mb_include module=system template=inc_object_history object=$curr_op}}
      <strong>
        {{if $curr_op->rank}}
          {{$curr_op->rank}} - {{$curr_op->time_operation|date_format:$conf.time}}
        {{/if}}
        <a href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_ref_sejour->_ref_patient->_guid}}');">
            {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
          </span>
        </a>
      </strong>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_ref_sejour->_guid}}');" {{if !$curr_op->_ref_sejour->entree_reelle}}style="color: red;"{{/if}}>
        Admission le {{mb_value object=$curr_op->_ref_sejour field=_entree}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
      </span>
      {{if $curr_op->horaire_voulu}}
        <br />
        Passage souhaité à {{$curr_op->horaire_voulu|date_format:$conf.time}}
      {{/if}}
      <br />
      <form name="edit-interv-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
        {{mb_label object=$curr_op field="temp_operation"}}
        {{mb_field object=$curr_op field="temp_operation" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        {{if $curr_op->rank}}
          <br />
          {{mb_label object=$curr_op field="pause"}}
          {{mb_field object=$curr_op field="pause" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        {{elseif $listPlages|@count != '1'}}
          <br />
          Changement de salle
          <select name="plageop_id" onchange="submitOrder(this.form);">
            {{foreach from=$listPlages item="_plage"}}
            <option value="{{$_plage->_id}}" {{if $plage->_id == $_plage->_id}} selected = "selected"{{/if}}>
            {{$_plage->_ref_salle->nom}} / {{$_plage->debut|date_format:$conf.time}} à {{$plage->fin|date_format:$conf.time}}
            </option>
            {{/foreach}}
          </select>
        {{/if}}
      </form>
    </td>
    <td class="text" style="vertical-algn: top;">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}');">
        Dr {{$curr_op->_ref_chir->_view}}
        <br />
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
        {{else}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
          <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong|truncate:60:"...":false}}<br />
        {{/foreach}}
        {{/if}}
        </span>
      </a>
      Côté : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}
      <br />
      {{if $curr_op->rques}}
      <div class="small-warning">
        {{$curr_op->rques|nl2br}}
      </div>
      {{/if}}
      <button style="float: right;" class="{{if $curr_op->_ref_consult_anesth->_ref_consultation->_id}}print{{else}}warning{{/if}}" style="width:11em;" type="button" onclick="printFicheAnesth('{{$curr_op->_ref_consult_anesth->_ref_consultation->_id}}', '{{$curr_op->_id}}');">
        Fiche d'anesthésie
      </button>
      <form name="editFrmAnesth{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
        <select name="type_anesth" onchange="submitOrder(this.form, '{{$list_type}}');" style="width: 10em; float: left;">
          <option value="">&mdash; Anesthésie &mdash;</option>
          {{foreach from=$anesth item=curr_anesth}}
          <option value="{{$curr_anesth->type_anesth_id}}" {{if $curr_op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
            {{$curr_anesth->name}}
          </option>
          {{/foreach}}
        </select>
      </form>
    </td>
    <td>
      <!-- Intervention à valider -->
      {{if $curr_op->annulee}}
      <img src="images/icons/cross.png" width="12" height="12" border="0" />
      {{elseif $curr_op->rank == 0}}
      <form name="edit-insert-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="_move" value="last" /><!-- Insertion à la fin -->
        <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
        <button type="button" class="tick notext oneclick" title="{{tr}}Add{{/tr}}" onclick="submitOrder(this.form);">
          {{tr}}Add{{/tr}}
        </button>
      </form>
      {{else}}
      <!-- Intervention validée -->
        {{if $curr_op->rank != 1}}
        <form name="edit-up-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="before" />
          <button type="button" class="up notext oneclick" title="{{tr}}Up{{/tr}}" onclick="submitOrder(this.form, '{{$list_type}}');">
            {{tr}}Up{{/tr}}
          </button>
        </form>
        <br />
        {{/if}}
        <form name="edit-del-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="out" />
          <button type="button" class="cancel notext oneclick" title="{{tr}}Delete{{/tr}}" onclick="submitOrder(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        <br />
        {{if $curr_op->rank != $intervs|@count}}
        <form name="edit-down-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="after" />
          <button type="button" class="down notext oneclick" title="{{tr}}Down{{/tr}}" onclick="submitOrder(this.form, '{{$list_type}}');">
            {{tr}}Down{{/tr}}
          </button>
        </form>
        <br />
        {{/if}}
      {{/if}}
    </td>
  </tr>
{{/foreach}}
</table>