{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
   <th>
     <label for="{{$m}}[{{$class}}][{{$var}}][{{$type_niveau}}][{{$niveau}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type_niveau}}-{{$niveau}}{{/tr}}">
         {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type_niveau}}-{{$niveau}}{{/tr}}
     </label>  
   </th>
  <td>
    <select name="{{$m}}[{{$class}}][{{$var}}][{{$type_niveau}}][{{$niveau}}]">
       <option value="0" {{if 0 == $dPconfig.$m.$class.$var.$type_niveau.$niveau}} selected="selected" {{/if}}>0</option>
       <option value="1" {{if 1 == $dPconfig.$m.$class.$var.$type_niveau.$niveau}} selected="selected" {{/if}}>1</option>
       <option value="2" {{if 2 == $dPconfig.$m.$class.$var.$type_niveau.$niveau}} selected="selected" {{/if}}>2</option>
     </select>
  </td>
</tr>