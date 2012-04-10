{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=forms script=ex_class_editor}}
{{mb_script module=system script=object_selector}}

{{main}}
ExClass.refreshList();
ExClass.edit({{$ex_class->_id}});
{{/main}}

<table class="main">
  <tr>
    <td id="exClassList" style="width: 15%; min-width: 18em;">
      <!-- exClassList -->
    </td>
    <td id="exClassEditor">
      <!-- exClassEditor -->
    </td>
  </tr>
</table>