{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<style type="text/css">
div.bullet {
  width: 10px; 
  height: 10px; 
  display: inline-block;
}

div.bullet.read {
  background-color: #139DF9;
}

div.bullet.edit {
  background-color: #97E406;
}

div.bullet.empty {
  width: 8px; 
  height: 8px; 
  border: 1px dotted #999;
}
</style>

<table class="tbl">
  {{foreach from=$listFunctions item=curr_func}}
  <tr>
    <th class="text" style="background-color: #{{$curr_func->color}}; color: #000;">{{$curr_func}}</th>
    <th style="background-color: #{{$curr_func->color}}; color: #000;">Profil</th>
    
    {{foreach from=$listModules item=curr_mod}}
    <th class="text">{{tr}}module-{{$curr_mod->mod_name}}-court{{/tr}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$curr_func->_ref_users item=curr_user}}
  {{assign var=user_id value=$curr_user->_id}}
  <tr>
    <td>{{$curr_user->_view}}</td>
    <td>{{$curr_user->_ref_profile->_view}}</td>
    {{if $curr_user->actif}}
    {{foreach from=$listModules item=curr_mod}}
    {{assign var=mod_id value=$curr_mod->_id}}
    <td style="text-align: center; {{if $matrice.$user_id.$mod_id.type == "spécifique"}} background-color: #faa;{{/if}}"
        title="{{$matrice.$user_id.$mod_id.text}} ({{$matrice.$user_id.$mod_id.type}})">
      <div class="bullet {{$matrice.$user_id.$mod_id.permIcon}}"></div>
      <div class="bullet {{$matrice.$user_id.$mod_id.viewIcon}}"></div>
    </td>
    {{/foreach}}
    {{else}}
    <td class="cancelled" colspan="100">Inactif</td>
    {{/if}}
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>