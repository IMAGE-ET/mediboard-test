{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editPermMod" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">


<input type="hidden" name="dosql" value="do_perms_mod_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />
<input type="hidden" name="perm_module_id" value="" />

<table class="form">
  <tr>
    <th class="category" colspan="3">
      Ajouter un droit sur :
      <select name="mod_id">
        {{if !$isAdminPermSet}}
        <option value="">Droits généraux</option>
        {{/if}}
        {{foreach from=$modulesInstalled item=module}}
        <option value="{{$module->mod_id}}">
          {{tr}}module-{{$module->mod_name}}-court{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  <tr>
    <td class="button">
      Permission :
      <select name="permission">
      {{foreach from=$permission|smarty:nodefaults key=key_perm item=curr_perm}}
        <option value="{{$key_perm}}">
          {{$curr_perm}}
        </option>
      {{/foreach}}
      </select>
    </td>
    <td class="button">
      Visibilité :
      <select name="view">
      {{foreach from=$visibility|smarty:nodefaults key=key_view item=curr_view}}
        <option value="{{$key_view}}">
          {{$curr_view}}
        </option>
      {{/foreach}}
      </select>
    </td>
    <td class="button">
      <button class="new" type="submit">Ajouter</button>
    </td>
  </tr>
</table>
</form>
<table class="tbl">
  
  <tr>
    <th colspan="4">Droits existants</th>
  </tr>
  <tr>
    <th>Module</th>
    <th>Type</th>
    <th>Permission - Visibilité</th>
  </tr>
  
  
  {{foreach from=$listPermsModuleComplet item="module"}}
  <tbody class="hoverable">      
  {{counter start=0 skip=1 assign="curr_counter"}}
    {{foreach from=$module key="key" item="perm"}}
    <tr>
     {{if $curr_counter==0}}
      <td class="text" rowspan="{{$module|@count}}"> 
        {{if $perm->mod_id}}
          <strong>{{tr}}module-{{$perm->_ref_db_module->mod_name}}-court{{/tr}}</strong>
          <br />
          {{tr}}module-{{$perm->_ref_db_module->mod_name}}-long{{/tr}}
        {{else}}
          <strong>Droits généraux</strong>
        {{/if}}
      </td>
      {{/if}}
      <td>
      {{if $key == "user"}}
      Utilisateur
      {{elseif $key == "profil"}}
      Profil
      {{/if}}
      </td>
      <td>
      
        <form name="editPermMod{{$perm->perm_module_id}}" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_perms_mod_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="perm_module_id" value="{{$perm->perm_module_id}}" />
          <select name="permission" {{if $key != "user"}} disabled = "disabled"{{/if}}>
          {{foreach from=$permission|smarty:nodefaults key=key_perm item=curr_perm}}
            <option value="{{$key_perm}}" {{if $key_perm == $perm->permission}}selected="selected"{{/if}}>
             {{$curr_perm}}
            </option>
          {{/foreach}}
          </select>
          -
          <select name="view" {{if $key != "user"}} disabled = "disabled"{{/if}}>
            {{foreach from=$visibility|smarty:nodefaults key=key_view item=curr_view}}
            <option value="{{$key_view}}" {{if $key_view == $perm->view}}selected="selected"{{/if}}>
            {{$curr_view}}
            </option>
            {{/foreach}}
          </select>
          {{if $key == "profil" && $module|@count == "2"}}
            <img src="images/icons/no.png" title="Profil desactivé" />
          {{/if}}
          {{if $key == "user"}}
         <button class="modify" type="submit">Valider</button>
         <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la permission sur',objName:'{{$perm->_ref_db_module->mod_name|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
        {{/if}}
       </form>
      </td>
     </tr>
    {{counter}}
    {{/foreach}}
    </tbody>
  {{/foreach}}
  
</table>