{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">

function cancelObject(oObject) {
  var oForm = document.editPermObj;
  oForm.object_id.value = "";
  oForm._object_view.value = "";
}

Main.add(function(){
  $(getForm('editPermObj').object_class).makeAutocomplete({width: '300px'});
});

</script>

<form name="editPermObj" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_perms_obj_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />
<input type="hidden" name="perm_object_id" value="" />


<table class="form">
  <tr>
    <th class="category" colspan="3">
      Ajouter une permission sur la classe :
			<span style="text-align: left; font-weight: normal; color:#000;">
      <select name="object_class">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$classes item=_class}}
        <option value="{{$_class}}">
          {{tr}}{{$_class}}{{/tr}} - {{$_class}}
        </option>
        {{/foreach}}
      </select>
			</span>
    </th>
  </tr>
  <tr>
    <td class="button readonly">
      <input type="text" name="_object_view" value="" readonly="readonly" />
      <input type="hidden" name="object_id" value="" />
      <button type="button" class="search" onclick="ObjectSelector.init()">
        Chercher un objet
      </button>
      <button type="button" class="cancel" onclick="cancelObject()">
        Pas d'objet
      </button>
      <script type="text/javascript">
        ObjectSelector.init = function(){  
          this.sForm     = "editPermObj";
          this.sId       = "object_id";
          this.sView     = "_object_view";
          this.sClass    = "object_class";
          this.onlyclass = "false";
          this.pop();
        } 
       </script>
    </td>
    <td class="button">
      {{mb_label object=$permObject field=permission}}
      {{mb_field object=$permObject field=permission}}
    </td>
    <td class="button">
      <button class="new" type="submit">Ajouter</button>
    </td>
  </tr>
</table>
</form>
<table class="tbl">
  <tr>
    <th class="title" colspan="4">Droits existants</th>
  </tr>
  <tr>
    <th>{{mb_label object=$permObject field=object_class}}</th>
    <th>{{mb_label object=$permObject field=object_id}}</th>
    <th>{{mb_label object=$permObject field=_owner}}</th>
    <th>{{mb_label object=$permObject field=permission}}</th>
  </tr>
  
  
  {{foreach from=$permsObject item=_permsObjectByClass}}
    <tbody class="hoverable">
    {{foreach from=$_permsObjectByClass item=_permsObject}}
    {{foreach from=$_permsObject key=owner item=_perm name=owner}}
		{{assign var=object value=$_perm->_ref_db_object}}
    <tr>
      {{if $smarty.foreach.owner.first}} 
      <td class="text" rowspan="{{$_permsObject|@count}}"> 
        <strong>{{tr}}{{$_perm->object_class}}{{/tr}}</strong>
      </td>
      <td class="text" rowspan="{{$_permsObject|@count}}">
        {{if $object->_id}}
				  <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
            {{$object}}
				  </span>
        {{else}}
          <strong>Droits généraux</strong>
        {{/if}}      
      </td>
      {{/if}}

      <td>{{mb_value object=$_perm field=_owner}}</td>

      <td style="{{if count($_permsObject) > 1 && $owner == "profil"}} text-decoration: line-through; {{/if}}">
        <form name="Edit-{{$_perm->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

        <input type="hidden" name="dosql" value="do_perms_obj_aed" />
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

        {{if $owner == "user"}}
        <span style="margin: 0 1em;">
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la permission sur',objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
        </span>
				{{/if}}

        </form>
		  </td>
		</tr>
    {{/foreach}}
    {{/foreach}}
	  </tbody>
  {{/foreach}}
</table>