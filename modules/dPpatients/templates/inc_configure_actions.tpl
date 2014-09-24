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
  },

  patientState : function (action) {
    var state = $$("input:checked[type=radio][name=state]")[0].value;
    new Url("dPpatients", "ajax_patient_state_tools")
      .addParam("action", action)
      .addParam("state", state)
      .requestUpdate("result_tools_patient_state");
  }
};
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
  <tr>
    <td>
      <label><input type="radio" name="state" value="PROV" checked>{{tr}}CPatient.status.PROV{{/tr}}</label>
      <label><input type="radio" name="state" value="VALI">{{tr}}CPatient.status.VALI{{/tr}}</label><br/>
      <button type="button" class="search" onclick="Actions.patientState('verifyStatus')">
        Vérifier le nombre de patient sans statut
      </button>
      <button type="button" class="send" onclick="Actions.patientState('createStatus')">
        Placer le statut provisoire pour les patients sans statut
      </button></td>
    <td id="result_tools_patient_state"></td>
  </tr>
</table>