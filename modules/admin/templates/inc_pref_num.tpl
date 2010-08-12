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
    <input class="num" type="text" size="8" name="pref[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
  </td>
</tr>