{{* $Id: inc_config_bool.tpl 8113 2010-02-22 09:29:33Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8113 $
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

  <td {{if @$tdcolspan}}colspan="{{$tdcolspan}}"{{/if}}>
    <select class="str" name="{{$field}}">
      {{assign var=values value='|'|explode:$values}}
    	{{foreach from=$values item=_value}}
        <option value="{{$_value}}" {{if $value == $_value}} selected="selected"{{/if}}>
        	{{tr}}{{$locale}}-{{$_value}}{{/tr}}</option>
			{{/foreach}}
    </select> 
  </td>
</tr>
