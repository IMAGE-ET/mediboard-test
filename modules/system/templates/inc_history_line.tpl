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
  	{{assign var=object value=$_log->_ref_object}}
  	{{if $object->_id}} 
      <label onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
        {{$object}}
      </label>
  	{{else}}
		  {{$object}}
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
      <label title="{{$curr_field}}{{if isset($_log->_old_values.$curr_field|smarty:nodefaults)}} - {{$_log->_old_values.$curr_field}}{{/if}}">
        {{tr}}{{$_log->object_class}}-{{$curr_field}}{{/tr}}
      </label>
      {{if !$smarty.foreach.field.last}},{{/if}}
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
