<script type="text/javascript">

function pageMain() {
  
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
  opsUpdater.addParam("date", "{{$date}}");
  opsUpdater.periodicalUpdate('ops', { frequency: 90 });
  
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <div id="ops">
      </div>
    </td>
    <td class="halfPane">
      <div id="reveil">
      {{include file="inc_reveil_reveil.tpl"}}
      </div>
      <div id="out">
      {{include file="inc_reveil_out.tpl"}}
      </div>
    </td>
  </tr>
</table>