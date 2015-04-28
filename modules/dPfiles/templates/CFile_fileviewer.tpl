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

  <table class="layout table_icon_fileview" onmouseover="ObjectTooltip.createEx(this, '{{$file->_guid}}')">
    <tr>
      <td style="text-align: center; height: 120px; vertical-align: middle;">
        <div class="icon_fileview" ondblclick="popFile('{{$file->object_class}}', '{{$file->object_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')"
          style="line-height: 90px;">
          {{if in_array($file->_file_type, array("pdf", "image"))}}
            <img src="?m=files&raw=fileviewer&file_id={{$file->_id}}&phpThumb=1&w=64&h=92"
                 style="background: white; max-width: 64px; max-height: 92px;" />
          {{elseif $file->file_type == "image/fabricjs"}}
            <span style="font-family: FontAwesome; font-size: 11pt;">
              &#xf1fc;
            </span>
          {{else}}
            <img src="images/pictures/medifile_black.png" style="background: white; max-width: 64px; max-height: 92px;" />
          {{/if}}
        </div>
      </td>
    </tr>
    <tr>
      <td class="text item_name" style="text-align: center; vertical-align: top;">
        {{if $file->file_category_id}}<span class="compact circled">{{$file->_ref_category}}</span>{{/if}} {{$file->_icon_name}}
      </td>
    </tr>
  </table>

  {{mb_return}}
{{/if}}

<tr>
  <td class="narrow">
    <span style="font-family: FontAwesome; font-size: 11pt;">
      {{if $file->file_type == "image/fabricjs"}}
        &#xf1fc;
      {{elseif $file->_file_type == "pdf"}}
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
