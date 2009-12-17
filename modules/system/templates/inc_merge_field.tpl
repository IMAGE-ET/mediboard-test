{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=specs value=$result->_specs}}
{{assign var=specType value=$specs.$field->getSpecType()}}

<tr class="{{if array_key_exists($field, $unequal)}}unequal{{else}}equal{{/if}}"
    style="{{if !array_key_exists($field, $unequal)}}display: none;{{/if}}">
  <th>{{mb_label object=$result field=$field}}</th>
  
  {{foreach from=$objects item=object name=object}}
  <td class="{{$object->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" 
             value="{{if $specType != "enum" && $specType != "bool" && $specType != "text"}}{{mb_value object=$object field=$field}}{{else}}{{$object->$field}}{{/if}}" 
             {{if $smarty.foreach.object.first || $result->$field == $object->$field}}checked="checked"{{/if}}
             onclick="setField('{{$field}}', $V(this), this.form);" />
      {{if $object->$field != null}}
        {{mb_value object=$object field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  {{/foreach}}
  
  <td class="{{$result->_props.$field}}">
    {{mb_field object=$result field=$field form="form-merge" register=true defaultOption="- Non spécifié -"}}
  </td>
</tr>