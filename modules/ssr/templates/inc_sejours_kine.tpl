{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$sejours item=_sejour}}
{{mb_include template=inc_sejour_draggable sejour=$_sejour}}
{{foreachelse}}
<tr>
  <td >
    <em>{{tr}}CSejour.none{{/tr}}</em>
  </td>
</tr>
{{/foreach}}