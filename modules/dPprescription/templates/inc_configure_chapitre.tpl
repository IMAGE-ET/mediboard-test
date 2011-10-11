{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th><strong>{{tr}}CPrescription._chapitres.{{$var}}{{/tr}}</strong></th>
  <td style="text-align: center">
    <input type="text" name="{{$m}}[{{$class}}][{{$var}}][phrase]" value="{{$conf.$m.$class.$var.phrase}}"/>
  </td>
  <td style="text-align: center">
    <input type="text" name="{{$m}}[{{$class}}][{{$var}}][unite_prise]" value="{{$conf.$m.$class.$var.unite_prise}}"/>
  </td>  
  <td style="text-align: center;">
    <label for="{{$m}}[{{$class}}][{{$var}}][fin_sejour]_1">{{tr}}bool.1{{/tr}}</label>
    <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][fin_sejour]" value="1" {{if $conf.$m.$class.$var.fin_sejour == "1"}}checked="checked"{{/if}} />
    <label for="{{$m}}[{{$class}}][{{$var}}][fin_sejour]_0">{{tr}}bool.0{{/tr}}</label>
    <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][fin_sejour]" value="0" {{if $conf.$m.$class.$var.fin_sejour == "0" || $conf.$m.$class.$var.fin_sejour == ""}}checked="checked"{{/if}} />
  </td>
</tr>