<table class="form">
  <tr>
    <th class="category">
      <button type="button" class="hslip notext" style="float:left" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
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
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_infos}}
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