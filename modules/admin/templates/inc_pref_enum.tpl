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
      {{assign var=values value='|'|explode:$values}}
      {{foreach from=$values item=_value}}
      <option value="{{$_value}}" {{if $prefsUser.$module.$var == $_value}}selected="selected"{{/if}}>
      	{{tr}}pref-{{$var}}-{{$_value}}{{/tr}}
			</option>
			{{/foreach}}
    </select>
  </td>
</tr>
