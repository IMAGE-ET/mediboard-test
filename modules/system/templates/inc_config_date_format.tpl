{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>
    <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
      {{tr}}config-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    {{$now|date_format:$dPconfig.$var}}
  </td>
</tr>
