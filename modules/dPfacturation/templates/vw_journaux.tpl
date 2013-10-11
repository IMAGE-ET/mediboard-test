<script>
  function viewTotaux(type) {
    var oForm = document.printFrm;
    var url = new Url('facturation', 'vw_journal');
    url.addParam('type'    , type);
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addParam('prat_id' , oForm.chir.value);
    url.addParam('suppressHeaders' , '1');
    url.popup(1000, 600);
  }
</script>
<table class="form">
  <tr>
    <th class="category" colspan="4">
      Journaux de relances
    </th>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="button" class="print" onclick="viewTotaux('paiement');">Journal des paiements</button>
      <div class="small-info">
        Impression du journal des paiements
      </div>
    </td>
    <td colspan="2" class="button">
      <button type="button" class="print" onclick="viewTotaux('debiteur');">Journal des débiteurs</button>
      <div class="small-info">
        Impression du journal des débiteurs
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="print" onclick="viewTotaux('rappel');">Journal des rappels (contentieux)</button>
      <div class="small-info">
        Impression du journal des rappels triés par ordre de statut
      </div>
    </td>
  </tr>
</table>