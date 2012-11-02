{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=CMbObject_view}}

{{assign var=log value=$object}}

{{if $log->_old_values}}
<table class="main tbl">
  <tr>
    <th>{{tr}}Field{{/tr}}</th>
    <th>Valeur avant</th>
    <th>Valeur après</th>
  </tr>
  {{foreach from=$log->_old_values item=_field key=_field}}
    <tr>
      <td>
        {{mb_label object=$log->_ref_object field=$_field}}
      </td>

      {{if array_key_exists($_field,$log->_old_values)}}
        <td>
          {{assign var=old_value value=$log->_old_values.$_field}}
          {{mb_value object=$log->_ref_object field=$_field value=$old_value tooltip=1}}
        </td>
        <td>
          {{assign var=log_id value=$log->_id}}
          {{assign var=new_value value=$log->_ref_object->_history.$log_id.$_field}}
          <strong>
            {{mb_value object=$log->_ref_object field=$_field value=$new_value tooltip=1}}
          </strong>
        </td>
      {{else}}
        <td colspan="2" class="empty">{{tr}}Unavailable information{{/tr}}</td>
      {{/if}}
    </tr>
  {{/foreach}}

  <tr>
  	<td class="button" colspan="3">
			<form name="process-{{$log->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this)">
				<input type="hidden" name="callback" value="location.reload" />
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="@class" value="CUserLog" />
			  {{mb_key object=$log}}
			  
			  {{if $log->_canUndo}}
				  <input type="hidden" name="_undo" value="0" />
			    <button class="undo" onclick="$V(this.form._undo, 1)">{{tr}}Revoke{{/tr}}</button>
			  {{/if}}
			  
				{{* 
				{{if $log->_canEdit}}
	        <input type="hidden" name="del" value="0" />
	        <button class="trash" onclick="$V(this.form.del, 1)">{{tr}}Remove{{/tr}}</button>
				{{/if}}
			  *}}
			</form>
  	</td>
  </tr>
</table>

{{/if}}