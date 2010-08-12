{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8692 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=value_locale_prefix value="pref-$var-"}}

{{if !is_array($values)}} 
{{assign var=values value='|'|explode:$values}}
{{/if}}

<tr>
  <th>
    <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
  </th>
  <td>
    <select name="pref[{{$var}}]">
      {{foreach from=$values item=_value}}
      <option value="{{$_value}}" {{if $prefsUser.$module.$var == $_value}} selected="selected" {{/if}}>
      	{{tr}}{{$value_locale_prefix}}{{$_value}}{{/tr}}
			</option>
			{{/foreach}}
    </select>
  </td>
</tr>
