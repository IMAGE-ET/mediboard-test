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
  <td class="narrow">
    <form name="delEchange" action = "" method="post">
      <input type="hidden" name="m" value="hprimxml" />
      <input type="hidden" name="dosql" value="do_echangehprim_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_key object=$object}}

      <button class="cancel notext" type="button" onclick="confirmDeletion(this.form, {
          typeName:'l\'échange',
          objName:'{{$object|smarty:nodefaults|JSAttribute}}'
        })">
      </button>
    </form>
  </td>
  <td class="narrow">
    <a href="?m=hprimxml&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->_id}}" class="button search">
     {{$object->echange_hprim_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </a>
  </td>
  {{if $dPconfig.sip.server}}
  <td class="narrow">
    {{if $object->initiateur_id}}
      <a href="?m=hprimxml&amp;tab=vw_idx_echange_hprim&amp;echange_hprim_id={{$object->initiateur_id}}" class="button search">
        {{$object->initiateur_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </a>
    {{/if}}
  </td>
  {{/if}}
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
  {{assign var=emetteur value=$object->_ref_emetteur}}
  <td class="{{if $object->emetteur_id == '0'}}error{{/if}} narrow">
     {{if $object->_self_emetteur}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$emetteur field="nom"}}
     {{/if}}
     {{if $object->identifiant_emetteur}}
      : {{$object->identifiant_emetteur|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
     {{/if}}
  </td>
  {{assign var=destinataire value=$object->_ref_destinataire}}
  <td class="narrow">
    {{if $object->_self_destinataire}}
     <label title='[SELF]' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$destinataire field="nom"}}
     {{/if}}
  </td>
  <td class="{{if $object->sous_type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$object field="sous_type"}}</td>
  <td class="{{if $object->date_echange}}ok{{else}}warning{{/if}} narrow">
    {{if $dPconfig.sip.server == "1"}}
      {{if $object->_self_emetteur}}
        <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}')" type="button" style="float:right">{{tr}}Send{{/tr}}</button>
      {{/if}}
    {{else}}
      {{if !($object->date_echange)}}
        <button class="change" onclick="sendMessage('{{$object->_id}}', '{{$object->_class_name}}')" type="button" style="float:right">{{tr}}Send{{/tr}}</button>
      {{/if}}
    {{/if}}
    <span>
      <label title='{{mb_value object=$object field="date_echange"}}'>
        {{mb_value object=$object field="date_echange" format=relative}}
      </label>
    </span>
  </td>
  <td class="narrow">
    <button class="change" onclick="reprocessing('{{$object->_id}}', '{{$object->_class_name}}')" type="button">{{tr}}Reprocess{{/tr}}</button>
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