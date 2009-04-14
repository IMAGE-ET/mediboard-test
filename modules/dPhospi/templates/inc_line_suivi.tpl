<tr>
{{if $_suivi->_class_name == "CObservationMedicale"}}
  {{if @$show_patient}}
  <td><strong>{{$_suivi->_ref_sejour->_ref_patient}}</strong></td>
{{/if}}
  
  <td><strong>{{tr}}{{$_suivi->_class_name}}{{/tr}}</strong></td>
  
  <td>
    <strong>
      <div class="mediuser" style="border-color: #{{$_suivi->_ref_user->_ref_function->color}};">
        {{$_suivi->_ref_user}}
      </div>
    </strong>
  </td>
  
  <td  style="text-align: center">
    <strong>
      {{mb_ditto name=date value=$_suivi->date|date_format:$dPconfig.date}}
    </strong>
  </td>
  
  <td>{{$_suivi->date|date_format:$dPconfig.time}}</td>
  
  <td class="text" colspan="2"
    {{if $_suivi->degre == "high"}}style="background-color: #faa"{{/if}} 
    {{if $_suivi->degre == "info"}}style="background-color: #aaf"{{/if}} 
  >
    <div>
      <strong>{{mb_value object=$_suivi field=text}}</strong>
    </div>
  </td>

  {{if !$without_del_form}}
  <td class="button">
  {{if $_suivi->user_id == $app->user_id}}
    <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_observation_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
      <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
      <button type="button" class="trash notext" onclick="submitSuivi(this.form, '$prescription->_id')">{{tr}}Delete{{/tr}}</button>
    </form>
    {{/if}}
  </td>
  {{/if}}
</tr>
{{/if}}

{{if $_suivi->_class_name == "CTransmissionMedicale"}}
<tr>
  {{if @$show_patient}}
  <td>{{$_suivi->_ref_sejour->_ref_patient}}</td>
{{/if}}

  <td>{{tr}}{{$_suivi->_class_name}}{{/tr}}</td>
  
  <td>{{$_suivi->_ref_user}}</td>
  
  <td  style="text-align: center">
    {{mb_ditto name=date value=$_suivi->date|date_format:$dPconfig.date}}
  </td>
  
  <td>{{$_suivi->date|date_format:$dPconfig.time}}</td>
  
  <td class="text" colspan="2" {{if $_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
    <div>
     {{if $_suivi->object_id}}
     <em>Cible : {{$_suivi->_ref_object}}</em><br />
     {{/if}}
      {{mb_value object=$_suivi field=text}}
    </div>
  </td>
  
  {{if !$without_del_form}}
  <td class="button">
  {{if $_suivi->user_id == $app->user_id}}
    <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
      <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
      <button type="button" class="trash notext" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Delete{{/tr}}</button>
    </form>
    {{/if}}
  </td>
  {{/if}}
  
</tr>
{{/if}}

