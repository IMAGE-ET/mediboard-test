<script>
  printBill = function(){
    var form = document.printFrm;
    var formBill = document.printFacture;
    var url = new Url('facturation', 'ajax_print_bill');
    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.addParam('prat_id' , form.chir.value);
    url.addParam('definitive' , $V(formBill.definitive));
    url.addElement(formBill.facture_class);
    url.addElement(formBill.tri);
    url.addElement(formBill.facture_id);
    url.addElement(formBill.type_fact);
    url.requestModal();
  }
</script>

<form name="printFacture" action="?" method="get">
  <table class="form">
    <tr>
      <th class="category" colspan="4">Impression de factures</th>
    </tr>
    <tr {{if !$conf.dPfacturation.CFactureEtablissement.view_bill || !$conf.dPfacturation.CFactureCabinet.view_bill}} style="display:none;" {{/if}}>
      <th>
        <label for="facture_class">Type de facture</label>
      </th>
      <td>
        <select name="facture_class">
          {{if $conf.dPfacturation.CFactureCabinet.view_bill}}
            <option value="CFactureCabinet">{{tr}}CFactureCabinet{{/tr}}</option>
          {{/if}}
          {{if $conf.dPfacturation.CFactureEtablissement.view_bill}}
            <option value="CFactureEtablissement">{{tr}}CFactureEtablissement{{/tr}}</option>
          {{/if}}
        </select>
      </td>
    </tr>
    <tr>
      <th style="width:50%;"></th>
      <td>
        <input type="radio" name="definitive" value="0" checked="checked"/>
        <label for="definitive_0">Provisoire</label>
        <input type="radio" name="definitive" value="1" />
        <label for="definitive_1">Définitif</label>
      </td>
    </tr>
    <tr>
      <th>Ordonné par</th>
      <td>
        <select name="tri">
          <option value="nom_patient">Nom de patient</option>
          <option value="num_fact" selected="selected">Numéro de facture</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>Type de facture</th>
      <td>
        <select name="type_fact">
          <option value="" selected="selected">Toutes</option>
          <option value="patient">Facture patient</option>
          <option value="garant">Facture garant</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>Numéro de facture</th>
      <td>
        <input type="" name="facture_id" value=""/>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="print" type="button" onclick="printBill();">Impression</button>
      </td>
    </tr>
  </table>
</form>