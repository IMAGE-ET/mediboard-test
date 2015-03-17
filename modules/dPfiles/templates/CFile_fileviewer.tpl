{{*
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=display value="icon"}}

{{if $display == "icon"}}
  {{mb_script module=patients script=fileviewer ajax=true}}

  <script>
    trashFile = window.trashFile || function(form, file_view) {
      return confirmDeletion(form, {typeName: "le fichier", objName: file_view}, function() {
        if (window.loadAllDocs) {
          loadAllDocs();
        }
      });
    };

    archiveFile = window.archiveFile || function(form) {
      if (confirm($T("CFile-comfirm_cancel"))) {
        $V(form.annule, 1);
        return onSubmitFormAjax(form, function() {
          if (window.loadAllDocs) {
            loadAllDocs();
          }
        });
      }
    };

    restoreFile = window.archiveFile || function(form) {
      $V(form.annule, 0);
      return onSubmitFormAjax(form, function() {
        if (window.loadAllDocs) {
          loadAllDocs();
        }
      });
    };
  </script>

  <table class="layout" style="width: 300px; display: inline-block;" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
    <tr>
      <td style="width: 80px;">
        <img class="thumbnail" src="?m=files&raw=fileviewer&file_id={{$file->_id}}&phpThumb=1&w=64&h=92"
             style="background: white; max-width: 64px; max-height: 92px;"
             onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')" />
      </td>
      <td style="visibility: hidden" class="toolbar">
        {{$file->file_date|date_format:$conf.datetime}} <br />

        {{mb_include module=files template=inc_fileviewer_toolbar}}
      </td>
    </tr>
    <tr>
      <td colspan="2">{{$file}}</td>
    </tr>
  </table>

  {{mb_return}}
{{/if}}

<table class="tbl" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
  <tr>
    <td style="width: 75%">
      {{$file}}
    </td>
    <td>
      {{$file->file_date|date_format:$conf.datetime}}
      <span class="toolbar" style="float: right; visibility: hidden;">
        {{mb_include module=files template=inc_fileviewer_toolbar}}
      </span>
    </td>
  </tr>
</table>