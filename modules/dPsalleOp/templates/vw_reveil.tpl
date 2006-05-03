{literal}
<script type="text/javascript">

function pageMain() {
  
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
  {/literal}
  opsUpdater.addParam("date", "{$date}");
  {literal}
  opsUpdater.periodicalUpdate('ops', { frequency: 30 });
  
}

</script>
{/literal}

<table class="main">
  <tr>
    <td class="halfPane">
      <div id="ops">
      </div>
    </td>
    <td class="halfPane">
      <div id="reveil">
      {include file="inc_reveil_reveil.tpl"}
      </div>
      <div id="out">
      {include file="inc_reveil_out.tpl"}
      </div>
    </td>
  </tr>
</table>