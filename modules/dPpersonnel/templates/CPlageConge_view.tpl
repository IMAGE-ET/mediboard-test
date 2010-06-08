{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{include file=CMbObject_view.tpl}}
{{assign var=plage value=$object}}

{{if $object->canEdit()}}
<table class="tbl">
  <tr>
    <td class="button">
      <button type="submit" class="edit" onclick="editPlageConge('{{$plage->_id}}','{{$plage->user_id}}')">
        {{tr}}Modify{{/tr}}
      </button>
    </td>
  </tr>
</table>
{{/if}}