<script>
  seeDblActes = function(see, form){
    $('resultDblActes').show();
    var url = new Url('facturation', 'ajax_corrected_fact');
    url.addParam('see', see);
    url.addElement(form.debut);
    url.addElement(form.fin);
    url.requestUpdate("resultDblActes");
  }
  Main.add(function () {
    Calendar.regField(getForm("choicedates").debut);
    Calendar.regField(getForm("choicedates").fin);
  });
</script>

<form name="choicedates" action="" method="get">
  <table class="tbl">
    <tr>
      <th colspan="2">Actions</th>
    </tr>
    <tr>
      <td>
        Depuis le <input type="hidden" name="debut" value="" /> <br/>
        Jusqu'au <input type="hidden" name="fin"   value="" /> <br/>
        <button class="search" type="button" onclick="seeDblActes(1, this.form)">Voir les lignes de factures en trop</button><br/>
        <button class="submit" type="button" onclick="seeDblActes(0, this.form)">Supprimer ces lignes</button>
      </td>
      <td>
        <div class="small-info" id="resultDblActes" style="display: none;"></div>
      </td>
    </tr>
  </table>
</form>