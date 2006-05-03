<table class="main">
  <tr>
    <th>
      <a href="javascript:window.print()">
        &mdash; Dr. {$prat->_view} &mdash<br />
        Plages du {$deb|date_format:"%A %d %b %Y"}
        au {$fin|date_format:"%A %d %B %Y"}<br />
        {$plages|@count}
        {if $type}
        plage(s) payée(s)
        {else}
        plage(s) en attente de paiement
        {/if}
        sur la periode
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage horaire</th>
          <th>Libellé</th>
          <th>Tarif</th>
        </tr>
        {foreach from=$plages item=curr_plage}
        <tr>
          <td>{$curr_plage->date|date_format:"%A %d %B %Y"}</td>
          <td>
            {$curr_plage->debut|date_format:"%Hh%M"}
            &mdash
            {$curr_plage->fin|date_format:"%Hh%M"}
          </td>
          <td>{$curr_plage->libelle}</td>
          <td>{$curr_plage->tarif} €</td>
        </tr>
        {/foreach}
        <tr>
          <th colspan="2" />
          <th>Total</th>
          <td><strong>{$total} €</strong></td>
        </tr>
      </table>
    </td>
  </tr>
</table>