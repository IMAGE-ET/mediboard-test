<script type="text/javascript">
function setClose(time) {ldelim}
  window.opener.setRDV(time,
    "{$plage->plageconsult_id}",
    "{$plage->date|date_format:"%A %d/%m/%Y"}",
    "{$plage->freq}",
    "{$plage->chir_id}",
    "{$plage->_ref_chir->_view}");
  window.close();
{rdelim}

function pageMain() {ldelim}
  regRedirectPopupCal("{$date}", "index.php?m=dPcabinet&a=plage_selector&dialog=1&chir_id={$chir_id}&date=");  
{rdelim}

</script>

<table class="main">

<tr>
  <th class="category" colspan="2">
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir_id={$chir_id}&amp;date={$pdate}">&lt;&lt;&lt;</a>
    {$date|date_format:"%B %Y"}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir_id={$chir_id}&amp;date={$ndate}">&gt;&gt;&gt;</a>
  </th>
</tr>

<tr>
  <td>
    <table class="tbl">
      <tr>
        <th>Date</th>
        <th>Praticien</th>
        <th>Libelle</th>
        <th>Etat</th>
      </tr>
      {foreach from=$listPlage item=curr_plage}
      <tr style="{if $curr_plage->plageconsult_id == $plageconsult_id}font-weight: bold;{/if}">
        <td>
          <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;plageconsult_id={$curr_plage->plageconsult_id}&amp;chir_id={$chir_id}&amp;date={$date}">
          {$curr_plage->date|date_format:"%A %d"}
          </a>
        </td>
        <td class="text">{$curr_plage->_ref_chir->_view}</td>
        <td class="text">{$curr_plage->libelle}</td>
        <td>{$curr_plage->_affected} / {$curr_plage->_total}</td>
      </tr>
      {/foreach}
    </table>
  </td>
  <td>
    <table class="tbl">
      <tr>
        <th>Heure</th>
        <th>Patient</th>
        <th>Durée</th>
      </tr>
      {foreach from=$listPlace item=curr_place}
      <tr>
        <td><input type="button" value="+" onclick="setClose('{$curr_place.time|date_format:"%H:%M"}')" />{$curr_place.time|date_format:"%Hh%M"}</td>
        <td class="text">
          {foreach from=$curr_place.consultations item=curr_consultation}
          <div {if $curr_consultation->premiere}style="background: #faa;" {/if}>
            {$curr_consultation->_ref_patient->_view}
            {if $curr_consultation->motif}
            ({$curr_consultation->motif|truncate:"20"})
            {/if}
          </div>
          {/foreach}
        </td>
        <td>
          {foreach from=$curr_place.consultations item=curr_consultation}
          <div {if $curr_consultation->premiere}style="background: #faa;" {/if}>
            {$curr_consultation->duree}
          </div>
          {/foreach}
        </td>
      </tr>
      {/foreach}
    </table>
  </td>
</tr>

</table>
