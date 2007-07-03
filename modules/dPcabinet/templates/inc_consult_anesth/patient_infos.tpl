<table class="form">
  <tr>
    <th class="category">
      <button class="hslip notext" id="listConsult-trigger" style="float:left">Programme</button>
      Patient
    </th>
    <th class="category">Intervention</th>
    <th class="category">
      <a style="float:right;" href="#" onclick="view_history_consult({{$consult->consultation_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Historique
    </th>
    <th class="category">Correspondants</th>
  </tr>
  <tr>
    <td class="text">
      {{include file="inc_patient_infos.tpl"}}
    </td>
    <td class="text" id="consultAnesth">
      {{include file="inc_consult_anesth/interventions.tpl"}}
    </td>
    <td class="text">
      {{include file="inc_patient_history.tpl"}}
    </td>
    <td class="text">
      {{include file="inc_patient_medecins.tpl"}}
    </td>
  </tr>
</table>