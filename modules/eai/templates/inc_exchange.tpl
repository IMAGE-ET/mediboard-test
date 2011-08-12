{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td class="narrow">
   {{if $object->_self_sender}}
     <img src="images/icons/prev.png" alt="&lt;" />
   {{else}}
     <img src="images/icons/next.png" alt="&gt;" />
   {{/if}}
   {{if $object->_delayed}}
     <label title='{{$object->_delayed}} min' style="font-weight:bold">
       <img src="images/icons/hourglass.png" alt="Retard" />
     </label>
   {{/if}}
  </td>
  <td class="narrow">
    <form name="del{{$object->_guid}}" action="" method="post">
      {{mb_class object=$object}}
      {{mb_key object=$object}}
      <input type="hidden" name="del" value="1" />
    
      <button class="cancel notext" type="button" onclick="confirmDeletion(this.form, {
          ajax:1, 
          typeName:&quot;{{tr}}{{$object->_class}}.one{{/tr}}&quot;,
          objName:&quot;{{$object->_view|smarty:nodefaults|JSAttribute}}&quot;},
          { onComplete: ExchangeDataFormat.refreshExchangesList.curry(getForm('filterExchange'))
        })">
      </button>
    </form>
    {{if $object->_self_receiver}}
      <button class="change notext" onclick="ExchangeDataFormat.reprocessing('{{$object->_guid}}')" type="button">{{tr}}Reprocess{{/tr}}</button>
    {{/if}}
    {{if $object->_self_sender}}
      <button class="send notext" onclick="ExchangeDataFormat.sendMessage('{{$object->_guid}}')" type="button">{{tr}}Reprocess{{/tr}}</button>
    {{/if}}
  </td>
  <td class="narrow">
    <button type="button" onclick="ExchangeDataFormat.viewExchange('{{$object->_guid}}')" class="search">
     {{$object->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </button>
  </td>
  <td class="narrow">
    {{$object->object_class}}
  </td>
  <td class="narrow">
    {{if $object->object_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->object_class}}-{{$object->object_id}}');">
        {{$object->object_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </span>
    {{/if}}
  </td>
  <td class="narrow">
    {{if $object->id_permanent}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->object_class}}-{{$object->object_id}}', 'identifiers');">
        {{$object->id_permanent|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </span>
    {{/if}}
  </td>
  <td class="narrow">
    <label title='{{mb_value object=$object field="date_production"}}'>
      {{mb_value object=$object field="date_production" format=relative}}
    </label>
  </td>
  {{assign var=emetteur value=$object->_ref_sender}}
  <td class="{{if $object->sender_id == '0'}}error{{/if}} narrow">
     {{if $object->_self_sender}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$emetteur->_guid}}">
         {{mb_value object=$emetteur field="nom"}}
       </a>
     {{/if}}
  </td>
  {{assign var=destinataire value=$object->_ref_receiver}}
  <td class="narrow">
    {{if $object->_self_receiver}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$destinataire->_guid}}">
         {{mb_value object=$destinataire field="nom"}}
       </a>
     {{/if}}
  </td>
  <td class="{{if $object->type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$object field="type"}}</td>
  <td class="{{if $object->sous_type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$object field="sous_type"}}</td>
  <td class="{{if $object->date_echange}}ok{{else}}warning{{/if}} narrow">
    <span>
      <label title='{{mb_value object=$object field="date_echange"}}'>
        {{mb_value object=$object field="date_echange" format=relative}}
      </label>
    </span>
  </td>
  <td class="{{if !$object->statut_acquittement || 
                  ($object->statut_acquittement == 'erreur') || 
                  ($object->statut_acquittement == 'err')}}error
             {{elseif ($object->statut_acquittement == 'avertissement') || 
                      ($object->statut_acquittement == 'avt')
             }}warning{{/if}} narrow">
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
  <td class="narrow {{if !$object->message_valide}}error{{/if}}">
    <a target="blank" href="?m=eai&a=download_exchange&exchange_guid={{$object->_guid}}&dialog=1&suppressHeaders=1&message=1" 
      class="button modify notext"></a>
  </td>
  <td class="{{if !$object->acquittement_valide}}error{{/if}}">
   {{mb_value object=$object field="acquittement_valide"}}
  </td>
  <td class="narrow {{if !$object->acquittement_valide}}error{{/if}}">
    {{if $object->_acquittement}}
      <a target="blank" href="?m=eai&a=download_exchange&exchange_guid={{$object->_guid}}&dialog=1&suppressHeaders=1&ack=1" 
        class="button modify notext"></a>
    {{/if}}
  </td>
</tr>