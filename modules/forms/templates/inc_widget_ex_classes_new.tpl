{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button class="forms" {{if $ex_objects|@count == 0}}disabled{{/if}} onclick="ObjectTooltip.createDOM(this, $(this).next(), {duration: 0});">
  Form. ({{$count_available}})
</button>

<table class="layout" style="border: 1px solid #000; display: none;">
{{foreach from=$ex_objects key=_ex_class_id item=_ex_objects}}
  <tr>
    <th style="text-align: right; font-weight: bold; vertical-align: middle;">{{$ex_classes.$_ex_class_id->name}}</th>
    <td style="text-align: left;">
      {{foreach from=$_ex_objects item=_ex_object}}
        {{if $_ex_object->_id}}
          <button class="edit" title="{{mb_value object=$_ex_object->_ref_last_log field=user_id}}"
            onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event_name}}', '{{$_element_id}}')">
            {{mb_value object=$_ex_object->_ref_last_log field=date}}
          </button>
        {{else}}
          <button class="new" value="{{$_ex_class_id}}" onclick="selectExClass(this, '{{$object->_guid}}', '{{$event_name}}', '{{$_element_id}}')">
            Nouveau
          </button>
        {{/if}}
      {{/foreach}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td class="empty" colspan="2">{{tr}}CExClass.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>
