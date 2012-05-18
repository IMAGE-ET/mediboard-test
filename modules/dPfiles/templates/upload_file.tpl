<script type="text/javascript">

showLoading = function(){
  var systemMsg = $('systemMsg');
  systemMsg.update('\<div class=\'loading\'\>{{tr}}Loading in progress{{/tr}}\</div\>');
  systemMsg.show();
}

var count_files = 0;
// Les images ne sont pas converties en PDF,
// donc le bouton de fusion reste grisé tant que l'on rajoute une image
var extensions = ["bmp", "gif", "jpeg", "jpg", "png"];

addFile = function(elt) {
  {{if $conf.dPfiles.CFile.merge_to_pdf}}
    var add_and_merge = $("add_and_merge");
    
    var oForm = getForm("uploadFrm");
    
    if (add_and_merge.disabled) {
      var name_file = oForm.elements["formfile["+count_files+"]"].value;
      var extension = name_file.substring(name_file.lastIndexOf(".")+1);
    
      if (extensions.indexOf(extension) == -1) {
        add_and_merge.disabled = "";
      }
    }
  {{/if}}
    
  count_files ++;
  // Incrément du rowspan pour le th du label
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

<iframe name="upload-{{$object->_guid}}" id="upload-{{$object->_guid}}" style="width: 1px; height: 1px;"></iframe>

<form name="uploadFrm" action="?" enctype="multipart/form-data" method="post"
  onsubmit="return checkForm(this)" target="upload-{{$object->_guid}}">
<input type="hidden" name="m" value="dPfiles" />
<input type="hidden" name="a" value="upload_file" />
<input type="hidden" name="dosql" value="do_file_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="ajax" value="1" />
<input type="hidden" name="suppressHeaders" value="1" />
<input type="hidden" name="callback" value="reloadCallback" />
<input type="hidden" name="object_class" value="{{$object->_class}}" />
<input type="hidden" name="object_id" value="{{$object->_id}}" />
<input type="hidden" name="named" value="{{$named}}" />
<input type="hidden" name="_merge_files" value="0" />

{{if $named}}
<input type="hidden" name="_rename" value="{{$_rename}}" />
{{/if}}

<table class="form">
  <tr>
    <th class="title" colspan="{{if $named}} 6 {{else}} 7 {{/if}}">
      Ajouter un fichier {{if $named}}'{{$_rename}}'{{/if}} pour 
      <br/>'{{$object->_view}}'
    </th>
  </tr>

  <tr>
    <td class="button" colspan="4">
      <div class="small-info">
        <div>{{tr}}config-dPfiles-upload_max_filesize{{/tr}} : <strong>{{$conf.dPfiles.upload_max_filesize}}</strong></div>
        <div>{{tr}}config-dPfiles-extensions_yoplet  {{/tr}} : <strong>{{$conf.dPfiles.extensions_yoplet  }}</strong></div>
      </div>
    </td>
  </tr>

{{if !$named}}
  <tr>
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
{{/if}}
  
  <tr>
    <td colspan="7">
      <div style="max-height: 220px; overflow: auto; width: 100%;">
      <table class="form">
        <tr>
          <th id="labelfile-{{$object->_id}}">
            <label>{{tr}}CFile{{/tr}}</label>
          </th>
          <td colspan="6">
            <input type="file" name="formfile[0]" size="0"
              {{if !$named}}
                onchange="addFile(this); this.onchange=''"
              {{/if}} />
          </td>
        </tr>
      </table>
      </div>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="7">
      <button class="submit" type="submit" onclick="showLoading();">{{tr}}Add{{/tr}}</button>
      {{if $conf.dPfiles.CFile.merge_to_pdf}}
        <button class="hslip" id="add_and_merge" disabled="disabled" onclick="$V(this.form._merge_files, 1); this.form.submit(); showLoading();">
          {{tr}}CFile-_add_and_merge{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>

</table>
</form>
