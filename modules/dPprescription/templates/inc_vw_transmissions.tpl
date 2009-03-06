<table class="tbl">
  {{if $ajax || $dialog}}
  <tr>
    <th class="title" colspan="6">Transmissions et observations</th>
  </tr>
  {{/if}}
  <tr>
    <th>Patient</th>
    <th>Type</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Heure</th>
    <th>Texte</th>
  </tr>
  {{assign var=date value=""}}
  {{foreach from=$trans_and_obs item=_objects_by_date}}
	  {{foreach from=$_objects_by_date item=_object}}
	    
		  <tr class="{{$_object->_ref_sejour->_ref_patient->_id}} trans_or_obs">
		    <td>{{$_object->_ref_sejour->_ref_patient->_view}}</td>
			  {{if $_object->_class_name == "CObservationMedicale"}}
			    <td><strong>Observation</strong></td>
			    <td>
			      <strong>
			        <div class="mediuser" style="border-color: #{{$_object->_ref_user->_ref_function->color}};">
			          {{$_object->_ref_user->_view}}
			        </div>
			      </strong>
			    </td>
			    <td  style="text-align: center">
			      <strong>
			        {{if $date != $_object->date|date_format:"%d/%m/%Y"}}
			          {{$_object->date|date_format:"%d/%m/%Y"}}
			        {{else}}
			          &mdash;
			        {{/if}}
			      </strong>
			    </td>
			    <td>
			      {{$_object->date|date_format:$dPconfig.time}}
			    </td>
			    <td class="text" colspan="2">
			      <div {{if $_object->degre == "high"}}style="background-color: #faa"{{/if}}>
			        <strong>{{$_object->text|nl2br}}</strong>
			      </div>
			    </td>
			  {{/if}}
			  
			  {{if $_object->_class_name == "CTransmissionMedicale"}}
			    <td>Transmission</td>
			    <td>{{$_object->_ref_user->_view}}</td>
			    <td style="text-align: center">
			      {{if $date != $_object->date|date_format:"%d/%m/%Y"}}
			        {{$_object->date|date_format:"%d/%m/%Y"}}
			      {{else}}
			        &mdash;
			      {{/if}}    
			      </td>
			      <td>
			        {{$_object->date|date_format:$dPconfig.time}}
			      </td>
			    <td class="text" colspan="2">
			      <div {{if $_object->degre == "high"}}style="background-color: #faa"{{/if}}>
				      {{if $_object->object_id}}
				      <em>Cible : {{$_object->_ref_object->_view}}</em><br />
				      {{/if}}
			        {{$_object->text|nl2br}}
			      </div>
			    </td>
			  {{/if}}
		  
		    {{assign var=date value=$_object->date|date_format:"%d/%m/%Y"}}
		    </tr>
	{{/foreach}}
{{foreachelse}}
<tr>
  <td colspan="6">Aucune transmission</td>
</tr>
{{/foreach}}
</table>