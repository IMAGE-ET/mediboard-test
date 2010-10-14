<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});
</script>

<table class="tbl main">
  <tr>
    <th class="title" colspan="7">
      Hors plage du {{$date|date_format:$dPconfig.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{tr}}CSejour-patient_id{{/tr}}</th>
    <th>{{tr}}COperation-chir_id{{/tr}}</th>
    <th>{{tr}}COperation-time_operation{{/tr}}</th>
    <th>{{tr}}CSalle{{/tr}}</th>
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{tr}}COperation-cote{{/tr}}</th>
    <th>{{tr}}COperation-rques{{/tr}}</th>
  </tr>
  {{foreach from=$urgences item=curr_op}}
  <tr>
    <td>
      <span class="{{if !$curr_op->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $curr_op->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_ref_sejour->_ref_patient->_guid}}');">
        {{$curr_op->_ref_sejour->_ref_patient->_view}}
      </span>
    </td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_op->_ref_chir}}</td>
    <td class="text">
      <form name="editTimeFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        {{assign var=curr_op_id value=$curr_op->_id}}
        {{mb_field object=$curr_op field=time_operation form="editTimeFrm$curr_op_id" register=true onchange="onSubmitFormAjax(this.form)"}}
      </form>
    </td>
    <td class="text">
      <form name="editSalleFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <select  style="width: 15em;" name="salle_id" onchange="onSubmitFormAjax(this.form)">
          <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
          {{foreach from=$listBlocs item=curr_bloc}}
          <optgroup label="{{$curr_bloc->nom}}">
            {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
            <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $curr_op->salle_id}}selected="selected"{{/if}}>
              {{$curr_salle->nom}}
            </option>
            {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select>
      </form>
      {{if $curr_op->_alternate_plages|@count}}
      <form name="editPlageFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="date" value="" />
        <input type="hidden" name="time_op" value="" />
        <input type="hidden" name="salle_id" value="" />
        <input type="hidden" name="horaire_voulu" value="{{$curr_op->time_operation}}" />
        <select name="plageop_id" style="width: 15em;" onchange="this.form.submit()">
          <option value="">&mdash; Replacer cette intervention</option>
          {{foreach from=$curr_op->_alternate_plages item=_plage}}
          <option value="{{$_plage->_id}}">{{$_plage->_ref_salle}} - {{mb_value object=$_plage field=debut}} à {{mb_value object=$_plage field=fin}} - {{$_plage}}</option>
          {{/foreach}}
        </select>
      </form>
      {{/if}}
    </td>
    <td class="text">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}');">
      {{if $curr_op->libelle}}
        <em>[{{$curr_op->libelle}}]</em><br />
      {{/if}}
      {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
      {{/foreach}}
      </span>
      </a>
    </td>
    <td>{{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}</td>
    <td class="text">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
      {{$curr_op->rques|nl2br}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>