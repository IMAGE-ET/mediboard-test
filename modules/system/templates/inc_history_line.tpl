{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$logs item=_log}}
<tbody class="hoverable">
	
	<tr {{if $_log->type != "store"}} style="font-weight: bold" {{/if}}>
	  {{assign var=field_count value=$_log->_fields|@count}}
	  {{if !$dialog}}
	  <td rowspan="{{$field_count}}">
	  	<label title="{{$_log->object_class}}">
	  		{{tr}}{{$_log->object_class}}{{/tr}}
	  	</label>
	  	({{$_log->object_id}})
	  </td>
	  <td rowspan="{{$field_count}}" class="text">
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
	  <td rowspan="{{$field_count}}">{{mb_value object=$_log field=ip_address}}</td>
	  {{/if}}
	  <td rowspan="{{$field_count}}" style="text-align: center;">
	    <label onmouseover="ObjectTooltip.createEx(this, '{{$_log->_ref_user->_guid}}');">
	      {{mb_ditto name=user value=$_log->_ref_user->_view}}
	    </label>
	  </td>
	  <td rowspan="{{$field_count}}" style="text-align: center;">
	    {{mb_ditto name=date value=$_log->date|date_format:$dPconfig.date}}
	  </td>
	  <td rowspan="{{$field_count}}" style="text-align: center;">
	    {{mb_ditto name=time value=$_log->date|date_format:$dPconfig.time}}
	  </td>
	  <td rowspan="{{$field_count}}" {{if $_log->type != "store"}} colspan="4" {{/if}}>{{mb_value object=$_log field=type}}</td>
		
    <!-- Valeurs de champs-->
    {{if $object->_id}}
	    {{foreach from=$_log->_fields item=_field name=field}}
			  <td class="text">
			  	{{mb_label object=$object field=$_field}}
				</td>
	
	      {{if array_key_exists($_field,$_log->_old_values)}}
		      <td class="text">
		      	{{assign var=old_value value=$_log->_old_values.$_field}}
						{{mb_value object=$object field=$_field value=$old_value}}
		      </td>
		      <td class="text">
   		      {{assign var=log_id value=$_log->_id}}
            {{assign var=new_value value=$object->_history.$log_id.$_field}}
            <strong>
            	{{mb_value object=$object field=$_field value=$new_value}}
						</strong>
		      </td>
			  {{else}}
				  <td colspan="3"><em>{{tr}}Unavailable information{{/tr}}</em></td>
	      {{/if}}
	    {{if !$smarty.foreach.field.last}}</tr><tr>{{/if}}
	    {{/foreach}}
		{{else}}
      <td class="text">
      {{foreach from=$_log->_fields item=_field name=field}}
        {{mb_label class=$_log->object_class field=$_field}}
        {{if !$smarty.foreach.field.last}} - {{/if}}
			{{/foreach}}
      </td>

    {{/if}}

	</tr>
</tbody>
{{foreachelse}}
<tr>
  <td colspan="20">{{tr}}CUserLog.none{{/tr}}</td>
</tr>
{{/foreach}}
