<table class="form">
  <tr>
    <th class="category">Patient</th>
    <th class="category">Informations</th>
    <th class="category">Correpondants</th>
    <th class="category">
      <a style="float:right;" href="javascript:view_log('CConsultation',{{$consult->consultation_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Historique
    </th>
  </tr>
  <tr>
    <td class="text">
      {{include file="inc_patient_infos.tpl"}}
    </td>
    <td class="text" id="consultAnesth">
      {{include file="inc_vw_consult_anesth.tpl"}}
    </td>
    <td class="text">
      {{include file="inc_patient_medecins.tpl"}}
    </td>
    <td class="text">
      {{include file="inc_patient_history.tpl"}}
    </td>
  </tr>
</table>