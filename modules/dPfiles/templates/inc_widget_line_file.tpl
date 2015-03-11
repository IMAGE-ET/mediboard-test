<table class="form">
  <tr {{if $_file->annule}}style="display: none;" class="file_cancelled hatching"{{/if}}>
    <td class="text docitem">
      <a href="#" class="action" id="readonly_{{$_file->_guid}}"
         onclick="File.popup('{{$object_class}}','{{$object_id}}','{{$_file->_class}}','{{$_file->_id}}');"
         onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectView')">{{$_file}}</a>

      <!--  Formulaire pour modifier le nom d'un fichier -->
      <form name="editName-{{$_file->_guid}}" action="?" method="post" submit="return false;"
        onsubmit="if (File.checkFileName($V(this.file_name)))
          return onSubmitFormAjax(this,
            {onComplete: function() {
              File.reloadFile('{{$object_id}}', '{{$object_class}}', '{{$_file->_id}}');
            }}); return false;">
        {{mb_key object=$_file}}
        <input type="hidden" name="m" value="dPfiles" />
        <input type="hidden" name="dosql" value="do_file_aed" />
        <input type="text" style="display: none;" name="file_name" size="50" value="{{$_file->file_name}}""/>
        <script>
          var form = getForm("editName-{{$_file->_guid}}");
          var evt = Prototype.Browser.Gecko ? "keypress" : "keydown";
          Event.observe(form.file_name, evt, File.switchFile.curry('{{$_file->_id}}', form));
        </script>
        <span id="buttons_{{$_file->_guid}}" style="display: none;">
          <button class="tick notext compact" type="button"
            onclick="if (File.checkFileName($V(this.form.file_name))) this.form.onsubmit();">{{tr}}Valid{{/tr}}</button>
        </span>
      </form>
      <small>({{$_file->_file_size}})</small>
      {{if $_file->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
    </td>
    {{if $_file->_can->edit}}
      <td class="button" style="width: 1px">
        <form name="Delete-{{$_file->_guid}}" action="?" enctype="multipart/form-data" method="post"
          onsubmit="return checkForm(this)">
          <input type="hidden" name="m" value="files" />
          <input type="hidden" name="dosql" value="do_file_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="annule" value="0" />
          {{mb_key object=$_file}}
          {{mb_field object=$_file field="_view" hidden=1}}
          <span style="white-space: nowrap;">
            {{if !$name_readonly}}
              <button class="edit notext compact" id="edit_{{$_file->_guid}}" type="button"
                onclick="File.editNom('{{$_file->_guid}}'); File.toggleClass(this);">{{tr}}Modify{{/tr}}</button>
            {{/if}}

            <a class="button print notext compact" target="_blank"
               href="?m=files&a=fileviewer&file_id={{$_file->_id}}&suppressHeaders=1"></a>

            {{if !$_file->annule}}
              <button class="archive notext compact" type="button" onclick="File.cancel(this.form, '{{$object_id}}', '{{$object_class}}')">
                {{tr}}Archive{{/tr}}
              </button>
            {{else}}
              <button class="undo notext compact" type="button" onclick="File.restore(this.form, '{{$object_id}}', '{{$object_class}}')">
                {{tr}}Restore{{/tr}}
              </button>
            {{/if}}

            {{if $can->admin}}
              <button class="trash notext compact" type="button" onclick="File.remove(this, '{{$object_id}}', '{{$object_class}}')">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}
          </span>
        </form>
      </td>
    {{/if}}
    {{if $conf.dPfiles.system_sender}}
    <td class="button" style="width: 1px">
      <form name="Edit-{{$_file->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
     
      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_file}}
     
      <!-- Send File -->
      {{mb_include module=files template=inc_file_send_button 
         notext=notext
          _doc_item=$_file
          onComplete="File.refresh('$object_id','$object_class')"
      }}
      </form>
    </td>
    {{/if}}        
  </tr>
</table>
