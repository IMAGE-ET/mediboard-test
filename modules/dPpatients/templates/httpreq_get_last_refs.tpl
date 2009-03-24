<table width="100%">
  <tr>
    <td style="font-weight: bold;">
		  <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"
			   class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
			   	{{$patient->_view}} &mdash; {{$patient->_age}} ans
			</a>
		</td>
  </tr>
  <tr>
    <td class="text">
    	<strong>Séjours: </strong>
			<ul>
    	{{foreach from=$patient->_ref_sejours item=curr_sejour}}
			  <li>
	        <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}')">
		        du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
		        au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
					</span>
					<ul>
		      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
			      <li class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')" style="list-style-type: none;">
			      <input type="radio" name="_operation_id" value="{{$curr_op->operation_id}}" {{if $curr_op->operation_id == $consultation->_ref_consult_anesth->operation_id}}checked="checked"{{/if}} />
			      Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
			      avec le Dr {{$curr_op->_ref_chir->_view}} {{if $curr_op->annulee}}<span style="color: red;">[ANNULE]</span>{{/if}}
			      {{*if $curr_op->_ext_codes_ccam|@count || $curr_op->libelle}}
				      <ul>
				        {{if $curr_op->libelle}}
				        <li><em>[{{$curr_op->libelle}}]</em></li>
				        {{/if}}
				        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
				        <li>{{$curr_code->libelleLong}}</li>
				        {{/foreach}}
				      </ul>
			      {{/if*}}
						</li>
		      {{foreachelse}}
			      <li>Aucune intervention</li>
		      {{/foreach}}
					</ul>
				</li>
			{{foreachelse}}
			  <li>Aucun</li>
	    {{/foreach}}
			</ul>
    </td>
  </tr>
  
  <tr>
    <td class="text">
    	<strong>Consultations:</strong>
			<ul>
	    	{{foreach from=$patient->_ref_consultations item=curr_consult}}
		      <li class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_consult->_guid}}')">le {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}} avec le Dr {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</li>
				{{foreachelse}}
				  <li>Aucune</li>
				{{/foreach}}
			</ul>
    </td>
  </tr>
</table>