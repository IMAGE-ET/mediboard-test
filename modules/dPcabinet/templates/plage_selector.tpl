<script type="text/javascript">
function setClose(hour, min) {ldelim}
  window.opener.setRDV(hour, min,
              "{$plage->plageconsult_id}",
              "{$plage->date|date_format:"%A %d/%m/%Y"}",
              "{$plage->freq}",
              "{$plage->chir_id}",
              "{$plage->_ref_chir->_view}");
  window.close();
{rdelim}

function pageMain() {ldelim}
  regRedirectPopupCal("{$date}", "index.php?m=dPcabinet&a=plage_selector&dialog=1&chir={$chir}&date=");  
{rdelim}

</script>

<table class="main">

<tr>
  <th class="category" colspan="2">
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir={$chir}&amp;date={$pdate}">&lt;&lt;&lt;</a>
    {$date|date_format:"%B %Y"}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir={$chir}&amp;date={$ndate}">&gt;&gt;&gt;</a>
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
      <tr style="{if $curr_plage->plageconsult_id == $plageSel}font-weight: bold;{/if}">
        <td>
          <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;plagesel={$curr_plage->plageconsult_id}&amp;chir={$chir}&amp;date={$date}">
          {$curr_plage->date|date_format:"%A %d"}
          </a>
        </td>
        <td class="text">{$curr_plage->_ref_chir->_view}</td>
        <td class="text">{$curr_plage->libelle}</td>
        <td>{$curr_plage->_ref_consultations|@count} / {$curr_plage->_total}</td>
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
        <td><input type="button" value="+" onclick="setClose({$curr_place.hour}, {$curr_place.min})" />{$curr_place.hour}h{$curr_place.min}</td>
        <td class="text">
          {foreach from=$curr_place.patient item=curr_patient}
            {if $curr_patient.patient}
              <div {if $curr_patient.premiere}style="background: #faa;" {/if}>
              {$curr_patient.patient}
              {if $curr_patient.motif}
                ({$curr_patient.motif|truncate:"20"})
              {/if}
              </div>
            {/if}
          {/foreach}
        </td>
        <td>
          {foreach from=$curr_place.patient item=curr_patient}
            {if $curr_patient.patient}
              <div {if $curr_patient.premiere}style="background: #faa;" {/if}>
              {$curr_patient.duree}
              </div>
            {/if}
          {/foreach}
        </td>
      </tr>
      {/foreach}
    </table>
  </td>
</tr>

</table>
