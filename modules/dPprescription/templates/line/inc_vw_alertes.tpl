{{if !$line->_protocole}}
{{assign var="color" value=#ccc}}
{{assign var=code_cip value=$line->code_cip}}

{{if (array_key_exists($code_cip, $prescription_reelle->_alertes.allergie) ||
     array_key_exists($code_cip, $prescription_reelle->_alertes.interaction) ||
     array_key_exists($code_cip, $prescription_reelle->_alertes.profil) ||
     array_key_exists($code_cip, $prescription_reelle->_alertes.IPC))}}
  {{if array_key_exists($code_cip, $prescription_reelle->_alertes.profil) ||
       array_key_exists($code_cip, $prescription_reelle->_alertes.IPC)}}
    {{assign var="image" value="note_orange.png"}}
    {{assign var="color" value=#fff288}}
  {{/if}}  
  {{if array_key_exists($code_cip, $prescription_reelle->_alertes.allergie) ||
       array_key_exists($code_cip, $prescription_reelle->_alertes.interaction)}}
    {{assign var="image" value="note_red.png"}}
    {{assign var="color" value=#ff7474}}
  {{/if}}  
  <img src="images/icons/{{$image}}" title="" alt="" 
       onmouseover="$('line-{{$line->_guid}}').show();"
       onmouseout="$('line-{{$line->_guid}}').hide();" />
{{/if}}

<div id="line-{{$line->_guid}}" class="tooltip" style="text-align: left; display: none; background-color: {{$color}}; border-style: ridge; padding-right:5px; ">
	{{foreach from=$prescription_reelle->_alertes key=type item=curr_type}}
	  {{if array_key_exists($code_cip, $curr_type)}}
	    <ul>
	    {{foreach from=$curr_type.$code_cip item=_alerte}}
	      <li>
	        <strong>{{tr}}CPrescriptionLineMedicament-alerte-{{$type}}-court{{/tr}} :</strong>
	        {{$_alerte}}
	      </li>
	    {{/foreach}}
	    </ul>
	  {{/if}}
	{{/foreach}}
</div>
{{/if}}