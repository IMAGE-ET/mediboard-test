{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>  
  <th><strong>{{$var}}</strong></th>
  <td colspan="3" style="text-align: center">
    <input type="text" name="{{$m}}[{{$class}}][{{$var}}][phrase]" value="{{$dPconfig.$m.$class.$var.phrase}}"/>
  </td>
  <td colspan="2" style="text-align: center">
    <input type="text" name="{{$m}}[{{$class}}][{{$var}}][unite_prise]" value="{{$dPconfig.$m.$class.$var.unite_prise}}"/>
  </td>             
</tr>