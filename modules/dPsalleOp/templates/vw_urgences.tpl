<script type="text/javascript">
Main.add(function () {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});
</script>

<table class="tbl main">
  <tr>
    <th class="title" colspan="7">
      Urgences du {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
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
    <td>{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
    <td>Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
      {{$curr_op->_datetime|date_format:"%Hh%M"}}
      </a>
    </td>
    <td>
    {{if array_key_exists($curr_op->salle_id, $listSalles)}}
      <form name="editOpFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <select name="salle_id" onchange="submitFormAjax(this.form, 'systemMsg')">
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
      {{else}}
        {{$curr_op->_ref_salle->_view}}
      {{/if}}
    </td>
    <td class="text">
      {{if $curr_op->libelle}}
        <em>[{{$curr_op->libelle}}]</em><br />
      {{/if}}
      {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
      {{/foreach}}
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