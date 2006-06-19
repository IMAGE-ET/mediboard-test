<!-- $Id$ -->

<script type="text/javascript">
function setClose(time) {ldelim}
  window.opener.setRDV(time,
    "{$plage->plageconsult_id}",
    "{$plage->date|date_format:"%A %d/%m/%Y"}",
    "{$plage->freq}",
    "{$plage->chir_id}",
    "{$plage->_ref_chir->_view|escape:"javascript"}");
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
      {assign var="pct" value=$curr_plage->_affected/$curr_plage->_total*100|intval}
      {if $pct gt 100}
      {assign var="pct" value=100}
      {/if}
      {if $pct lt 50}{assign var="img" value="pempty.png"}
      {elseif $pct lt 90}{assign var="img" value="pnormal.png"}
      {elseif $pct lt 100}{assign var="img" value="pbooked.png"}
      {else}{assign var="img" value="pfull.png"}
      {/if}
      <tr style="{if $curr_plage->plageconsult_id == $plageconsult_id}font-weight: bold;{/if}">
        <td>
          <span style="float: left; width: {$pct}%; height: 100%; background:url(./modules/dPcabinet/images/{$img}) repeat;" />
          <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;plageconsult_id={$curr_plage->plageconsult_id}&amp;chir_id={$chir_id}&amp;date={$date}">
          {$curr_plage->date|date_format:"%A %d"}
          </a>
        </td>
        <td class="text">
          {$curr_plage->_ref_chir->_view}
        </td>
        <td class="text">
          {$curr_plage->libelle}
        </td>
        <td>
          {$curr_plage->_affected} / {$curr_plage->_total}
        </td>
      </tr>
      {/foreach}
    </table>
  </td>
  <td>
    <table class="tbl">
      {if $plage->plageconsult_id}
      <tr>
        <th colspan="3">Plage du {$plage->date|date_format:"%A %d %B %Y"}</th>
      </tr>
      <tr>
        <th>Heure</th>
        <th>Patient</th>
        <th>Durée</th>
      </tr>
      {else}
      <tr>
        <th colspan="3">Pas de plage le {$date|date_format:"%A %d %B %Y"}</th>
      </tr>
      {/if}
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
