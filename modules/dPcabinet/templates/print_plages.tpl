<!-- $Id$ -->

<table class="tbl">
  <tr class="clear">
    <th colspan="6">
      <a href="javascript:window.print()">
        Rapport du {$deb|date_format:"%d/%m/%Y"}
        {if $deb != $fin}
        au {$fin|date_format:"%d/%m/%Y"}
        {/if}
      </a>
    </th>
  </tr>
  {foreach from=$listPlage item=curr_plage}
  <tr class="clear">
    <td colspan="6">
      <b>{$curr_plage->date|date_format:"%d/%m/%Y"} - Dr. {$curr_plage->_ref_chir->_view}</b>
    </td>
  </tr>
  <tr>
    <th rowspan="2"><b>Heure</b></th>
    <th colspan="2"><b>Patient</b></th>
    <th colspan="3"><b>Consultation</b></th>
  </tr>
  <tr>
    <th>Nom / Prénom</th>
    <th>Age</th>
    <th>Motif</th>
    <th>Remarques</th>
    <th>Durée</th>
  </tr>
  {foreach from=$curr_plage->_ref_consultations item=curr_consult}
  <tr>
    {if $curr_consult->premiere}
    <td style="background-color:#eaa">
    {else}
    <td>
    {/if}
      {$curr_consult->heure|date_format:"%Hh%M"}
    </td>
    <td>{$curr_consult->_ref_patient->_view}</td>
    <td>
      {$curr_consult->_ref_patient->_age} ans
      {if $curr_consult->_ref_patient->_age != "??"}
        ({$curr_consult->_ref_patient->_naissance})
      {/if}
    </td>
    <td class="text">{$curr_consult->motif|nl2br}</td>
    <td class="text">{$curr_consult->rques|nl2br}</td>
    <td class="text">{$curr_consult->duree} x {$curr_consult->_ref_plageconsult->freq|date_format:"%M"} min</td>
  </tr>
  {/foreach}
  {/foreach}
</table>