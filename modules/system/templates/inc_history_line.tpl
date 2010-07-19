{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$logs item=_log}}
<tr {{if $_log->type != "store"}}style="font-weight: bold"{{/if}}>
  {{if !$dialog}}
  <td>
  	<label title="{{$_log->object_class}}">
  		{{tr}}{{$_log->object_class}}{{/tr}}
  	</label>
  	({{$_log->object_id}})
  </td>
  <td class="text">
  	{{assign var=ref_object value=$_log->_ref_object}}
  	{{if $ref_object->_id}} 
      <label onmouseover="ObjectTooltip.createEx(this, '{{$ref_object->_guid}}');">
        {{$ref_object}}
      </label>
  	{{else}}
		  {{$ref_object}}
      {{if $_log->extra}}
        - {{$_log->extra}}
      {{/if}}
  	{{/if}}
  </td>
  {{/if}}
  <td style="text-align: center;">
    <label onmouseover="ObjectTooltip.createEx(this, '{{$_log->_ref_user->_guid}}');">
      {{mb_ditto name=user value=$_log->_ref_user->_view}}
    </label>
  </td>
  <td style="text-align: center;">
    {{mb_ditto name=date value=$_log->date|date_format:$dPconfig.date}}
  </td>
  <td style="text-align: center;">
    {{mb_ditto name=time value=$_log->date|date_format:$dPconfig.time}}
  </td>
  <td>{{mb_value object=$_log field=type}}</td>
  <td class="text">
    {{foreach from=$_log->_fields item=curr_field name=field}}
      {{if $object->_id}}
      <label title="{{$curr_field}}{{if isset($_log->_old_values.$curr_field|smarty:nodefaults)}} - {{$_log->_old_values.$curr_field}}{{/if}}">
        {{tr}}{{$_log->object_class}}-{{$curr_field}}{{/tr}}
      </label>
      {{if array_key_exists($curr_field,$_log->_old_values)}}
      :
      {{assign var=_old_value value=$_log->_id}}
      de <strong>'{{$_log->_old_values.$curr_field}}'</strong>
      {{assign var=log_id value=$_log->_id}}
      à <strong>'{{$object->_history.$log_id.$curr_field}}'</strong>
      {{/if}}
      {{if !$smarty.foreach.field.last}}<br />{{/if}}
      {{else}}
      <label title="{{$curr_field}}{{if isset($_log->_old_values.$curr_field|smarty:nodefaults)}} - {{$_log->_old_values.$curr_field}}{{/if}}">
        {{tr}}{{$_log->object_class}}-{{$curr_field}}{{/tr}}
      </label>
      {{if !$smarty.foreach.field.last}},{{/if}}
      {{/if}}
    {{/foreach}}
  </td>
  
  {{if !$dialog}}
    <td>{{mb_value object=$_log field=ip_address}}</td>
  {{/if}}
</tr>
{{foreachelse}}
<tr>
  <td colspan="20">{{tr}}CUserLog.none{{/tr}}</td>
</tr>
{{/foreach}}
