{{*
  * CFile's tooltip
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{assign var="file" value=$object}}

<script>
  trashFile = function(form, file_view) {
    return confirmDeletion(form, {typeName: "le fichier", objName: file_view}, function() {
      if (window.loadAllDocs) {
        loadAllDocs();
      }
    });
  };

  archiveFile = function(form) {
    if (confirm($T("CFile-comfirm_cancel"))) {
      $V(form.annule, 1);
      return onSubmitFormAjax(form, function() {
        if (window.loadAllDocs) {
          loadAllDocs();
        }
      });
    }
  };

  restoreFile = function(form) {
    $V(form.annule, 0);
    return onSubmitFormAjax(form, function() {
      if (window.loadAllDocs) {
        loadAllDocs();
      }
    });
  };
</script>

<table class="tbl">
  <tr>
    <th class="title text">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      {{$object}}
    </th>
  </tr>
</table>

<table class="main">
  <tr>
    <td style="text-align: center;" class="narrow">
      <div style="width: 66px; height: 92px; background: white; cursor: pointer;"
           onclick="new Url().ViewFilePopup('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}')">
        <img style="background: white; max-width: 64px; max-height: 92px; border: 1px solid black; vertical-align: middle;"
             src="?m=files&raw=fileviewer&file_id={{$file->_id}}&phpThumb=1&w=128&h=184" />
      </div>
    </td>
    <td style="vertical-align: top;" class="text">
      {{foreach from=$object->_specs key=prop item=spec}}
        {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      {{if $file->_can->edit}}
        {{if $file->file_type == "image/fabricjs"}}
          <button class="edit" type="button"
                  onclick="editDrawing('{{$file->_id}}', null, null, window.loadAllDocs ? window.loadAllDocs : Prototype.emptyFunction)">{{tr}}Edit{{/tr}}</button>
        {{else}}
          <a class="button download" target="_blank" href="?m=files&raw=fileviewer&file_id={{$file->_id}}">{{tr}}Download{{/tr}}</a>
        {{/if}}

        <form name="actionFile{{$file->_guid}}" method="post">
          <input type="hidden" name="m" value="files" />
          <input type="hidden" name="dosql" value="do_file_aed" />
          {{mb_key object=$file}}
          {{mb_field object=$file field=annule hidden=1}}

          {{if $file->annule}}
            <button type="button" class="undo" onclick="restoreFile(this.form, '{{$file}}')">{{tr}}Restore{{/tr}}</button>
          {{else}}
            <button type="button" class="archive" onclick="archiveFile(this.form, '{{$file}}')">{{tr}}Archive{{/tr}}</button>
          {{/if}}
          <button type="button" class="trash" onclick="trashFile(this.form, '{{$file|smarty:nodefaults|JSAttribute}}')">{{tr}}Delete{{/tr}}</button>
        </form>
      {{/if}}
    </td>
  </tr>
</table>