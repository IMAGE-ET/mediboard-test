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

{{if $canDoFiles->permission == PERM_EDIT && $object->_can->edit}}
  <button class="new" type="button" onclick="File.upload('{{$object->_class}}','{{$object->_id}}', '')" >
    {{tr}}CFile-title-create{{/tr}}
  </button>
{{/if}}

{{if $app->user_prefs.directory_to_watch}} 
<button class="new yopletbutton" type="button" disabled="disabled" onclick="File.applet.modalOpen('{{$object->_guid}}')">
  {{tr}}Upload{{/tr}}
</button>
{{/if}}


<table class="form" id="list_{{$object->_class}}{{$object->_id}}">
  {{mb_include module=cabinet template=inc_widget_list_files}}
</table>