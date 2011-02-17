{{if $can->admin}}
<form name="DeleteAll-{{$object->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPfiles" />
<input type="hidden" name="dosql" value="do_file_multi_delete" />
<input type="hidden" name="del" value="1" />
<input type="hidden" name="object_guid" value="{{$object->_guid}}">
 
  <button class="trash" type="button" style="float: right;" onclick="File.removeAll(this, '{{$object->_guid}}')">
  {{tr}}Delete-all{{/tr}}
</button>

</form>
{{/if}}

<button class="new" type="button" onclick="File.upload('{{$object->_class_name}}','{{$object->_id}}', '')" >
  {{tr}}CFile-title-create{{/tr}}
</button>

{{if $app->user_prefs.directory_to_watch}} 
<button class="new yopletbutton" type="button" disabled="disabled" onclick="File.applet.modalOpen('{{$object->_guid}}')">
  {{tr}}Upload{{/tr}}
</button>
{{/if}}


<table class="form">
  {{foreach from=$object->_ref_files item=_file}}
    {{assign var=object_class value=$object->_class_name}}
    {{assign var=object_id    value=$object->_id        }}
    <tr id="tr_{{$_file->_guid}}">
      <td id="td_{{$_file->_guid}}">
        {{mb_include template="inc_widget_line_file"}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
    	<td>
    		<em>
    	    {{tr}}{{$object->_class_name}}{{/tr}} :
    	    {{tr}}CFile.none{{/tr}}
    	  </em>
    	</td>
    </tr>
  {{/foreach}}

</table>
  