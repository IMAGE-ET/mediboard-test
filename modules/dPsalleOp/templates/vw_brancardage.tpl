<script type="text/javascript">

Main.add(function () {
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_entree_bloc");
  opsUpdater.addParam("date", "{{$date}}");
  opsUpdater.periodicalUpdate('ops', { frequency: 90 });
  
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab=vw_brancardage&date=");
});

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <div id="reveil">
      {{include file="inc_entree_bloc.tpl"}}
      </div>
    </td>
    <td class="halfPane">
      <div id="ops">
      </div>
    </td>
  </tr>
</table>