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

{{if $file->file_type == "image/fabricjs"}}
  <button class="edit compact notext" type="button" onclick="editDrawing('{{$file->_id}}', null, null, loadAllDocs)">{{tr}}Edit{{/tr}}</button>
{{else}}
  <a class="button download notext" target="_blank" href="?m=files&raw=fileviewer&file_id={{$file->_id}}">{{tr}}Download{{/tr}}</a>
{{/if}}

<form name="actionFile{{$file->_guid}}" method="post">
  <input type="hidden" name="m" value="files" />
  <input type="hidden" name="dosql" value="do_file_aed" />
  {{mb_key object=$file}}
  {{mb_field object=$file field=annule hidden=1}}

  <button type="button" class="trash compact notext"   onclick="trashFile(this.form, '{{$file}}')">{{tr}}Delete{{/tr}}</button>
  {{if $file->annule}}
    <button type="button" class="undo compact notext" onclick="archiveFile(this.form, '{{$file}}')">{{tr}}Restore{{/tr}}</button>
  {{else}}
    <button type="button" class="archive compact notext" onclick="restoreFile(this.form, '{{$file}}')">{{tr}}Archive{{/tr}}</button>
  {{/if}}
</form>
