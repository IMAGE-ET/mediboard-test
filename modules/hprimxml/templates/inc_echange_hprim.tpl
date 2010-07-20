{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
	<td>
	 {{if $object->_self_emetteur}}
	   <img src="images/icons/prev.png" alt="&lt;" />
	 {{else}}
	   <img src="images/icons/next.png" alt="&gt;" />
	 {{/if}}
	</td>
	<td style="width:0.1%">
	  <a href="?m=hprimxml&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->_id}}" class="button search">
	   {{$object->echange_hprim_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
	  </a>
	</td>
  {{if $dPconfig.sip.server}}
	<td style="width:0.1%">
	  {{if $object->initiateur_id}}
	    <a href="?m=hprimxml&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->initiateur_id}}" class="button search">
        {{$object->initiateur_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
	    </a>
    {{/if}}
	</td>
  {{/if}}
  <td style="width:0.1%">
    {{$object->object_class}}
  </td>
	<td style="width:0.1%">
	  {{if $object->object_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->object_class}}-{{$object->object_id}}');">
        {{$object->object_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </span>
    {{/if}}
	</td>
	<td style="width:0.1%">
	  {{if $object->id_permanent}}
  	  <span onmouseover="ObjectTooltip.createEx(this, '{{$object->object_class}}-{{$object->object_id}}', 'identifiers');">
        {{$object->id_permanent|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </span>
    {{/if}}
	</td>
	<td style="width:0.1%">
	  <label title='{{mb_value object=$object field="date_production"}}'>
      {{mb_value object=$object field="date_production" format=relative}}
    </label>
	</td>
	<td {{if $object->emetteur == "inconnu"}}class="error"{{/if}} style="width:0.1%">
	   {{if $object->_self_emetteur}}
	   <label title='{{mb_value object=$object field="emetteur"}}' style="font-weight:bold">
	     [SELF]
	   </label>
	   {{else}}
	     {{mb_value object=$object field="emetteur"}}
	   {{/if}}
	   {{if $object->identifiant_emetteur}}
	    : {{$object->identifiant_emetteur|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
	   {{/if}}
	</td>
	<td style="width:0.1%">
    {{if $object->_self_destinataire}}
     <label title='{{mb_value object=$object field="destinataire"}}' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$object field="destinataire"}}
     {{/if}}
  </td>
	<td {{if $object->sous_type == "inconnu"}}class="error"{{/if}} style="width:0.1%">{{mb_value object=$object field="sous_type"}}</td>
	<td class="{{if $object->date_echange}}ok{{else}}warning{{/if}}" style="width:0.1%">
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
  <td style="width:0.1%">
    <button class="change" onclick="reprocessing('{{$object->_id}}', '{{$object->_class_name}}')" type="button">Retraiter</button>
  </td>
	<td class="{{if !$object->statut_acquittement || 
	                ($object->statut_acquittement == 'erreur') || 
                  ($object->statut_acquittement == 'err')}}error
             {{elseif ($object->statut_acquittement == 'avertissement') || 
                      ($object->statut_acquittement == 'avt')
             }}warning{{/if}}" style="width:0.1%">
    {{mb_value object=$object field="statut_acquittement"}}
  </td>
  <td>
    {{foreach from=$object->_observations item=_observation}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
         {{$_observation.code}}
       </span>
    {{/foreach}}
  </td>
  <td class="{{if !$object->message_valide}}error{{/if}}">
   {{mb_value object=$object field="message_valide"}}
  </td>
  <td>
    <a target="blank" href="?m=hprimxml&a=download_echange&echange_hprim_id={{$object->_id}}&dialog=1&suppressHeaders=1&message=1" class="button modify notext"></a>
  </td>
  <td class="{{if !$object->acquittement_valide}}error{{/if}}">
   {{mb_value object=$object field="acquittement_valide"}}
  </td>
  <td>
    {{if $object->_acquittement}}
      <a target="blank" href="?m=hprimxml&a=download_echange&echange_hprim_id={{$object->_id}}&dialog=1&suppressHeaders=1&ack=1" class="button modify notext"></a>
    {{/if}}
  </td>
  
</tr>