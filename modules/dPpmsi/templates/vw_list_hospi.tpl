{literal}
<script type="text/javascript">

function pageMain() {
  {/literal}
  regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {literal}
}

</script>
{/literal}

<table class="tbl">
  <tr>
    <th class="title" colspan="8">
      Liste {$listSejours|@count} personnes hospitalisée(s) au {$date|date_format:"%A %d %B %Y"}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Entrée</th>
    <th>Sortie</th>
    <th>Intervention(s)</th>
    <th>GHM</th>
    <th>Bornes</th>
  </tr>
  {foreach from=$listSejours item=curr_sejour}
  {assign var="GHM" value=$curr_sejour->_ref_GHM}
  <tr>
    <td class="text">
      {$curr_sejour->_ref_praticien->_view}
    </td>

    <td class="text" {if !$GHM->_DP} style="background-color:#fdd" {/if}>
      <a title="Voir le dossier PMSI" href="index.php?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={$curr_sejour->patient_id}">
        {$curr_sejour->_ref_patient->_view}
      </a>
    </td>

    <td class="text">
      {$curr_sejour->entree_prevue|date_format:"%d/%m/%Y à %Hh%M"}
    </td>

    <td class="text">
      {$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y à %Hh%M"}
    </td>

    <td class="text">
      {foreach from=$curr_sejour->_ref_operations item=curr_operation}
      <a title="Voir la feuille d'admission" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_operation->operation_id}">
        Le {$curr_operation->_ref_plageop->date|date_format:"%d/%m/%Y"}
        par le Dr. {$curr_operation->_ref_chir->_view}
      </a>
      <br />
      {/foreach}
    </td>
    
    <td class="text" {if !$GHM->ghm_id} style="background-color:#fdd" {/if}>
      <a title="Labo de groupage pour l'intervention" href="index.php?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={$curr_sejour->sejour_id}">
      	{$GHM->_GHM}
        {if $GHM->_DP}: {$GHM->_GHM_nom}{/if}
      </a>
    </td>
  
    <td class="text">
      {if $GHM->_DP}
        {if $GHM->_borne_basse > $GHM->_duree}
        <img src="modules/dPpmsi/images/cross.png" alt="alerte" />
        Séjour trop court
        {elseif $GHM->_borne_haute < $GHM->_duree}
        <img src="modules/dPpmsi/images/cross.png" alt="alerte" />
        Séjour trop long
        {else}
        <img src="modules/dPpmsi/images/tick.png" alt="ok" />
        {/if}
      {else}
      -
      {/if}
    </td>
  </tr>
  {/foreach}
</table>