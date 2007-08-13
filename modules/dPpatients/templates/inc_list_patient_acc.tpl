<table class="form">
    <tr>
    <td>
    {{mb_ternary var="tabPatient" test=$board 
                     value="vw_full_patients&patient_id=" 
                     other="vw_idx_patients&patient_id="}}
    {{foreach from=$tab_recherche item=curr_patient}}
        <tr>
          <td class="text">
            <a href="?m=dPpatients&tab={{$tabPatient}}{{$curr_patient->patient_id}}">
              {{mb_value object=$curr_patient field="_view"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&tab={{$tabPatient}}{{$curr_patient->patient_id}}">
              {{mb_value object=$curr_patient field="naissance"}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPpatients&tab={{$tabPatient}}{{$curr_patient->patient_id}}">
              {{mb_value object=$curr_patient field="adresse"}}
              {{mb_value object=$curr_patient field="cp"}}
              {{mb_value object=$curr_patient field="ville"}}
            </a>
          </td>
        </tr>
    {{/foreach}} 
    </td>
    </tr>
</table>
