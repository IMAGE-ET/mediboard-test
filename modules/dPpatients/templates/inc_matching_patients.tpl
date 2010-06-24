<script type="text/javascript">
  function mergeMatchingPatients(){
    var url = new Url("dPpatients", "ajax_merge_matching_patients");
    url.addParam("do_merge", $('do_merge').checked ? 1 : 0);
    url.requestUpdate("matching-patients-messages");
  }
</script>

<table class="form">
  <tr>
    <th colspan="2" class="category">
      Fusion de masse de patients identiques
    </th>
  </tr>
  <tr>
    <td>
      <button type="button" class="change" onclick="mergeMatchingPatients()">
        Chercher les patients identiques
      </button>
      <label><input type="checkbox" id="do_merge"/> {{tr}}Merge{{/tr}} </label>
    </td>
    <td style="width: 60%" id="matching-patients-messages"></td>
  </tr>
</table>
