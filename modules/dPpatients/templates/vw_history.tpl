<table class="tbl">
  <tr>
    <th colspan="10" class="title">
      Dossier de {{$patient->_view}}
    </th>
  </tr>

  <tr>
    <th>{{mb_title class=CUserLog field=user_id}}</th>
    <th colspan="2">{{mb_title class=CUserLog field=date}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
  </tr>
  
  {{include file=../../system/templates/inc_history_line.tpl logs=$patient->_ref_logs}}

  {{if $patient->_ref_consultations}}  
  <tr>
    <th colspan="10" class="title">
      Consultations
    </th>
  </tr> 
  
  {{foreach from=$patient->_ref_consultations item=_consult}}
  {{if $_consult->_ref_logs}}
  <tr>
    <th colspan="10" class="category">
      <strong>
      	Consultation du {{mb_value object=$_consult field=_date}} 
      	par le Dr {{$_consult->_ref_plageconsult->_ref_chir->_view}}
      </strong> 
    </th>
  </tr>
  
  {{include file=../../system/templates/inc_history_line.tpl logs=$_consult->_ref_logs}}
  {{/if}}
  {{/foreach}} 

  {{/if}}
  
  {{if $patient->_ref_sejours}}
  <tr>
    <th colspan="10" class="title">
      Séjours
    </th>
  </tr>  
  {{foreach from=$patient->_ref_sejours item=curr_object}}
  <tr>
    <th colspan="10" class="category">
      <strong>{{$curr_object->_view}}</strong> 
    </th>
  </tr>  

  {{include file=../../system/templates/inc_history_line.tpl logs=$curr_object->_ref_logs}}

  {{foreach from=$curr_object->_ref_operations item=curr_operation}}
  <tr>
    <th colspan="10" class="category">
      {{$curr_operation->_view}} 
      le {{$curr_operation->_datetime|date_format:$dPconfig.date}}
    </th>
  </tr> 

  {{include file=../../system/templates/inc_history_line.tpl logs=$curr_operation->_ref_logs}}
  {{/foreach}}

  {{foreach from=$curr_object->_ref_affectations item=curr_affect}}
  <tr>
    <th colspan="10" class="category">
      Affectation 
      du {{mb_value object=$curr_affect field=entree}}
		  au {{mb_value object=$curr_affect field=sortie}}
    </th>
  </tr> 

  {{include file=../../system/templates/inc_history_line.tpl logs=$curr_affect->_ref_logs}}

  {{/foreach}}  
  {{/foreach}}
  {{/if}}
</table>