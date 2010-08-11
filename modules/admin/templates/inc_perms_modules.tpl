{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="EditCPermModule" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">


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
      {{mb_label object=$permModule field=permission}}
      {{mb_field object=$permModule field=permission}}
    </td>
    <td class="button">
      {{mb_label object=$permModule field=view}}
      {{mb_field object=$permModule field=view}}
    </td>
    <td class="button">
      <button class="new" type="submit">{{tr}}Add{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
<table class="tbl">
  
  <tr>
    <th class="title" colspan="4">Droits existants</th>
  </tr>
  <tr>
    <th>{{mb_label object=$permModule field=mod_id}}</th>
    <th>{{mb_label object=$permModule field=_owner}}</th>
    <th>
      {{mb_label object=$permModule field=permission}} - 
      {{mb_label object=$permModule field=view}} 
		</th>
  </tr>
  
  
  {{foreach from=$permsModule item=_permsModule}}
  <tbody class="hoverable">
    {{foreach from=$_permsModule key=owner item=_perm name=owner}}
    <tr>
    	{{if $smarty.foreach.owner.first}} 
      <td class="text" rowspan="{{$_permsModule|@count}}"> 
        {{if $_perm->mod_id}}
          {{assign var=module value=$_perm->_ref_db_module}}
          <strong>{{tr}}module-{{$module->mod_name}}-court{{/tr}}</strong>
          <br />
          {{tr}}module-{{$module->mod_name}}-long{{/tr}}
        {{else}}
          <strong>{{tr}}CModule.all{{/tr}}</strong>
        {{/if}}
      </td>
    	{{/if}}
			
      <td>{{mb_value object=$_perm field=_owner}}</td>
			
      <td style="{{if count($_permsModule) > 1 && $owner == "profil"}} text-decoration: line-through; {{/if}}">
        <form name="Edit-{{$_perm->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  
        <input type="hidden" name="dosql" value="do_perms_mod_aed" />
        <input type="hidden" name="del" value="0" />
				{{mb_key object=$_perm}}
        
				<div style="width: 8em; display: inline-block;">
        {{if $owner == "user"}}
          {{mb_field object=$_perm field=permission}}
        {{else}}
				  <span style="padding: 0 6px;">
	          {{mb_value object=$_perm field=permission}}
   			  </span>
        {{/if}}
				</div>

        <div style="width: 8em; display: inline-block;">
        {{if $owner == "user"}}
          {{mb_field object=$_perm field=view}}
        {{else}}
          <span style="padding: 0 6px;">
          {{mb_value object=$_perm field=view}}
					</span>
        {{/if}}
        </div>

        {{if $owner == "user"}}
				<span style="margin: 0 1em;">
         <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
         <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la permission sur',objName:'{{$module->mod_name|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
				</span>
        {{/if}}
 
       </form>
      </td>
     </tr>
    {{/foreach}}
    </tbody>
  {{/foreach}}
  
</table>