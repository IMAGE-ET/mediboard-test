<table class="tbl">
  <tr>
    <th colspan="4" class="title">
      Dossier de {{$consult->_view}}
    </th>
  </tr>
  
  <tr>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>{{tr}}Action{{/tr}}</th>
    <th>Propriétés</th>
  </tr>
  
  {{foreach from=$consult->_ref_logs item=curr_object}}
  <tr>
    <td>{{$curr_object->_ref_user->_view}}</td>
    <td>{{$curr_object->date|date_format:$dPconfig.datetime}}</td>
    <td>{{tr}}CUserLog.type.{{$curr_object->type}}{{/tr}}</td>
    <td>
      {{foreach from=$curr_object->_fields|smarty:nodefaults item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}

  {{if $consult->_ref_consult_anesth->consultation_anesth_id}}
  <tr>
    <th colspan="4" class="title">
      Consultation Anesthesique
    </th>
  </tr>  
  
  {{foreach from=$consult->_ref_consult_anesth->_ref_logs item=curr_object}}
  <tr>
    <td>{{$curr_object->_ref_user->_view}}</td>
    <td>{{$curr_object->date|date_format:$dPconfig.datetime}}</td>
    <td>{{tr}}CUserLog.type.{{$curr_object->type}}{{/tr}}</td>
    <td>
      {{foreach from=$curr_object->_fields|smarty:nodefaults item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>