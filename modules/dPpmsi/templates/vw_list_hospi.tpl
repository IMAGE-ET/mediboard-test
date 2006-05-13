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
      Liste {$listAffectations|@count} personnes hospitalisée(s) au {$date|date_format:"%A %d %B %Y"}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Entrée</th>
    <th>Intervention</th>
    <th>Sortie</th>
    <th>Chambre</th>
    <th>GHM</th>
    <th>Bornes</th>
  </tr>
  {foreach from=$listAffectations item=curr_aff}
  <tr>
    <td class="text">{$curr_aff->_ref_operation->_ref_chir->_view}</td>
    <td class="text" {if !$curr_aff->_ref_operation->_ref_GHM->_DP}style="background-color:#fdd"{/if}>
      <a href="index.php?m=dPpmsi&tab=vw_dossier&amp;pat_id={$curr_aff->_ref_operation->_ref_pat->patient_id}">
        {$curr_aff->_ref_operation->_ref_pat->_view}
      </a>
    </td>
    <td class="text">
      {$curr_aff->entree|date_format:"%d/%m/%Y à %Hh%M"}
      {if $curr_aff->_ref_prev->affectation_id}
      (déplacement)
      {/if}
    </td>
    <td class="text">
      {$curr_aff->_ref_operation->_ref_plageop->date|date_format:"%d/%m/%Y"}
    </td>
    <td class="text">
      {$curr_aff->sortie|date_format:"%d/%m/%Y à %Hh%M"}
      {if $curr_aff->_ref_next->affectation_id}
      (déplacement)
      {/if}
    </td>
    <td class="text">{$curr_aff->_ref_lit->_view}</td>
    <td class="text" {if !$curr_aff->_ref_operation->_ref_GHM->ghm_id}style="background-color:#fdd"{/if}>
      <a href="index.php?m=dPpmsi&amp;tab=labo_groupage&amp;operation_id={$curr_aff->_ref_operation->operation_id}" title="editer le GHM">
        {$curr_aff->_ref_operation->_ref_GHM->_GHM}
        {if $curr_aff->_ref_operation->_ref_GHM->_DP}
        : {$curr_aff->_ref_operation->_ref_GHM->_GHM_nom}
        {/if}
      </a>
    </td>
    <td class="text">
      {if $curr_aff->_ref_operation->_ref_GHM->_DP}
        {if $curr_aff->_ref_operation->_ref_GHM->_borne_basse > $curr_aff->_ref_operation->_ref_GHM->_duree}
        <img src="modules/dPpmsi/images/cross.png" alt="alerte" />
        Séjour trop court
        {elseif $curr_aff->_ref_operation->_ref_GHM->_borne_haute < $curr_aff->_ref_operation->_ref_GHM->_duree}
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