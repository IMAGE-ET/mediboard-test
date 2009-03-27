<tr>
	<td>
	 {{if $object->initiateur_id}}
	   <img src="images/icons/prev.png" alt="&lt;" />
	 {{else}}
	   <img src="images/icons/next.png" alt="&gt;" />
	 {{/if}}
	</td>
	<td>
	  <button onclick="location.href='?m=sip&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->_id}}'" class="search" type="button">
	   {{$object->echange_hprim_id|str_pad:6:'0':STR_PAD_LEFT}}
	  </button>
	</td>
	<td>
	  {{if $object->initiateur_id}}
		  <button onclick="location.href='?m=sip&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->initiateur_id}}'" class="search" type="button">
	     {{$object->initiateur_id|str_pad:6:'0':STR_PAD_LEFT}}
	    </button>
    {{/if}}
	</td>
	<td>{{mb_value object=$object field="date_production"}}</td>
	<td>
	   {{if $object->_self_emetteur}}
	   <label title='{{mb_value object=$object field="emetteur"}}' style="font-weight:bold">
	     [SELF]
	   </label>
	   {{else}}
	     {{mb_value object=$object field="emetteur"}}
	   {{/if}}
	   {{if $object->identifiant_emetteur}}
	    : {{$object->identifiant_emetteur|str_pad:6:'0':STR_PAD_LEFT}}
	   {{/if}}
	</td>
	<td>
    {{if $object->_self_destinataire}}
     <label title='{{mb_value object=$object field="destinataire"}}' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$object field="destinataire"}}
     {{/if}}
  </td>
	<td>{{mb_value object=$object field="type"}}</td>
	<td>{{mb_value object=$object field="sous_type"}}</td>
	<td class="{{if $object->date_echange}}ok{{else}}warning{{/if}}">
	  {{if $object->initiateur_id}}
	    {{if $dPconfig.sip.server == "1"}}
	      <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}', 'notification')" type="button" style="float:right">Envoyer</button>
	    {{/if}}
	  {{else}}
	    {{if $dPconfig.mb_id == $object->emetteur}}
        <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}', 'initiateur')" type="button" style="float:right">Envoyer</button>
      {{/if}}
    {{/if}}
	  <span>{{mb_value object=$object field="date_echange"}}</span>
	</td>
	<td class="{{if ($object->date_echange && !$object->acquittement)}}error{{/if}}">
	  {{if $object->acquittement}}Oui{{else}}Non{{/if}}
	</td>
</tr>