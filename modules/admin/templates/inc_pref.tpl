{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8692 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=readonly value=0}}

<tr>
  {{if $can->admin}} 
  <td class="narrow">
    <button class="search notext compact" type="button" onclick="Preferences.report('{{$var}}');">
      {{tr}}Report{{/tr}}
    </button>
  </td>
  {{/if}}
  <th>
    <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
  </th>

  {{assign var=pref value=$prefs.$module.$var}}
  {{if $user_id != "default"}} 

  <td class="{{if $pref.template !== null || $pref.user !== null}} redefined {{else}} active {{/if}}">
    {{mb_include template="inc_pref_value_$spec" value=$pref.default}}
  </td>

  {{if !$user->template}} 
  <td class="{{if $pref.user !== null}} redefined {{else}} active {{/if}}">
    {{if $pref.template}}
    {{mb_include template="inc_pref_value_$spec" value=$pref.template}}
    {{/if}}
  </td>
  {{/if}}

  {{/if}}
  <td>
    {{mb_include template="inc_pref_field_$spec"}}
  </td>
</tr>