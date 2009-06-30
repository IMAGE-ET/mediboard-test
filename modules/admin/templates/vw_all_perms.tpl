{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <td class="button">Permission Visibilité</td>
    {{foreach from=$listModules item=curr_mod}}
    <th>{{tr}}module-{{$curr_mod->mod_name}}-court{{/tr}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$listFunctions item=curr_func}}
  <tr>
    <th  style="background-color: #{{$curr_func->color}}; color: #000;">{{$curr_func->_view}}</th>
    <td colspan="{{$listModules|@count}}" style="background-color: #{{$curr_func->color}};" />
  </tr>
  {{foreach from=$curr_func->_ref_users item=curr_user}}
  {{assign var=user_id value=$curr_user->_id}}
  <tr>
    <td class="text">{{$curr_user->_view}}</td>
    {{foreach from=$listModules item=curr_mod}}
    {{assign var=mod_id value=$curr_mod->_id}}
    <td style="text-align: center;">
      <span title="{{$matrice.$user_id.$mod_id.text}}">
        {{if $matrice.$user_id.$mod_id.permIcon}}
        <img src="images/icons/{{$matrice.$user_id.$mod_id.permIcon}}" alt="{{$matrice.$user_id.$mod_id.permIcon}}" />
        {{else}}
        -
        {{/if}}
        {{if $matrice.$user_id.$mod_id.viewIcon}}
        <img src="images/icons/{{$matrice.$user_id.$mod_id.viewIcon}}" alt="{{$matrice.$user_id.$mod_id.viewIcon}}" />
        {{else}}
        -
        {{/if}}
        <br />
      </span>
    </td>
    {{/foreach}}
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>