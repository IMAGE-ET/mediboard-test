{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=display value="icon"}}

{{assign var=file value=$doc->loadFile()}}
{{assign var="src" value="?m=files&raw=fileviewer&file_id=`$file->_id`&phpThumb=1&w=64&h=92"}}

{{if $display == "icon"}}
  {{mb_script module=patients script=fileviewer ajax=true}}

  <table class="layout table_icon_fileview" onmouseover="ObjectTooltip.createEx(this, '{{$doc->_guid}}')">
    <tr>
      <td style="text-align: center; height: 120px;">
        <div class="icon_fileview" ondblclick="popFile('{{$doc->_class}}', '{{$doc->_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')">
          <img src="{{$src}}" style="background: white; max-width: 64px; max-height: 92px;" />
        </div>
      </td>
    </tr>
    <tr>
      <td class="text item_name" style="text-align: center; vertical-align: top;">
        {{if $doc->file_category_id}}<span class="compact circled">{{$doc->_ref_category}}</span>{{/if}} {{$doc->_icon_name}}
      </td>
    </tr>
  </table>

  {{mb_return}}
{{/if}}

<tr>
  <td class="narrow">
    <span style="font-family: FontAwesome; font-size: 11pt;">&#xf0f6;</span>
  </td>
  <td class="item_name">
    <span onclick="popFile('{{$doc->_class}}', '{{$doc->_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')"
          style="cursor: pointer;">
      {{$doc}}
    </span>
  </td>
  <td style="width: 25%">
    {{if $doc->file_category_id}}<span class="compact circled">{{$doc->_ref_category}}</span>{{/if}}
  </td>
  <td>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$doc->_ref_object->_guid}}')">
      {{$doc->_ref_object}}
    </span>
  </td>
  <td class="narrow">
    {{mb_value object=$doc->_ref_content field=last_modified}}
  </td>
</tr>