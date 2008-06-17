<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="7">Urgences du {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Praticien</th>
          <th>Heure</th>
          <th>Salle</th>
          <th>Intervention</th>
          <th>Coté</th>
          <th>Remarques</th>
        </tr>
        {{foreach from=$urgences item=curr_op}}
        <tr>
          <td>{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
          <td>Dr {{$curr_op->_ref_chir->_view}}</td>
          <td class="text">
            <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->operation_id}}">
            {{$curr_op->_datetime|date_format:"%Hh%M"}}
            </a>
          </td>
          <td>
            <form name="editOpFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
            <select name="salle_id" onchange="submitFormAjax(this.form, 'systemMsg')">
              <option value="">&mdash; Choix de la salle</option>
              {{foreach from=$listSalles item=curr_salle}}
              <option value="{{$curr_salle->salle_id}}" {{if $curr_salle->salle_id == $curr_op->salle_id}}selected="selected"{{/if}}>
                {{$curr_salle->nom}}
              </option>
              {{/foreach}}
            </select>
            </form>
          </td>
          <td class="text">
            {{if $curr_op->libelle}}
              <em>[{{$curr_op->libelle}}]</em>
              <br />
            {{/if}}
            {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
            {{/foreach}}
          </td>
          <td>{{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}</td>
          <td class="text">
            <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->operation_id}}">
            {{$curr_op->rques|nl2br}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>