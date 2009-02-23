<table class="tbl">
  
  <tr>
    <th>Type</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Heure</th>
    
    <th colspan="3">Texte</th>
  </tr>
  
  {{assign var=date value=""}}
  
  {{foreach from=$sejour->_ref_suivi_medical item=curr_suivi}}
  <tr>
  {{if $curr_suivi->_class_name == "CObservationMedicale"}}
    <td><strong>Observation</strong></td>
    <td>
      <strong>
        <div class="mediuser" style="border-color: #{{$curr_suivi->_ref_user->_ref_function->color}};">
          {{$curr_suivi->_ref_user->_view}}
        </div>
      </strong>
    </td>
    
    <td  style="text-align: center">
      <strong>
        {{if $date != $curr_suivi->date|date_format:"%d/%m/%Y"}}
          {{$curr_suivi->date|date_format:"%d/%m/%Y"}}
        {{else}}
          &mdash;
        {{/if}}
      </strong>
    </td>
    <td>
      {{$curr_suivi->date|date_format:$dPconfig.time}}
    </td>
    
    <td class="text" colspan="2">
      
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
        <strong>{{$curr_suivi->text|nl2br}}</strong>
      </div>
    </td>
    <td class="button">
    {{if !$without_del_form}}
    {{if $curr_suivi->user_id == $app->user_id}}
      <form name="delObs{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="observation_medicale_id" value="{{$curr_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, '$prescription->_id')">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
      {{/if}}
    </td>
    </tr>
  {{/if}}
  {{if $curr_suivi->_class_name == "CTransmissionMedicale"}}
  <tr>
    <td>Transmission</td>
    <td>{{$curr_suivi->_ref_user->_view}}</td>
   <td  style="text-align: center">
      {{if $date != $curr_suivi->date|date_format:"%d/%m/%Y"}}
        {{$curr_suivi->date|date_format:"%d/%m/%Y"}}
      {{else}}
        &mdash;
      {{/if}}    
      </td>
      <td>
        {{$curr_suivi->date|date_format:$dPconfig.time}}
      </td>
    <td class="text" colspan="2">
     
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
	      {{if $curr_suivi->object_id}}
	      <em>Cible : {{$curr_suivi->_ref_object->_view}}</em><br />
	      {{/if}}
        {{$curr_suivi->text|nl2br}}
      </div>
    </td>
    <td class="button">
    {{if !$without_del_form}}
    {{if $curr_suivi->user_id == $app->user_id}}
      <form name="delTrans{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_transmission_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="transmission_medicale_id" value="{{$curr_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
      {{/if}}
    </td>
    </tr>
  {{/if}}
  {{assign var=date value=$curr_suivi->date|date_format:"%d/%m/%Y"}}
  {{/foreach}}
</table>