<script type="text/javascript">
  
  Main.add(function(){
    var oForm = getForm("editPlageTiming");
    var options = {
      exactMinutes: false, 
      minInterval : {{$conf.dPplanningOp.COperation.min_intervalle}},
      minHours    : {{$conf.dPplanningOp.COperation.duree_deb}},
      maxHours    : {{$conf.dPplanningOp.COperation.duree_fin}}
    };
    {{foreach from=$intervs item=_op}}
    oForm = getForm("edit-interv-{{$_op->_id}}");
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
        <button type="button" class="tick oneclick" style="float: right;" onclick="submitOrder(this.form);">
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

{{foreach from=$intervs item=_op}}
  {{assign var=sejour  value=$_op->_ref_sejour}}
  {{assign var=patient value=$sejour->_ref_patient}}

  <tr>
    <td class="text" style="vertical-align: top;">
      {{mb_include module=system template=inc_object_history object=$_op}}
      <strong>
        {{if $_op->rank}}
          <div class="rank" style="float: left;">{{$_op->rank}}</div>
          {{$_op->time_operation|date_format:$conf.time}}
        {{/if}}
        <a href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$patient->_id}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
            {{$patient}} ({{$patient->_age}} ans)
          </span>
        </a>
      </strong>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
      <br />
      <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');" {{if !$sejour->entree_reelle}}style="color: red;"{{/if}}>
       Adm. le {{mb_value object=$sejour field=_entree}} ({{$sejour->type|truncate:1:""|capitalize}})
      </span>
      {{if $_op->horaire_voulu}}
        <br />
        Passage souhaité à {{$_op->horaire_voulu|date_format:$conf.time}}
      {{/if}}
      <div style="text-align: right;">
      <form name="edit-interv-{{$_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
        {{mb_label object=$_op field="temp_operation"}}
        {{mb_field object=$_op field="temp_operation" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
        {{if $_op->rank}}
          <br />
          {{mb_label object=$_op field="pause"}}
          {{mb_field object=$_op field="pause" hidden=true onchange="submitOrder(this.form, '$list_type');"}}
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
      </div>
    </td>
    <td class="text" style="vertical-align: top;">
      <form name="editFrmAnesth{{$_op->operation_id}}" action="?m={{$m}}" method="post" style="float: right;">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_op->operation_id}}" />
        <select name="type_anesth" onchange="submitOrder(this.form, '{{$list_type}}');" style="width: 11em; clear: both;">
          <option value="">&mdash; Anesthésie</option>
          {{foreach from=$anesth item=curr_anesth}}
          <option value="{{$curr_anesth->type_anesth_id}}" {{if $_op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
            {{$curr_anesth->name}}
          </option>
          {{/foreach}}
        </select>
        <br />
        <button style="clear: both;" class="{{if $_op->_ref_consult_anesth->_ref_consultation->_id}}print{{else}}warning{{/if}}"
          style="width:11em;" type="button"
          onclick="printFicheAnesth('{{$_op->_ref_consult_anesth->_ref_consultation->_id}}', '{{$_op->_id}}');">
          Fiche d'anesthésie
        </button>
      </form>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_op->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
          {{if $_op->libelle}}
            <strong>{{$_op->libelle}}</strong>
          {{else}}
          {{foreach from=$_op->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong|truncate:60:"...":false}}<br />
          {{/foreach}}
          {{/if}}
        </span>
      </a>
      {{mb_label object=$_op field=cote}} :
      <strong>{{mb_value object=$_op field=cote}}</strong>
      {{if $_op->materiel}}
      <div class="small-info">
        <em>{{mb_label object=$_op field=materiel}}</em> :
        {{mb_value object=$_op field=materiel}}
      </div>
      {{/if}}
      {{if $_op->rques}}
      <div class="small-warning">
        <em>{{mb_label object=$_op field=rques}}</em> :
        {{mb_value object=$_op field=rques}}
      </div>
      {{/if}}
    </td>
    <td>
      <!-- Intervention à valider -->
      {{if $_op->annulee}}
      <img src="images/icons/cross.png" width="12" height="12" border="0" />
      {{elseif $_op->rank == 0}}
      <form name="edit-insert-{{$_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="_move" value="last" /><!-- Insertion à la fin -->
        <input type="hidden" name="operation_id" value="{{$_op->operation_id}}" />
        <button type="button" class="tick notext oneclick" title="{{tr}}Add{{/tr}}" onclick="submitOrder(this.form);">
          {{tr}}Add{{/tr}}
        </button>
      </form>
      {{else}}
      <!-- Intervention validée -->
        {{if $_op->rank != 1}}
        <form name="edit-up-{{$_op->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
          <input type="hidden" name="_move" value="before" />
          <button type="button" class="up notext oneclick" title="{{tr}}Up{{/tr}}" onclick="submitOrder(this.form, '{{$list_type}}');">
            {{tr}}Up{{/tr}}
          </button>
        </form>
        <br />
        {{/if}}
        <form name="edit-del-{{$_op->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
          <input type="hidden" name="_move" value="out" />
          <button type="button" class="cancel notext oneclick" title="{{tr}}Delete{{/tr}}" onclick="submitOrder(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        <br />
        {{if $_op->rank != $intervs|@count}}
        <form name="edit-down-{{$_op->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
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
{{foreachelse}}
  <tr>
    <td class="empty">
      {{tr}}COperation.none{{/tr}}

    </td>
  </tr>
{{/foreach}}
</table>