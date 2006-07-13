<table class="form">
        <tr>
          <th class="category">
            Patient
          </th>
          <th class="category">Correspondants</th>
          <th class="category">
            <a style="float:right;" href="javascript:view_log('CConsultation',{{$consult->consultation_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Historique
          </th>
          <th class="category">Planification</th>
        </tr>
        <tr>
          <td class="text">
            {{include file="inc_patient_infos.tpl"}}
          </td>
          <td class="text">
            {{include file="inc_patient_medecins.tpl"}}
          </td>
          <td class="text">
            {{include file="inc_patient_history.tpl"}}
          </td>
          <td class="button">
            <button style="margin: 1px;" class="new" type="button" onclick="newOperation      ({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouvelle intervention</button>
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newHospitalisation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouveau séjour</button>
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newConsultation   ({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouvelle consultation</button>
          </td>
        </tr>
      </table>
