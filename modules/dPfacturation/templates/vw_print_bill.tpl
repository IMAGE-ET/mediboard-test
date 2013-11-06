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
    url.addElement(formBill.tiers_soldant);
    url.addElement(formBill.uniq_checklist);
    url.requestModal();
  }
  integrationComptable = function(){
    var form = document.printFrm;
    var formBill = document.printFacture;
    var url = new Url('facturation', 'vw_integration_compta');
    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.addElement(formBill.facture_class);
    url.requestModal();
  }
  reprint = function() {
    var form = document.reprintFacture;
    var url = new Url('facturation', 'ajax_print_journal');
    url.addElement(form.uniq_checklist);
    url.addElement(form.journal_id);
    url.requestUpdate(SystemMessage.id);
  }
  function checkList(definitive) {
    var formBill = document.printFacture;
    if(definitive == 0) {
      $('checklist_print').show();
    }
    else {
      $('checklist_print').hide();
      formBill.uniq_checklist.checked = false;
    }
  }
</script>
<table class="form">
  <tr>
    <td style="width: 33%;">
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
              <input type="radio" name="definitive" value="0" checked="checked" onchange="checkList(this.value);"/>
              <label for="definitive_0">Provisoire</label>
              <input type="radio" name="definitive" value="1" onchange="checkList(this.value);"/>
              <label for="definitive_1">Définitif</label>
            </td>
          </tr>
          <tr id="checklist_print">
            <th></th>
            <td>
              <label>
                <input type="checkbox" name="uniq_checklist" value="0"/>
                Uniquement liste de controle
              </label>
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
            <th></th>
            <td>
              <label>
                <input type="checkbox" name="tiers_soldant" value="0"/>
                Facture à rediriger au patient
              </label>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button class="print" type="button" onclick="printBill();">Impression</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td style="width: 33%;">
      <form name="reprintFacture" action="?" method="get">
        <table class="form">
          <tr>
            <th class="category" colspan="4">Réimpression de factures</th>
          </tr>
          <tr>
            <th style="width: 50%;">Journal</th>
            <td>

              <input type="hidden" name="journal_id" />
              <input type="text" name="name_journal" style="width: 13em;" value=""/>
              <script>
                Main.add(function () {
                  var form = getForm("reprintFacture");
                  var url = new Url("system", "ajax_seek_autocomplete");
                  url.addParam("object_class", "CJournalBill");
                  url.addParam("field", "journal_id");
                  url.addParam("view_field", "nom");
                  url.addParam("where[type]", "debiteur");
                  url.addParam("input_field", "name_journal");
                  url.autoComplete(form.elements.name_journal, null, {
                    minChars: 0,
                    method: "get",
                    select: "view",
                    dropdown: true,
                    afterUpdateElement: function(field,selected){
                      $V(field.form.journal_id, selected.getAttribute("id").split("-")[2]);
                      $V(field.form.elements.name_journal, selected.down('.view').innerHTML);
                    }
                  });
                });
              </script>
            </td>
          </tr>
          <tr>
            <th></th>
            <td>
              <label>
                <input type="checkbox" name="uniq_checklist" value="0"/>
                Uniquement liste de controle
              </label>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button class="print" type="button" onclick="reprint();">Réimpression</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td>
      <form name="OtherFacture" action="?" method="get">
        <table class="form">
          <tr>
            <th class="category" colspan="4">{{tr}}Other{{/tr}}</th>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button type="button" class="print" onclick="viewJournal('all-paiement');">{{tr}}CJournalBill.type.paiement-small{{/tr}}</button>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button type="button" class="print" onclick="integrationComptable();">Intégration comptable</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>