<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date_suivi}}", "?m={{$m}}&tab={{$tab}}&date_suivi=");
  PairEffect.initGroup("acteEffect");
}

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
    <td>
      <a href="#" onclick="showLegend()" class="buttonsearch">Légende</a>
    </td>
    <th colspan="{{$listSalles|@count}}">
      {{$date_suivi|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
  {{foreach from=$listSalles key=keySalle item=currSalle}}
    <td>
      <table class="form">
        <tr>
          <th class="category">{{$currSalle->nom}}</th>
        </tr>
      </table>
      {{assign var="plages" value=$listInfosSalles.$keySalle.plages}}
      {{assign var="urgences" value=$listInfosSalles.$keySalle.urgences}}
      {{assign var="salle" value=$keySalle}}     
      {{include file="../../dPsalleOp/templates/inc_details_plages.tpl"}}
    </td>
  {{/foreach}}
  </tr>
</table>