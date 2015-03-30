<table class="form">
  <tr>
    <th class="category">
      <button type="button" class="hslip notext" style="float: left;" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
      <!-- Modification de la fiche patient -->
      <script>
        function editPatient(patient_id) {
          var url = new Url('patients', 'vw_edit_patients', 'tab');
          url.addParam("patient_id", patient_id);
          url.addParam("modal", 1);
          url.modal({
            width: "95%",
            height: "95%"
          });
        }
      </script>
      <a class="action" style="float: right" title="Modifier la fiche" href="#" onclick="editPatient('{{$patient->_id}}');">
        <img src="images/icons/edit.png" alt="modifier" />
      </a>
      Patient
    </th>
    <th class="category">Dossier d'anesthésie</th>
    <th class="category">
      <a style="float:right;" href="#" onclick="view_history_consult({{$consult->_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Historique
    </th>
    <th class="category">{{tr}}CPatient-back-correspondants{{/tr}}</th>
  </tr>
  <tr>
    <td class="button">
      <div>{{mb_include module=cabinet  template=inc_patient_infos}}</div>
      <div>
        {{mb_include module=patients template=inc_patient_planification
          patient_id=$patient->_id
          consult=$consult
          praticien_id=$consult->_ref_plageconsult->chir_id}}
      </div>
    </td>
    <td class="text" id="consultAnesth"></td>
    <td>
      {{mb_include module=cabinet template=inc_patient_history}}
    </td>
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_medecins}}
    </td>
  </tr>
</table>