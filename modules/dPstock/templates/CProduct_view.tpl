{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=CMbObject_view}}

{{if $object->canEdit()}}
  <table class="main tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$object->_id}}'">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
    </tr>
  </table>
{{/if}}
