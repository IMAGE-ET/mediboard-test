<script>
var Actions = {
  civilite: function(mode) {
    if (mode == "repair") {
      if (!confirm("Etes-vous sur de vouloir réparer les civilités ?")) {
        return;
      }
    }
    var url = new Url("dPpatients", "ajax_civilite");
    url.addParam("mode", mode);
    url.requestUpdate("ajax_civilite");
  }
}
editAntecedent = function(mode) {
  if (mode == "repair") {
    if (!confirm("Etes-vous sur de vouloir réparer les dossier médicaux ?")) {
      return;
    }
  }
  var url = new Url('patients', 'ajax_check_dossier');
  url.addParam("mode", mode);
  url.requestUpdate("ajax_check_dossier");
}
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
  <tr>
    <th style="width: 50%">{{tr}}Action{{/tr}}</th>
    <th style="width: 50%">{{tr}}Status{{/tr}}</th>
  </tr>
    
  <tr>
    <td>
      <button class="search" onclick="Actions.civilite('check')">
        Vérifier les civilités
      </button>
      <br />
      <button class="change" onclick="Actions.civilite('repair')">
        Corriger les civilités
      </button>
    </td>
    <td id="ajax_civilite">
    </td>
  </tr>
  <tr>
    <td>
      <button class="search" type="button" onclick="editAntecedent('check');">Vérifier les dossiers médicaux</button>
      <button class="change" type="button" onclick="editAntecedent('repair');">Corriger les dossiers médicaux</button>
    </td>
    <td id="ajax_check_dossier"></td>
  </tr>
</table>