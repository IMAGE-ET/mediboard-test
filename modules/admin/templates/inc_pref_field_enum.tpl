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

<select name="pref[{{$var}}]">
  {{if $user_id != "default"}} 
    <option value="">&mdash; {{tr}}Ditto{{/tr}}</option>
  {{/if}}

  {{foreach from=$values item=_value}}
  <option value="{{$_value}}" {{if $pref.user == $_value}} selected="selected" {{/if}}>
  	{{tr}}{{$value_locale_prefix}}{{$_value}}{{/tr}}
	</option>
	{{/foreach}}
</select>
