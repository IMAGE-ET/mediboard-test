<script>
  seeDblActes = function(see){
    $('resultDblActes').show();
    var url = new Url('facturation', 'ajax_corrected_fact');
    url.addParam('see', see);
    url.requestUpdate("resultDblActes");
  }
</script>
<table class="tbl">
  <tr>
    <th colspan="2">Actions</th>
  </tr>
  <tr>
    <td>
      <button class="search" type="button" onclick="seeDblActes(1)">Voir les lignes de factures en trop</button><br/>
      <button class="submit" type="button" onclick="seeDblActes(0)">Supprimer ces lignes</button>
    </td>
    <td>
      <div class="small-info" id="resultDblActes" style="display: none;"></div>
    </td>
  </tr>
</table>
