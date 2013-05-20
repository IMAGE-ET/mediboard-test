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
<table class="tbl">
  <tr>
    <td style="text-align: center;">
      <div style="width: 64px;">
        <a href="#1" onclick="new Url().ViewFilePopup('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}')">
          <img class="thumbnail" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;w=64" />
        </a>
      </div>
    </td>
    <td>
      {{mb_include module=system template=CMbObject_view}}
    </td>
  </tr>
</table>