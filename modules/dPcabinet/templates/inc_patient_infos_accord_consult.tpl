<table class="form" style="table-layout: fixed;">
  <tr>
    <th class="category">
      {{mb_include module=system template=inc_object_notes object=$patient}}
      {{mb_include module=dPpatients template=inc_view_ins_patient patient=$patient}}

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

      Dossier patient
    </th>
    <th class="category">
      Correspondants
    </th>
    <th class="category">
      {{mb_include module=system template=inc_object_idsante400 object=$consult}}
      {{mb_include module=system template=inc_object_history    object=$consult}}
      Historique
    </th>
  </tr>
  
  <tr>
    <td class="button">
      <div>{{mb_include module=cabinet  template=inc_patient_infos}}</div>
      <div>{{mb_include module=patients template=inc_patient_planification patient_id=$patient->_id praticien_id=$userSel->_id}}</div>
    </td>
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_medecins}}
    </td>
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_history}}
    </td>
  </tr>
</table>
