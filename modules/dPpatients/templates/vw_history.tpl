<table class="tbl">
  <tr>
    <th colspan="3" class="title">
      Dossier de {{$patient->_view}}
    </th>
  </tr>
  <tr>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Action</th>
  </tr>
  {{foreach from=$patient->_ref_logs item=curr_object}}
  <tr>
    <td>{{$curr_object->_ref_user->_view}} ({{$curr_object->user_id}})</td>
    <td>{{$curr_object->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_object->type}}</td>
  </tr>
  {{/foreach}}
</table>

{{if $patient->_ref_consultations}}
<table class="tbl">
  <tr>
    <th colspan="4" class="title">
      Consultations
    </th>
  </tr>
  <tr>
    <th>Consultation</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Action</th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=curr_object}}
  {{foreach from=$curr_object->_ref_logs item=curr_log}}
  <tr>
    <td class="text">du {{$curr_object->_ref_plageconsult->date|date_format:"%A %d %b %Y"}} par le Dr. {{$curr_object->_ref_plageconsult->_ref_chir->_view}}</td>
    <td>{{$curr_log->_ref_user->_view}} ({{$curr_log->user_id}})</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_log->type}}</td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>
{{/if}}

{{if $patient->_ref_sejours}}
<table class="tbl">
  <tr>
    <th colspan="4" class="title">
      Séjours
    </th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_object}}
  <tr>
    <th colspan="4" class="category">
      <strong>{{$curr_object->_view}}</strong> 
    </th>
  </tr>
  <tr>
    <th></th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Action</th>
  </tr>
  {{foreach from=$curr_object->_ref_logs item=curr_log}}
  <tr>
    <td class="text">{{$curr_object->_view}} ({{$curr_object->sejour_id}})</td>
    <td>{{$curr_log->_ref_user->_view}} ({{$curr_log->user_id}})</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_log->type}}</td>
  </tr>
  {{/foreach}}
  
  {{foreach from=$curr_object->_ref_operations item=curr_operation}}
  {{foreach from=$curr_operation->_ref_logs item=curr_log}}
  <tr>
    <td class="text">{{$curr_operation->_view}} le {{$curr_operation->_datetime|date_format:"%d/%m/%Y"}} ({{$curr_operation->operation_id}})</td>
    <td>{{$curr_log->_ref_user->_view}} ({{$curr_log->user_id}})</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_log->type}}</td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  
  {{foreach from=$curr_object->_ref_affectations item=curr_affect}}
  {{foreach from=$curr_affect->_ref_logs item=curr_log}}
  <tr>
    <td class="text">Affectation du {{$curr_affect->entree|date_format:"%d/%m/%Y"}} au {{$curr_affect->sortie|date_format:"%d/%m/%Y"}} ({{$curr_affect->affectation_id}})</td>
    <td>{{$curr_log->_ref_user->_view}} ({{$curr_log->user_id}})</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_log->type}}</td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  
  {{/foreach}}
</table>
{{/if}}