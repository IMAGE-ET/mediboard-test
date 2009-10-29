<table class="form">
  <tr>
    <th class="category">
      <button type="button" class="hslip notext" style="float:left" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
      Patient
    </th>
    <th class="category">{{tr}}COperation{{/tr}}</th>
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
      {{include file="../../dPcabinet/templates/inc_patient_infos.tpl"}}
    </td>
    <td class="text" id="consultAnesth">
      {{include file="../../dPcabinet/templates/inc_consult_anesth/interventions.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_history.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_medecins.tpl"}}
    </td>
  </tr>
</table>