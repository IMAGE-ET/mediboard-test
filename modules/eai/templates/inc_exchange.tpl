{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object}}
<tr>
  <td colspan="21" class="empty">
    {{tr}}CExchangeDataFormat.none{{/tr}}
  </td>
</tr> 
{{else}}
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
      <button class="change" type="button" {{if $object->reprocess >= $conf.eai.max_reprocess_retries}}disabled{{/if}} 
        onclick="ExchangeDataFormat.reprocessing('{{$object->_guid}}')" 
        title="{{tr}}Reprocess{{/tr}} ({{$object->reprocess}}/{{$conf.eai.max_reprocess_retries}} fois)">
          {{if $object->reprocess}}{{$object->reprocess}}{{/if}}
      </button>
    {{/if}}
    {{if $object->_self_sender}}
      <button class="send notext" onclick="ExchangeDataFormat.sendMessage('{{$object->_guid}}')" 
        type="button" title="{{tr}}Send{{/tr}}">
      </button>
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
  <td class="{{if $object->sender_id == '0'}}error{{/if}} narrow text">
     {{if $object->_self_sender}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$emetteur->_guid}}">
         {{$emetteur->_view}}
       </a>
     {{/if}}
  </td>
  {{assign var=destinataire value=$object->_ref_receiver}}
  <td class="narrow text">
    {{if $object->_self_receiver}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$destinataire->_guid}}">
         <span onmouseover="ObjectTooltip.createEx(this, '{{$destinataire->_guid}}');">
           {{$destinataire->_view}}
         </span>
       </a>
     {{/if}}
  </td>
  <td class="{{if $object->type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$object field="type"}}</td>
  <td class="{{if $object->sous_type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$object field="sous_type"}}</td>
  {{if $object instanceof CExchangeHL7v2}}
    <td class="narrow">{{mb_value object=$object field="code"}}</td>
  {{/if}}
  {{if $object instanceof CExchangeHL7v2 || $object instanceof CEchangeHprim21}}
    <td class="narrow">{{mb_value object=$object field="version"}}</td>
  {{/if}}
  <td class="{{if $object->date_echange}}ok{{else}}warning{{/if}} narrow">
    <label title='{{mb_value object=$object field="date_echange"}}'>
      {{mb_value object=$object field="date_echange" format=relative}}
    </label>
  </td>
  {{assign var=statut_acq value=$object->statut_acquittement}}
  <td class="{{if !$statut_acq && $object->_self_sender}}
               hatching
             {{elseif !$statut_acq || 
                      ($statut_acq == 'erreur') || 
                      ($statut_acq == 'AR') || 
                      ($statut_acq == 'err')}}
               error 
             {{elseif ($statut_acq == 'avertissement') || 
                      ($statut_acq == 'avt') || 
                      ($statut_acq == 'AE')}}
               warning
             {{/if}} 
             narrow">
    {{mb_value object=$object field="statut_acquittement"}}
  </td>
  <td class="narrow {{if !$object->_observations}}warning{{/if}}">
    {{foreach from=$object->_observations item=_observation}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
         {{$_observation.code}}
       </span>
    {{/foreach}}
  </td>
  <td class="{{if !$object->message_valide}}error{{/if}} narrow">
    <a target="_blank" href="?m=eai&a=download_exchange&exchange_guid={{$object->_guid}}&dialog=1&suppressHeaders=1&message=1" 
      class="button modify notext"></a>
  </td>
  <td class="{{if !$statut_acq && $object->_self_sender}}hatching{{elseif !$object->acquittement_valide}}error{{/if}} narrow">
    {{if $object->_acquittement}}
      <a target="_blank" href="?m=eai&a=download_exchange&exchange_guid={{$object->_guid}}&dialog=1&suppressHeaders=1&ack=1" 
        class="button modify notext"></a>
    {{/if}}
  </td>
</tr>
{{/if}}