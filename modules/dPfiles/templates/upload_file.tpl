{{if $uploadok}}
<script type="text/javascript">
if (window.parent.reloadAfterUploadFile) {
  window.parent.reloadAfterUploadFile();
}

if (window.parent.File && window.parent.File.refresh) {
	window.parent.File.refresh("{{$object->_id}}", "{{$object->_class_name}}");
}
</script>
{{/if}}

<script type="text/javascript">
var count_files = 0;

addFile = function(elt) {
  count_files ++;
  
  // Incr�ment du rowspan pour le th du label
  var label = $("labelfile-{{$object->_id}}");
  label.writeAttribute("rowspan", count_files + 1);
 
  // Ajout d'un input pour le fichier suivant
  // <input type="file" name="formfile[0]" size="0" onchange="addFile(this); this.onchange=''"/>
  var tr = elt.up().up().up();
  tr.insert(
    DOM.tr( {},
      DOM.td({colspan: 4},
        DOM.input({type: "file", name: "formfile["+count_files + "]", size: 0, onchange: "addFile(this); this.onchange=''"})
  )));
}

</script>

<form name="uploadFrm" action="?m=dPfiles&amp;a=upload_file&amp;dialog=1" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPfiles" />
<input type="hidden" name="a" value="upload_file" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="dosql" value="do_file_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="object_class" value="{{$object->_class_name}}" />
<input type="hidden" name="object_id" value="{{$object->_id}}" />

<table class="form">
  <tr>
    <th class="title" colspan="7">
      Ajouter un fichier pour {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td class="button">
      {{tr}}CFile-msg-maxsize{{/tr}} : {{$conf.dPfiles.upload_max_filesize}}<br />
    </td>
    <th>
      <label title="{{tr}}CFile-file_category_id-desc{{/tr}}">
        {{tr}}CFile-file_category_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="_file_category_id">
        <option value="" {{if !$file_category_id}}selected="selected"{{/if}}>&mdash; Aucune</option>
        {{foreach from=$listCategory item=curr_cat}}
        <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $file_category_id}}selected="selected"{{/if}} >
          {{$curr_cat->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <th>
      <label title="{{tr}}CFile-_rename-desc{{/tr}}">{{tr}}CFile-_rename{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="_rename" value="{{$_rename}}"/>
    </td>
    <th>
      <label title="{{tr}}CFile-private-desc{{/tr}}" for="uploadFrm___private">
        {{tr}}CFile-private{{/tr}}
      </label>
    </th>
    <td>
      {{mb_field object=$file field="private" typeEnum=checkbox}}
    </td>
  </tr>
  
  <tr>
    <td colspan="7">
      <div style="max-height: 220px; overflow: auto; width: 100%;">
      <table class="form">
        <tr>
          <th id="labelfile-{{$object->_id}}">
            <label>{{tr}}CFile{{/tr}}</label>
          </th>
          <td colspan="6">
            <input type="file" name="formfile[0]" size="0" onchange="addFile(this); this.onchange=''"/>
          </td>
        </tr>
      </table>
      </div>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="7">
      <button class="submit" type="submit">{{tr}}Add{{/tr}}</button>
    </td>
  </tr>

</table>
</form>
