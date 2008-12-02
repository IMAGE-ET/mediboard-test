
<form action="?" name="selection" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<table class="form">
  <tr>
    <th class="category">{{$listEntree|@count}} patients en attente</th>
    <th class="category" colspan="2">
      {{$date|date_format:$dPconfig.longdate}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
</table>
</form>

<table class="tbl">
  <tr>
    <th>Heure</th>
    <th>Salle</th>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Entre Bloc</th>
  </tr> 
  {{foreach from=$listEntree item=curr_op}}
  <tr>
    <td>{{$curr_op->time_operation|date_format:$dPconfig.time}}</td>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
    <td class="button">
      {{if $can->edit || $modif_operation}}
      <form name="editEntreeBlocFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_planning_aed" />
      <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="entree_bloc" value="" />
      <button class="tick notext" type="submit" onclick="this.form.entree_bloc.value = 'current'">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}-{{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>