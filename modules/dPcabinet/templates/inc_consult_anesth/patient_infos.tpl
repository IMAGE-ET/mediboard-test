<table class="form">
  <tr>
    <th class="category">
      <button type="button" class="hslip notext" style="float: left;" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
      <!-- Modification de la fiche patient -->
      <script>
        function editPatient(patient_id) {
          var url = new Url('patients', 'vw_edit_patients');
          url.addParam("patient_id", patient_id);
          url.redirect();
        }

      </script>
      <a class="action" style="float: right" title="Modifier la fiche" href="" onclick="editPatient('{{$patient->_id}}');">
        <img src="images/icons/edit.png" alt="modifier" />
      </a>

      Patient
    </th>
    <th class="category">{{tr}}CConsultAnesth{{/tr}}</th>
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
      <div>{{mb_include module=patients template=inc_patient_planification}}</div>
    </td>
    <td class="text" id="consultAnesth">
      {{mb_include module=cabinet template=inc_consult_anesth/interventions}}
    </td>
    <td>
      {{mb_include module=cabinet template=inc_patient_history}}
    </td>
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_medecins}}
    </td>
  </tr>
</table>