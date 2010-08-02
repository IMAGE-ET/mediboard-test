<script type="text/javascript">

function syncParentRegime() {
  oForm = document.forms['regimes'];
  window.opener.syncRegimes($V(oForm.hormone_croissance), $V(oForm.repas_sans_sel), $V(oForm.repas_sans_porc), $V(oForm.repas_diabete), $V(oForm.repas_sans_residu))
}

</script>

<form name="regimes">
<table class="form">
  <tr>
    <th colspan="2" class="title">
      Veuillez noter d'éventuels régimes alimentaires particulier pour le patient
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
    <td>{{mb_field object=$sejour field="hormone_croissance" onchange="syncParentRegime()"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="repas_sans_sel"}}</th>
    <td>{{mb_field object=$sejour field="repas_sans_sel" onchange="syncParentRegime()"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="repas_sans_porc"}}</th>
    <td>{{mb_field object=$sejour field="repas_sans_porc" onchange="syncParentRegime()"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
    <td>{{mb_field object=$sejour field="repas_diabete" onchange="syncParentRegime()"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
    <td>{{mb_field object=$sejour field="repas_sans_residu" onchange="syncParentRegime()"}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="tick" onclick="window.close();">{{tr}}Validate{{/tr}}</button>
    </td>
  </tr>
</table>
</form>