<tr {{if $object->emetteur == "inconnu"}}class="error"{{/if}}">
	<td>
	 {{if $object->_self_emetteur}}
	   <img src="images/icons/prev.png" alt="&lt;" />
	 {{else}}
	   <img src="images/icons/next.png" alt="&gt;" />
	 {{/if}}
	</td>
	<td>
	  <a href="?m=sip&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->_id}}'" class="buttonsearch">
	   {{$object->echange_hprim_id|str_pad:6:'0':STR_PAD_LEFT}}
	  </a>
	</td>
	<td>
	  {{if $object->initiateur_id}}
	    <a href="?m=sip&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->initiateur_id}}'" class="buttonsearch">
        {{$object->initiateur_id|str_pad:6:'0':STR_PAD_LEFT}}
	    </a>
    {{/if}}
	</td>
	<td>
	  {{if @$object->_patient_ipp}}
	    <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$object->_patient_id}}" class="buttonsearch">
	      {{$object->_patient_ipp|str_pad:6:'0':STR_PAD_LEFT}}
	    </a>
    {{/if}}
	</td>
	<td>
	  <label title='{{mb_value object=$object field="date_production"}}'>
      {{mb_value object=$object field="date_production" format=relative}}
    </label>
	</td>
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
	      <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}')" type="button" style="float:right">Envoyer</button>
	    {{/if}}
	  {{else}}
	    {{if !$object->date_echange || ($dPconfig.sip.server == "1")}}
		    {{if $dPconfig.mb_id == $object->emetteur}}
	        <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}')" type="button" style="float:right">Envoyer</button>
	      {{/if}}
      {{/if}}
    {{/if}}
	  <span>
	    <label title='{{mb_value object=$object field="date_echange"}}'>
        {{mb_value object=$object field="date_echange" format=relative}}
      </label>
	  </span>
	</td>
	<td class="{{if ($object->date_echange && !$object->acquittement)}}error{{/if}}">
	  {{if $object->acquittement}}Oui{{else}}Non{{/if}}
	</td>
</tr>