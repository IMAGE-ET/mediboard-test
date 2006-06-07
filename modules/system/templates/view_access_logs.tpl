<!-- $Id: $*/ -->

{literal}
<script type="text/javascript">

function zoom(date, module, action) {
  url = new Url();
  url.setModuleAction("system", "graph_accesslog");
  url.addParam("suppressHeaders", 1);
  url.addParam("size"  , 3);
  url.addParam("date"  , date);
  url.addParam("module", module);
  url.addParam("action", action);
  url.popup(1000, 400, date + " " + module + " " + action);
}

function pageMain() {
  {/literal}
  regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {literal}
}

</script>
{/literal}

<table class="main">

<tr>
  <th>
  	Logs d'accès du  {$date|date_format:"%A %d %b %Y"}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
  </th>
</tr>

<tr>
  <td>
    <table class="tbl">
      {foreach from=$logs item=log}
      {cycle values="0,1,2" assign=tr}
      {if $tr == 0}
      <tr>
      {/if}
        <td class="button">
          <a href="javascript:zoom('{$date}', '{$log->module}', '{$log->action}')" title="Agrandir">
            <img src="index.php?m=system&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={$date}&amp;module={$log->module}&amp;action={$log->action}" alt="Graphique pour {$log->module} - {$log->action}" />
          </a>
        </td>
      {if $tr == 2}
      </tr>
      {/if}
      {/foreach}
    </table>
  </td>
</tr>

</table>

