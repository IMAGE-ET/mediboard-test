<table class="tbl">
  <tr class="clear">
    <th colspan="100">
      <strong>
      <a href="javascript:window.print()">
        Rapport comptable du {$date|date_format:"%d/%m/%Y"}
        {if $date != $datefin}
        au {$datefin|date_format:"%d/%m/%Y"}
        {/if}
      </a>
      <strong>
    </th>
  </tr>
  <tr>
    <th rowspan="2">Date</th>
    <th rowspan="2">libelle</th>
    <th colspan="{$listRubriques|@count}">Rubriques</th>
    <th rowspan="2">Mode de paiement</th>
    <th rowspan="2">Remarques</th>
  </tr>
  <tr>
    {foreach from=$listRubriques item=rubrique}
    <th>{$rubrique->nom}</th>
    {/foreach}
  </tr>
  {foreach from=$listGestionCab item=fiche}
  <tr>
    <td>{$fiche->date|date_format:"%d/%m/%Y"}</td>
    <td>{$fiche->libelle}</td>
    {foreach from=$listRubriques item=rubrique}
    <td>
      {if $rubrique->rubrique_id == $fiche->rubrique_id}
      {$fiche->montant} €
      {/if}
    </td>
    {/foreach}
    <td>{$fiche->_ref_mode_paiement->nom}</td>
    <td>{$fiche->rques|nl2br}</td>
  </tr>
  {/foreach}
  <tr>
    <th colspan="2">Totaux</th>
    {foreach from=$listRubriques item=rubrique}
      {foreach from=$totaux item=total}
      {if $rubrique->rubrique_id == $total.rubrique_id}
      <td>{$total.value} €</td>
      {/if}
      {/foreach}
    {/foreach}
    <th colspan="2" />
</table>