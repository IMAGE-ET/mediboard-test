<tr>
	<td>
	 {{if $object->initiateur_id}}
	   <img src="images/icons/prev.png" alt="&lt;" />
	 {{else}}
	   <img src="images/icons/next.png" alt="&gt;" />
	 {{/if}}
	</td>
	<td>
	  {{if $object->initiateur_id}} Not : {{/if}}
	  <button onclick="location.href='?m=sip&amp;tab=vw_idx_message_hprim&amp;message_hprim_id={{$object->_id}}'" class="search" type="button">
	   {{$object->message_hprim_id|str_pad:6:'0':STR_PAD_LEFT}}
	  </button>
	</td>
	<td>{{mb_value object=$object field="date_production"}}</td>
	<td>{{mb_value object=$object field="emetteur"}}</td>
	<td>{{mb_value object=$object field="destinataire"}}</td>
	<td>{{mb_value object=$object field="type"}}</td>
	<td>{{mb_value object=$object field="sous_type"}}</td>
	<td class="{{if $object->date_echange}}ok{{else}}warning{{/if}}">
	  {{if $object->initiateur_id}}
	    {{if $dPconfig.sip.server == "1"}}
	      <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}', 'notification')" type="button" style="float:right">Envoyer</button>
	    {{/if}}
	  {{else}}
      <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}', 'initiateur')" type="button" style="float:right">Envoyer</button>
    {{/if}}
	  <span>{{mb_value object=$object field="date_echange"}}</span>
	</td>
	<td class="{{if ($object->date_echange && !$object->acquittement)}}error{{/if}}">
	  {{if $object->acquittement}}Oui{{else}}Non{{/if}}
	</td>
</tr>