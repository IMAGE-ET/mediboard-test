{{if $uploadok}}
<script type="text/javascript">
if (window.opener.reloadAfterUploadFile) {
  window.opener.reloadAfterUploadFile();
}

if (window.opener.File && window.opener.File.refresh) {
	window.opener.File.refresh("{{$object->_id}}", "{{$object->_class_name}}");
}
</script>
{{/if}}

<form name="uploadFrm" action="?m=dPfiles&amp;a=upload_file&amp;dialog=1" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPfiles" />
<input type="hidden" name="a" value="upload_file" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="dosql" value="do_file_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="object_class" value="{{$object->_class_name}}" />
<input type="hidden" name="object_id" value="{{$object->_id}}" />
<input type="hidden" name="file_category_id" value="{{$file_category_id}}" />
<input type="hidden" name="file_rename" value="{{$file_rename}}" />

<table class="form">
  <tr>
    <th class="title" colspan="5">
      Ajouter un fichier pour {{$object->_view}}
    </th>
  </tr>
  <tr>
    
    <td class="button">
      {{tr}}CFile-msg-maxsize{{/tr}} : {{$dPconfig.dPfiles.upload_max_filesize}}<br />
    </td>
    <td></td>
    <td></td>
    <th><label title="{{tr}}CFile-private-desc{{/tr}}" for="uploadFrm___private">{{tr}}CFile-private{{/tr}}</label></th>
    <td>
      {{mb_field object=$file field="private" typeEnum=checkbox}}
    </td>
  </tr>
  
  {{foreach from=$nb_files_upload item=curr_nb_file}}
  <tr>
    <th>
      <label for="_file_category_id[{{$curr_nb_file}}]" title="{{tr}}CFile-file_category_id-desc{{/tr}}">
        {{tr}}CFile-file_category_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="_file_category_id[{{$curr_nb_file}}]">
        <option value="" {{if !$file_category_id}}selected="selected"{{/if}}>&mdash; Aucune</option>
        {{foreach from=$listCategory item=curr_cat}}
        <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $file_category_id}}selected="selected"{{/if}} >
          {{$curr_cat->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th><label for="formfile[{{$curr_nb_file}}]">{{tr}}CFile{{/tr}}</label></th>
    <td colspan="2">
      <input type="file" name="formfile[{{$curr_nb_file}}]" size="0" />
    </td>
  </tr>
  {{/foreach}}
  <tr>
    <td class="button" colspan="6">
      <button class="submit" type="submit">{{tr}}Add{{/tr}}</button>
    </td>
  </tr>

</table>
</form>
