<script type="text/javascript">
  regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
</script>

<form name="changeView" action="index.php" method="get">
  <input type="hidden" name="m" value="{$m}" />
  <input type="hidden" name="tab" value="{$tab}" />

  <table class="form">
    <tr>
      <td colspan="6" style="text-align: center; width: 100%; font-weight: bold;">
        <div style="float: right;">{$hour|date_format:"%Hh%M"}</div>
        {$date|date_format:"%A %d %B %Y"}
        <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
      </td>
    </tr>
    <tr>
      <th><label for="vue2" title="Type de vue du planning">Type de vue:</label></th>
      <td colspan="5">
        <select name="vue2" onchange="this.form.submit()">
          <option value="0"{if $vue == "0"}selected="selected"{/if}>Tout afficher</option>
          <option value="1"{if $vue == "1"}selected="selected"{/if}>Cacher les Terminées</option>
        </select>
      </td>
    </tr>
  </table>

</form>
<table class="tbl">
{if $listPlage}
{foreach from=$listPlage item=curr_plage}
  <tr>
    <th colspan="5" style="font-weight: bold;">Consultations de {$curr_plage->_hour_deb}h à {$curr_plage->_hour_fin}h</th>
  </tr>
  <tr>
    <th>Heure</th>
    <th>Patient</th>
    <th>Motif</th>
    <th>RDV</th>
    <th>Etat</th>
  </tr>
  {foreach from=$curr_plage->_ref_consultations item=curr_consult}
  {if $curr_consult->premiere} 
    {assign var="style" value="style='background: #faa;font-size: 9px;'"}
  {else} 
    {assign var="style" value="style='font-size: 9px;'"}
  {/if}
  <tr>
    {if $curr_consult->consultation_id == $consult->consultation_id}
    <td style='font-size: 9px;background: #aaf;'>
    {else}
    <td {$style}>
    {/if}
      <a href="index.php?m={$m}&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">{$curr_consult->heure|truncate:5:"":true}</a>
    </td>
    <td class="text" {$style}>
      <a href="index.php?m={$m}&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
        {$curr_consult->_ref_patient->_view}
        {if $curr_consult->_ref_patient->_age != "??"}
          ({$curr_consult->_ref_patient->_age}&nbsp;ans)
        {/if}
      </a>
    </td>
    <td class="text" {$style}>
      <a href="index.php?m={$m}&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
        {$curr_consult->motif|nl2br|truncate:10:"...":true}
      </a>
    </td>
    <td {$style}>
      <a href="index.php?m={$m}&amp;tab=edit_planning&amp;consultation_id={$curr_consult->consultation_id}" title="Modifier le RDV">
        <img src="modules/dPcabinet/images/planning.png" alt="modifier" />
      </a>
    </td>
    <td {$style}>{$curr_consult->_etat}</td>
  </tr>
  {/foreach}
{/foreach}
{else}
  <tr>
    <th colspan="2" style="font-weight: bold;">Pas de consultations</th>
  </tr>
{/if}
</table>