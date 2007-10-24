{{mb_include_script module="system" script="object_selector"}}

<script language="Javascript" type="text/javascript">

function cancelObject(oObject) {
  var oForm = document.editPermObj;
  oForm.object_id.value = "";
  oForm._object_view.value = "";
}

</script>

<form name="editPermObj" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_perms_obj_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />
<input type="hidden" name="perm_object_id" value="" />


<table class="form">
  <tr>
    <th colspan="4" class="title">Droits sur les objets</th>
  </tr>
  {{if $type == "user"}}
  <tr>
    <th class="category" colspan="3">
      Ajouter un droit sur :
      <select name="object_class">
        {{foreach from=$listClasses|smarty:nodefaults item=class}}
        <option value="{{$class}}">
          {{tr}}{{$class}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  <tr>
    <td class="button readonly">
      <input type="text" name="_object_view" value="" readonly="readonly" />
      <input type="hidden" name="object_id" value="" />
      <br />
      <button type="button" class="cancel" onclick="cancelObject()">
        Pas d'objet
      </button>
      <button type="button" class="search" onclick="ObjectSelector.init()">
        Chercher un objet
      </button>
      <script language="Javascript" type="text/javascript">
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
      <button class="new" type="submit">Ajouter</button>
    </td>
  </tr>
  {{/if}}
</table>
</form>
<table class="tbl">
  <tr>
    <th colspan="4">Droits existants</th>
  </tr>
  <tr>
    <th>Classe</th>
    <th>Objet</th>
    <th>Permission</th>
  </tr>
  {{foreach from=$listPermsObjects item=perm}}
  <tr>
    <td>
      {{tr}}{{$perm->object_class}}{{/tr}}
    </td>
    <td class="text">
      {{if $perm->object_id}}
        {{tr}}{{$perm->_ref_db_object->_view}}{{/tr}}
      {{else}}
        <strong>Droits généraux</strong>
      {{/if}}
    </td>
    <td class="button">
      <form name="editPermObj{{$perm->perm_object_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_perms_obj_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="perm_object_id" value="{{$perm->perm_object_id}}" />
      <select name="permission" {{if $typeVue != "user"}} disabled = "disabled"{{/if}}>
      {{foreach from=$permission|smarty:nodefaults key=key_perm item=curr_perm}}
        <option value="{{$key_perm}}" {{if $key_perm == $perm->permission}}selected="selected"{{/if}}>
          {{$curr_perm}}
        </option>
      {{/foreach}}
      </select>
      {{if $type == "user"}}
      <button class="modify" type="submit">Valider</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la permission sur',objName:'{{$perm->_ref_db_object->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
      {{/if}}
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>