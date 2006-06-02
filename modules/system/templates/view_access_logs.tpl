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
      <th>Période</th>
      <th>Module</th>
      <th>Action</th>
      <th>Hits</th>
      <th>Duration</th>
    </tr>
    
    {foreach from=$logs item=log}
    <tr>
      <td>{$log->period}</td>
      <td>{$log->module}</td>
      <td>{$log->action}</td>
      <td>{$log->hits}</td>
      <td>{$log->_average|string_format:"%.3f"} secondes</td>
    </tr>
    {/foreach}
    
    </table>
  </td>
</tr>

</table>

