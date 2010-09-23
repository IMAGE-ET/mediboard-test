<div id="modal-yoplet" style="display: none;">
  <form name="addFastFile" method="post" action="?"
    onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="dPfiles" />
    <input type="hidden" name="dosql" value="do_file_aed" />
    <input type="hidden" name="_from_yoplet" value="1" />
    <input type="hidden" name="object_class" value="" />
    <input type="hidden" name="private" value="0" />
    <input type="hidden" name="object_id" value="" />
    <input type="hidden" name="file_date" value="now" />
    <input type="hidden" name="callback" value="File.applet.addfile_callback" />
    <input type="hidden" name="_index" value="" />
    <input type="hidden" name="_file_path" value="" />
    <input type="hidden" name="_checksum" value="" />
    
    <table class="tbl">
      <tr>
        <td colspan="3">Liste des fichiers</td>
      </tr>
      <tr>
        <td colspan="3">
          <table id="file-list">
          </table>
        </td>
      </tr>
      <tr>
        <td colspan="3">
          <input type="hidden" name="file_rename" value="" />
          {{tr}}CFile-file_category_id{{/tr}} :
          <select name="file_category_id">
            <option value="">&mdash; Choisissez une catégorie</option>
            {{foreach from=$categories item=_category key=_category_id}}
              <option value="{{$_category_id}}">{{$_category}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="3">Supprimer les fichiers après envoi <input
          type="checkbox" name="delete_auto" checked=true/></td>
      </tr>
      <tr>
        <td colspan="3">
        <button type="button" class="tick"
          onclick="File.applet.closeModal();">
        {{tr}}Ok{{/tr}}</button>
        <button type="button" class="cancel"
          onclick="File.applet.cancelModal();">{{tr}}Cancel{{/tr}}</button>
        <button type="button" class="tick uploadinmodal"
          onclick="this.disabled = 'disabled'; File.applet.uploadFiles();">Upload</button>
        </td>
      </tr>
    </table>
  </form>
</div>