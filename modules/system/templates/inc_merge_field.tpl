{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=spec value=$result->_specs.$field}}
{{assign var=prop value=$result->_props.$field}}
{{assign var=status value=$statuses.$field}}

<tr class="{{$status}}" style="{{if $status != "multiple"}}display: none;{{/if}}">
  <th>{{mb_label object=$result field=$field}}</th>
  
  <!-- Result field -->
  <td class="{{$result->_props.$field}}">
    {{assign var=onchange value="ObjectMerger.updateOptions(this)"}}
    {{if $spec instanceof CDateSpec || $spec instanceof CTimeSpec || $spec instanceof CDateTimeSpec}}
      {{mb_field object=$result field=$field form="form-merge" register=true onchange=$onchange}}
    {{elseif $spec instanceof CEnumSpec || $spec instanceof CBoolSpec}}
      {{mb_field object=$result field=$field defaultOption="&mdash; Non spécifié" onchange=$onchange}}
    {{elseif $spec instanceof CRefSpec}}
      {{mb_field object=$result field=$field hidden=1}}
      <input type="text" readonly="readonly" size="30" name="_{{$field}}_view" value="{{$result->_fwd.$field}}" onchange="{{$onchange}}" />
    {{else}}
      {{mb_field object=$result field=$field onchange=$onchange}}
    {{/if}}
  </td>

  <!-- All options -->
  {{foreach from=$objects item=object name=object}}
  <td class="{{$prop}}">
  	{{assign var=iteration value=$smarty.foreach.object.iteration}}
    <label for="_choix_{{$iteration}}_{{$field}}">
      <input type="radio" name="_choix_{{$field}}" id="form-merge__choix_{{$iteration}}_{{$field}}"
         value="{{$object->$field}}"
         {{if $result->$field == $object->$field}}checked="checked"{{/if}}
         onclick="ObjectMerger.setField('{{$field}}', this);" />
      {{if $object->$field !== ""}}
        {{if $spec instanceof CTextSpec}}
  				{{$object->$field|nl2br}}
        {{elseif $spec instanceof CRefSpec}}
				  {{assign var=ref value=$object->_fwd.$field}}
				  {{if $ref->_id}} 
          <span onmouseover="ObjectTooltip.createEx(this, '{{$ref->_guid}}')">
            {{$ref}}
          </span>
          {{/if}}
				{{else}}
          {{mb_value object=$object field=$field}}
				{{/if}}
      {{else}}
        Non spécifié
      {{/if}}
    </label>
  </td>
  {{/foreach}}
  
</tr>