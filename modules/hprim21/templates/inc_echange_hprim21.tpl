{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td style="width:0.1%">
    <form name="delEchange" action="" method="post">
      <input type="hidden" name="m" value="hprim21" />
      <input type="hidden" name="dosql" value="do_echangehprim21_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_key object=$object}}
      <button class="cancel notext" onclick="confirmDeletion(this.form, {
          typeName:'l\'échange',
          objName:'{{$object|smarty:nodefaults|JSAttribute}}'
        })">
      </button>
    </form>
  </td>
  <td style="width:0.1%">
    <a href="?m=hprim21&amp;tab=vw_idx_echange_hprim21&amp;echange_hprim21_id={{$object->_id}}" class="button search">
     {{$object->echange_hprim21_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </a>
  </td>
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
  <td style="width:0.1%">
    {{$object->version}}
  </td>
  <td style="width:0.1%">
    {{$object->type_message}}
  </td>
  <td>
     {{mb_value object=$object field="emetteur_desc"}} ({{mb_value object=$object field="id_emetteur"}})
  </td>
  <td>
    {{mb_value object=$object field="destinataire_desc"}}
  </td>
  <td class="{{if $object->date_echange}}ok{{else}}warning{{/if}}">
    <span>
      <label title='{{mb_value object=$object field="date_echange"}}'>
        {{mb_value object=$object field="date_echange" format=relative}}
      </label>
    </span>
  </td>
  <td style="width:0.1%">
    <button class="change" onclick="reprocessing('{{$object->_id}}', '{{$object->_class_name}}')" type="button">{{tr}}Reprocess{{/tr}}</button>
  </td>
  <td class="{{if !$object->message_valide}}error{{/if}}">
   {{mb_value object=$object field="message_valide"}}
  </td>
  <td style="width:0.1%">
    <a target="blank" href="?m=hprim21&a=download_echange&echange_hprim21_id={{$object->_id}}&dialog=1&suppressHeaders=1" class="button modify notext"></a>
  </td>
</tr>