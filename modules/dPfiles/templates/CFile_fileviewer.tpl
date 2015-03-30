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

  <table class="layout" style="display: inline-table; width: 150px; height: 150px;">
    <tr>
      <td style="text-align: center; height: 92px;">
        <div style="width: 64px; height: 92px; margin: auto; cursor: pointer;" class="icon_fileview"
             onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')">
          <img src="?m=files&raw=fileviewer&file_id={{$file->_id}}&phpThumb=1&w=64&h=92"
               style="background: white; max-width: 64px; max-height: 92px;" />
        </div>
      </td>
    </tr>
    <tr>
      <td class="text item_name" style="text-align: center; vertical-align: top;">
        {{if $file->file_category_id}}<span class="compact circled">{{$file->_ref_category}}</span>{{/if}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$file->_guid}}')">
          {{$file->_icon_name}}
        </span>
      </td>
    </tr>
  </table>

  {{mb_return}}
{{/if}}

<tr>
  <td class="narrow">
    <span style="font-family: FontAwesome; font-size: 11pt;">
      {{if $file->_file_type == "pdf"}}
        &#xf1c1;
      {{elseif $file->_file_type == "image"}}
        &#xf1c5;
      {{elseif $file->_file_type == "text"}}
        &#xf0f6;
      {{elseif $file->_file_type == "excel"}}
        &#xf1c3;
      {{elseif $file->_file_type == "word"}}
        &#xf1c2;
      {{else}}
        &#xf016;
      {{/if}}
    </span>
  </td>
  <td class="item_name">
    <span onclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')"
          onmouseover="ObjectTooltip.createEx(this, '{{$file->_guid}}')"
          style="cursor: pointer;">
      {{$file}}
    </span>
  </td>
  <td style="width: 25%">
    {{if $file->file_category_id}}<span class="compact circled">{{$file->_ref_category}}</span>{{/if}}
  </td>
  <td>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$file->_ref_object->_guid}}')">
      {{$file->_ref_object}}
    </span>
  </td>
  <td class="narrow">
    {{mb_value object=$file field=file_date}}
  </td>
</tr>
