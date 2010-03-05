{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=field  value="$m[$class][pagination_size]"}}
{{assign var=value  value=$dPconfig.$m.$class.pagination_size}}
{{assign var=values value=","|explode:"10,15,20,25,30,50,100,200"}}

<tr>
  <th {{if @$thcolspan}}colspan="{{$thcolspan}}"{{/if}}>
    <label for="{{$field}}">
      {{tr}}{{$class}}{{/tr}}
    </label>
  </th>
  <td>
    <select name="{{$field}}">
      {{if !in_array($value, $values)}}
        <option value="{{$value}}" selected="selected">{{$value}}</option>
      {{/if}}
      {{foreach from=$values item=_value}}
        <option value="{{$_value}}" {{if $_value == $value}}selected="selected"{{/if}}>{{$_value}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>
