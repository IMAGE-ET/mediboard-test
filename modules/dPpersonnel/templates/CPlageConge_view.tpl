{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{include file=CMbObject_view.tpl}}

{{if $object->_can->edit}}
{{assign var=plage value=$object}}
<table class="tbl">
  <tr>
    <td class="button">
      <button type="submit" class="edit" onclick="PlageConge.edit('{{$plage->_id}}','{{$plage->user_id}}')">
        {{tr}}Modify{{/tr}}
      </button>
    </td>
  </tr>
</table>
{{/if}}