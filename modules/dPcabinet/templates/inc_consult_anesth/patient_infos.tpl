{{assign var=multiple_dossiers_anesth value=$conf.dPcabinet.CConsultAnesth.multiple_dossiers_anesth}}

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
    <td class="text" {{if $multiple_dossiers_anesth}}rowspan="2"{{/if}}>
      {{mb_include module=cabinet template=inc_patient_infos}}
    </td>
    <td class="text" id="consultAnesth" {{if $multiple_dossiers_anesth}}rowspan="2"{{/if}}>
      {{mb_include module=cabinet template=inc_consult_anesth/interventions}}
    </td>
    <td>
      {{mb_include module=cabinet template=inc_patient_history}}
    </td>
    <td class="text">
      {{mb_include module=cabinet template=inc_patient_medecins}}
    </td>
  </tr>
  {{if $multiple_dossiers_anesth}}
    <tr>
      <td colspan="2" id="dossiers_anesth_area">
        {{mb_include module=cabinet template=inc_multi_consult_anesth}}
      </td>
    </tr>
  {{/if}}
</table>