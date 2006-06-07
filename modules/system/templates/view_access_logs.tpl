<!-- $Id: $*/ -->

{literal}
<script type="text/javascript">

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
    
    <tr>
      <th>Informations</th>
      <th>Graphique de la journée</th>
    </tr>
    
    {foreach from=$logs item=log}
    <tr>
      <td>
        <ul>
          <li><strong>Module :</strong> {$log->module}</li>
          <li><strong>Action :</strong> {$log->action}</li>
          <li><strong>Hits :</strong> {$log->hits}</li>
          <li><strong>Page :</strong> {$log->_average_duration|string_format:"%.3f"} secondes</li>
          <li><strong>DB :</strong> {$log->_average_request|string_format:"%.3f"} secondes</li>
        </ul>
      <td class="button">
        <img src="index.php?m=system&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={$date}&amp;module={$log->module}&amp;action={$log->action}" alt="Graphique pour {$log->module} - {$log->action}" />
      </td>
    </tr>
    {/foreach}
    
    </table>
  </td>
</tr>

</table>

