<script type="text/javascript">
Main.add(function () {
  regRedirectPopupCal("{{$date_suivi}}", "?m={{$m}}&tab={{$tab}}&date_suivi=");
});

function printFeuilleBloc(oper_id) {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function showLegend() {
  var url = new Url;
  url.setModuleAction("dPbloc", "legende");
  url.popup(500, 150, "Legend");
}
</script>

<table class="main">
  <tr>
    <td><a href="#" onclick="showLegend()" class="buttonsearch">Légende</a></td>
    <th colspan="100">
      <select name="bloc_id" onchange="location.href='?m={{$m}}&tab={{$tab}}&date_suivi={{$date_suivi}}&bloc_id='+$V(this)">
      {{foreach from=$listBlocs item=curr_bloc}}
        <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
          {{$curr_bloc->nom}}
        </option>
      {{/foreach}}
      </select>
      {{$date_suivi|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
    <td>
      <table class="form">
        <tr>
          <th class="category">{{$_salle->nom}}</th>
        </tr>
      </table>
      {{assign var="salle" value=$_salle}}     
      {{include file="../../dPsalleOp/templates/inc_details_plages.tpl"}}
    </td>
    {{foreachelse}}
    <td>{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
  </tr>
</table>