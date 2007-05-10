<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m=dPfacturation&amp;tab=vw_idx_facture&amp;facture_id=0">
        Créer une nouvelle facture
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Factures</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Nombre de lignes</th>
 		  <th>Montant</th>
        </tr>
        {{foreach from=$listFacture item=curr_facture}}
        <tr {{if $curr_facture->_id == $facture->_id}}class="selected"{{/if}}>
          <td>
            <a href="index.php?m=dPfacturation&amp;tab=vw_idx_facture&amp; title="Modifier la facture">
              {{$curr_facture->_view}}
            </a>
          </td>
          <td>{{$curr_facture->_ref_items|@count}}</td>
          <td>{{$curr_facture->_total}}</td>
        </tr>
        {{/foreach}}
      </table>
  </td>
  </tr>
  </table>