<script>
  function viewJournal(type) {
    var oForm = document.printFrm;
    var url = new Url('facturation', 'vw_journal');
    url.addParam('type'    , type);
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addParam('prat_id' , oForm.chir.value);
    url.addParam('suppressHeaders' , '1');
    url.popup(1000, 600);
  }

  filesJournal = function() {
    var form = document.printFrm;
    var formFiles = document.printFiles;
    var url = new Url("facturation", "ajax_vw_files");
    url.addParam("type_journal"   , formFiles.type.value);
    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.requestUpdate('files_journaux');
  }
  Main.add(function() {
    filesJournal();
  });
</script>
<table class="form">
  <tr>
    <th class="category">
      Edition des journaux
    </th>
    <th class="title">
      Recherche de journaux
    </th>
  </tr>
  <tr>
    <td class="button" style="width:50%;">
      <button type="button" class="print" onclick="viewJournal('paiement');">{{tr}}CJournalBill.type.paiement{{/tr}}</button>
      <div class="small-info">
        Impression du journal des paiements
      </div>
    </td>
    <td class="button">
      <form class="form" name="printFiles">
        {{mb_field class=CJournalBill field="type"}}<br/>
        <button type="button" class="save" onclick="filesJournal();">{{tr}}Validate{{/tr}}</button>
      </form>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="button" class="print" onclick="viewJournal('debiteur');">{{tr}}CJournalBill.type.debiteur{{/tr}}</button>
      <div class="small-info">
        Impression du journal des débiteurs
      </div>
    </td>
    <td rowspan="2" id="files_journaux">
      {{mb_script module=cabinet     script=file ajax=true}}
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="button" class="print" onclick="viewJournal('rappel');">{{tr}}CJournalBill.type.rappel{{/tr}}</button>
      <div class="small-info">
        Impression du journal des rappels triés par ordre de statut
      </div>
    </td>
  </tr>
</table>