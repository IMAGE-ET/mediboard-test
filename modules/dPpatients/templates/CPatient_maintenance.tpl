<script type="text/javascript">

  function purgePatients() {
    var url = new Url("dPpatients", "ajax_purge_patients");
    url.addParam("qte", 5);
    url.requestUpdate("purge_patients", repeatPurge);
  }

  function repeatPurge() {
    if($V($("check_repeat_purge"))) {
      purgePatients();
    }
  }

</script>

<h2>Actions sur les patients</h2>

<table class="tbl">
  <tr>
    <th class="section" style="width: 50%">{{tr}}Action{{/tr}}</th>
    <th class="section">{{tr}}Status{{/tr}}</th>
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
      <label><input type="radio" name="state" value="PROV" checked> {{tr}}CPatient.status.PROV{{/tr}}</label>
      <label><input type="radio" name="state" value="VALI"> {{tr}}CPatient.status.VALI{{/tr}}</label><br/>
      <button type="button" class="search" onclick="Actions.patientState('verifyStatus')">
        Vérifier le nombre de patients sans statut
      </button>
      <button type="button" class="send" onclick="Actions.patientState('createStatus')">
        Placer le statut provisoire pour les patients sans statut
      </button></td>
    <td id="result_tools_patient_state"></td>
  </tr>
</table>

<h2>Purge des patients</h2>

<div class="small-error">
  La purge des patients est une action irreversible qui supprime aléatoirement
  une partie des dossiers patients de la base de données et toutes les données
  qui y sont associées.
  <strong>
    N'utilisez cette fonctionnalité que si vous savez parfaitement ce que vous faites
  </strong>
</div>
<table class="tbl">
  <tr>
    <th>
      Purge des patients (par 5)
      <button type="button" class="tick" onclick="purgePatients();">
        GO
      </button>
      <br />
      <input type="checkbox" name="repeat_purge" id="check_repeat_purge"/> Relancer automatiquement
    </th>
  </tr>
  <tr>
    <td id="purge_patients">
      <div class="small-info">{{$nb_patients}} patients dans la base</div>
    </td>
  </tr>
</table>