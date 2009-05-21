{{* $Id: inc_config_date_format.tpl 6069 2009-04-14 10:17:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if @$class}}
	{{assign var=field  value="$m[$class][$var]"}}
	{{assign var=value  value=$dPconfig.$m.$class.$var}}
	{{assign var=locale value=config-$m-$class-$var}}
{{else}}
	{{assign var=field  value="$m[$var]"}}
	{{assign var=value  value=$dPconfig.$m.$var}}
	{{assign var=locale value=config-$m-$var}}
{{/if}}
	
<tr>
  <th>
    <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
      {{tr}}{{$locale}}{{/tr}}
    </label>  
  </th>

  <td>
    <label for="{{$field}}_1">{{tr}}bool.1{{/tr}}</label>
    <input type="radio" name="{{$field}}" value="1" {{if $value == "1"}}checked="checked"{{/if}}/>
    <label for="{{$field}}_0">{{tr}}bool.0{{/tr}}</label>
    <input type="radio" name="{{$field}}" value="0" {{if $value == "0"}}checked="checked"{{/if}}/>
  </td>
</tr>
