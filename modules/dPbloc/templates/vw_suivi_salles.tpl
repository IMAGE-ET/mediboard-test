<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
  PairEffect.initGroup("acteEffect");
}

function printFeuilleBloc(oper_id) {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}
</script>

<table class="main">
  <tr>
    <th colspan="{{$listSalles|@count}}">
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
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