{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if @$m}}
  {{if @$class}}
    {{assign var=field  value="$m[$class][$var]"}}
    {{assign var=value  value=$dPconfig.$m.$class.$var}}
    {{assign var=locale value=config-$m-$class-$var}}
  {{else}}
    {{assign var=field  value="$m[$var]"}}
    {{assign var=value  value=$dPconfig.$m.$var}}
    {{assign var=locale value=config-$m-$var}}
  {{/if}}
{{else}}
  {{assign var=field  value="$var"}}
  {{assign var=value  value=$dPconfig.$var}}
  {{assign var=locale value=config-$var}}
{{/if}}

<tr>
  <th {{if @$thcolspan}}colspan="{{$thcolspan}}"{{/if}}>
    <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
      {{tr}}{{$locale}}{{/tr}}
    </label>  
  </th>
  <td colspan="3">
    <input class="str" name="{{$field}}" value="{{$value}}" {{if @$size}}size="{{$size}}"{{/if}} {{if @$maxlength}}maxlength="{{$maxlength}}"{{/if}}/> 
    {{if @$suffix}}{{$suffix}}{{/if}} 
  </td>
</tr>
