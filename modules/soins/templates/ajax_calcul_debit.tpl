<script type="text/javascript">
  var getDebit = function() {
    var oForm = getForm("calcul_debit");
    var fDebitProduit           = $V(oForm.debit_produit);
    var sUniteDebitProduit      = $V(oForm.unite_debit_produit);
    var sUniteDebitProduitTemps = $V(oForm.unite_debit_produit_temps);
    var fVolumeTotal            = $V(oForm.volume_total);
    var fQuantiteProduit        = $V(oForm.quantite_produit);
    
    if(fDebitProduit && fVolumeTotal && fQuantiteProduit) {
      fDebitProduit = sUniteDebitProduit      == 'g'  ? fDebitProduit * 1000 : fDebitProduit;
      fDebitProduit = sUniteDebitProduit      == 'µg' ? fDebitProduit / 1000 : fDebitProduit;
      fDebitProduit = sUniteDebitProduitTemps == 'mn' ? fDebitProduit * 60   : fDebitProduit;
      var fResultat = (fVolumeTotal * fDebitProduit) / fQuantiteProduit;
      $V(oForm.resultat, fResultat.toFixed(2));
    } else {
      alert('Certains éléments sont manquant, merci de les remplir')
    }
  }
</script>

<form name="calcul_debit" action="?" method="get" onsubmit="getDebit(); return false;">
  <table class="form">
    <tr>
      <th class="title" colspan="3">Calcul de débits</th>
    </tr>
    <tr>
      <th class="category">Donnée</th>
      <th class="category">Valeur</th>
      <th class="category greedyPane">Unitée</th>
    </tr>
    <tr>
      <th>Débit</th>
      <td><input type="text" size="6" name="debit_produit" value="" /></td>
      <td>
        <select name="unite_debit_produit">
          <option value="µg">µg</option>
          <option value="mg" selected="selected">mg</option>
          <option value="g">g</option>
        </select>
        par
        <select name="unite_debit_produit_temps">
          <option value="h" selected="selected">heure</option>
          <option value="mn">minute</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>Volume total</th>
      <td><input type="text" readonly="readonly" size="6" name="volume_total" value="{{$volume_total}}" /></td>
      <td>ml</td>
    </tr>
    <tr>
      <th>Quantité de produit</th>
      <td><input type="text" readonly="readonly" size="6" name="quantite_produit" value="{{$quantite_produit}}" /></td>
      <td class="text">mg de {{$line_item->_view}}</td>
    </tr>
    <tr>
      <th><button type="submit" class="search">Resultat</button></th>
      <td><input type="text" readonly="readonly" size="6" name="resultat" value="" /></td>
      <td>ml par heure</td>
    </tr>
  </table>
</form>
