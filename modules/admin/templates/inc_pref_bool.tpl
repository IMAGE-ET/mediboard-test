{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8692 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>
    <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
  </th>
  <td>
    <select name="pref[{{$var}}]">
      <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
      <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
    </select>
  </td>
</tr>
