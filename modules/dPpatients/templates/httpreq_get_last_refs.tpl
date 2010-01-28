<table width="100%">
  <tr>
    <td style="font-weight: bold;">
		  <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
		    <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
			   	{{$patient->_view}} &mdash; {{$patient->_age}} ans
			  </span>
			</a>
		</td>
  </tr>
  <tr>
    <td class="text">
    	<strong>Séjours: </strong>
			<ul>
    	{{foreach from=$patient->_ref_sejours item=curr_sejour}}
			  <li>
	        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}')">
	          {{$curr_sejour->_shortview}} 
					</span>
					<ul>
		      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
			      <li style="list-style-type: none;">
  			      <input type="radio" name="_operation_id" value="{{$curr_op->operation_id}}" {{if $curr_op->operation_id == $consultation->_ref_consult_anesth->operation_id}}checked="checked"{{/if}} />
  			      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
    			      Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
    			      avec le Dr {{$curr_op->_ref_chir->_view}} {{if $curr_op->annulee}}<span style="color: red;">[ANNULE]</span>{{/if}}
  			      </span>
						</li>
		      {{foreachelse}}
			      <li>{{tr}}COperation.none{{/tr}}</li>
		      {{/foreach}}
          
          {{foreach from=$curr_sejour->_ref_consultations item=_consult}}
            <li style="list-style-type: none;" class="iconed-text {{$_consult->_type}}">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              Consultation le {{$_consult->_datetime|date_format:"%d/%m/%Y"}}
              avec le Dr {{$_consult->_ref_chir->_view}} {{if $_consult->annule}}<span style="color: red;">[ANNULE]</span>{{/if}}
              </span>
            </li>
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
		      <li class="iconed-text {{$curr_consult->_type}}" >
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_consult->_guid}}')">
		        {{$curr_consult->_view}}
		        </span>
		      </li>
				{{foreachelse}}
				  <li>Aucune</li>
				{{/foreach}}
			</ul>
    </td>
  </tr>
</table>