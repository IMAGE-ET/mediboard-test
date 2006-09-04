<table class="tbl">
  <tr>
    <th colspan="4" class="title">
      Dossier de {{$patient->_view}}
    </th>
  </tr>
  <tr>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Action</th>
    <th>Propriétés</th>
  </tr>
  {{foreach from=$patient->_ref_logs item=curr_object}}
  <tr>
    <td>{{$curr_object->_ref_user->_view}}</td>
    <td>{{$curr_object->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
    <td>{{$curr_object->type}}</td>
    <td>
      {{foreach from=$curr_object->_fields item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}

  {{if $patient->_ref_consultations}}  
  <tr>
    <th colspan="4" class="title">
      Consultations
    </th>
  </tr> 
  {{foreach from=$patient->_ref_consultations item=curr_object}}
  {{if $curr_object->_ref_logs}}
  <tr>
    <th colspan="4" class="category">
      <strong>Consultation du {{$curr_object->_ref_plageconsult->date|date_format:"%A %d %b %Y"}} par le Dr. {{$curr_object->_ref_plageconsult->_ref_chir->_view}}</strong> 
    </th>
  </tr>
  {{foreach from=$curr_object->_ref_logs item=curr_log}}
  <tr>
    <td>{{$curr_log->_ref_user->_view}}</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
    <td>{{$curr_log->type}}</td>
    <td>
      {{foreach from=$curr_log->_fields item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  {{/foreach}} 
  {{/if}}
  
  {{if $patient->_ref_sejours}}
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
  {{foreach from=$curr_object->_ref_logs item=curr_log}}
  <tr>
    <td>{{$curr_log->_ref_user->_view}}</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
    <td>{{$curr_log->type}}</td>
    <td>
      {{foreach from=$curr_log->_fields item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{foreach from=$curr_object->_ref_operations item=curr_operation}}
  <tr>
    <th colspan="4" class="category">
      {{$curr_operation->_view}} le {{$curr_operation->_datetime|date_format:"%d/%m/%Y"}}
    </th>
  </tr> 
  {{foreach from=$curr_operation->_ref_logs item=curr_log}}
  <tr>
    <td>{{$curr_log->_ref_user->_view}}</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
    <td>{{$curr_log->type}}</td>
    <td>
      {{foreach from=$curr_log->_fields item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{foreach from=$curr_object->_ref_affectations item=curr_affect}}
  <tr>
    <th colspan="4" class="category">
      Affectation du {{$curr_affect->entree|date_format:"%d/%m/%Y"}} au {{$curr_affect->sortie|date_format:"%d/%m/%Y"}}
    </th>
  </tr> 
  {{foreach from=$curr_affect->_ref_logs item=curr_log}}
  <tr>
    <td>{{$curr_log->_ref_user->_view}}</td>
    <td>{{$curr_log->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
    <td>{{$curr_log->type}}</td>
    <td>
      {{foreach from=$curr_log->_fields item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}  
  {{/foreach}}
  {{/if}}
</table>