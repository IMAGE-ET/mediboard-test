<!-- $Id: $*/ -->

{literal}
<script type="text/javascript">

function zoom(date, module, action) {
  url = new Url();
  url.setModuleAction("system", "graph_accesslog");
  url.addParam("suppressHeaders", 1);
  url.addParam("size"  , 2);
  url.addParam("date"  , date);
  url.addParam("module", module);
  url.addParam("action", action);
  url.popup(670, 270, date + " " + module + " " + action);
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
    <form action="index.php" name="typevue" method="get">
    <input type="hidden" name="m" value="{$m}" />
    <input type="hidden" name="tab" value="{$tab}" />
    <label for="vue1" title="Type de vue des graphiques">Type de vue :</label>
    <select name="groupmod" onchange="this.form.submit()">
      <option value="0"{if !$groupmod}selected="selected"{/if}>Pas de regroupement</option>
      <option value="1"{if $groupmod}selected="selected"{/if}>Regrouper par module</option>
    </select>
    </form>
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
          {if $groupmod}
          <a href="javascript:zoom('{$date}', '{$log->module}', 0)" title="Agrandir">
            <img src="index.php?m=system&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={$date}&amp;module={$log->module}&amp;action=0" alt="Graphique pour {$log->module} - {$log->action}" />
          </a>
          {else}
          <a href="javascript:zoom('{$date}', '{$log->module}', '{$log->action}')" title="Agrandir">
            <img src="index.php?m=system&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={$date}&amp;module={$log->module}&amp;action={$log->action}" alt="Graphique pour {$log->module} - {$log->action}" />
          </a>
          {/if}
        </td>
      {if $tr == 2}
      </tr>
      {/if}
      {/foreach}
    </table>
  </td>
</tr>

</table>

