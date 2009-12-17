{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr class="{{if array_key_exists($field, $unequal)}}unequal{{else}}equal{{/if}}"
    style="{{if !array_key_exists($field, $unequal)}}display: none;{{/if}}">
  <th>{{mb_label object=$result field=$field}}</th>
  {{assign var=result_ref value=$result->_fwd.$field}}

  {{foreach from=$objects item=object name=object}}
    {{assign var=ref value=$object->_fwd.$field}}
    <td class="{{$object->_props.$field}}">
      <label>
        <input type="radio" name="_choix_{{$field}}" value="{{$object->$field}}" 
               {{if $smarty.foreach.object.first || $result->$field == $object->$field}}checked="checked"{{/if}}
               onclick="$V(this.form.{{$field}}, '{{$object->$field}}');$V(this.form._{{$field}}_view, '{{$ref->_view|smarty:nodefaults|JSAttribute}}')" />
        {{if $object->$field != null}}
          {{$ref->_view}}
        {{else}}
          <span style="opacity: 0.3">Non spécifié</span>
        {{/if}}
      </label>
    </td>
  {{/foreach}}
  
  <td>
    {{mb_field object=$result field=$field hidden=1 prop=""}}
    <input type="text" readonly="readonly" size="30" name="_{{$field}}_view" value="{{$result_ref->_view}}" />
  </td>
</tr>
