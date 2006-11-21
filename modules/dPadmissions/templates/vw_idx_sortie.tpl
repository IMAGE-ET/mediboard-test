<script type="text/javascript">

function reloadAmbu() {
  var ambuUrl = new Url;
  ambuUrl.setModuleAction("dPadmissions", "httpreq_vw_sorties_ambu");
  ambuUrl.addParam("date", "{{$date}}");
  ambuUrl.addParam("vue", "{{$vue}}");
  ambuUrl.requestUpdate('sortiesAmbu', { waitingText : null });
}

function submitAmbu(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAmbu });
}

function reloadComp() {
  var compUrl = new Url;
  compUrl.setModuleAction("dPadmissions", "httpreq_vw_sorties_comp");
  compUrl.addParam("date", "{{$date}}");
  compUrl.addParam("vue", "{{$vue}}");
  compUrl.requestUpdate('sortiesComp', { waitingText : null });
}

function submitComp(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadComp });
}

function pageMain() {
  
  var ambuUpdater = new Url;
  ambuUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties_ambu");
  ambuUpdater.addParam("date", "{{$date}}");
  ambuUpdater.addParam("vue", "{{$vue}}");
  ambuUpdater.periodicalUpdate('sortiesAmbu', { frequency: 90 });
  
  var compUpdater = new Url;
  compUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties_comp");
  compUpdater.addParam("date", "{{$date}}");
  compUpdater.addParam("vue", "{{$vue}}");
  compUpdater.periodicalUpdate('sortiesComp', { frequency: 90 });

  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="typeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="vue" title="Choisir un type de vue">Type de vue</label>
      <select name="vue" onchange="submit()">
        <option value="0" {{if $vue == 0}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1" {{if $vue == 1}}selected="selected"{{/if}}>Ne pas afficher les validés</option>
      </select>
      </form>
    </td>
    <th class="halfPane">
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <td id="sortiesAmbu">
    </td>
    <td id="sortiesComp">
    </td>
  </tr>
</table>
