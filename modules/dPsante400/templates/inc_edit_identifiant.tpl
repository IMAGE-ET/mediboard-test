<form name="editFrm" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;object_class={{$filter->object_class}}&amp;object_id={{$filter->object_id}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_idsante400_aed" />
<input type="hidden" name="id_sante400_id" value="{{$idSante400->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">

<tr>
  <th class="category" colspan="2">
  {{if $idSante400->_id}}
    {{mb_include module=system template=inc_object_history object=$idSante400}}
    {{tr}}CIdSante400-title-modify{{/tr}} &lsquo;{{$idSante400->_view}}&rsquo;
  {{else}}
    {{tr}}CIdSante400-title-create{{/tr}}
  {{/if}}
  </th>
  
  <tr>
    <td>{{mb_label object=$filter field="object_class"}}</td>
    <td>
      {{if $dialog && $target}}
      <input type="hidden" name="object_class" class="{{$filter->_props.object_class}}" value="{{$filter->object_class}}" />
      {{tr}}{{$filter->object_class}}{{/tr}}
      {{else}}
      <select name="object_class" class="{{$idSante400->_props.object_class}}">
        <option value="">&mdash; Choisir une classe</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $idSante400->object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
      {{/if}}
    </td>
  </tr>

  <tr>
    <td>{{mb_label object=$filter field="object_id"}}</td>
    <td>
	  {{if $dialog && $target}}
	  <input type="hidden" name="object_id" class="{{$filter->_props.object_id}}" value="{{$filter->object_id}}" />
      {{$target->_view}}
	  {{else}}
	    {{mb_field object=$idSante400 field=object_id}}
      <button class="search" type="button" onclick="ObjectSelector.initEdit()">Chercher</button>
      <script type="text/javascript">
        ObjectSelector.initEdit = function(){
          this.sForm     = "editFrm";
          this.sId       = "object_id";
          this.sClass    = "object_class";
          this.onlyclass = "false";
          this.pop();
        }
      </script>
      {{if $idSante400->_id}}
      <br />
      {{$idSante400->_ref_object->_view}}
      {{/if}}
      {{/if}}
    </td>
  </tr>

  <tr>
    <td>{{mb_label object=$idSante400 field="id400" }}</td>
    <td>{{mb_field object=$idSante400 field="id400" canNull="false"}}</td>
  </tr>

  <tr>
    <td>{{mb_label object=$idSante400 field="tag"}}</td>
    <td>{{mb_field object=$idSante400 field="tag" size="40"}}</td>
  </tr>

  <tr>
    <td>{{mb_label object=$idSante400 field="last_update"}}</td>
    <td>{{mb_field object=$idSante400 field="last_update" form="editFrm" canNull="false" register=true}} </td>
  </tr>

        
  <tr>
    <td class="button" colspan="2">
    {{if $idSante400->_id}}
      <button class="modify" type="submit">Valider</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {
        typeName: 'l\'identifiant',
        objName: '{{$idSante400->_view|smarty:nodefaults|JSAttribute}}'
      })">
        Supprimer
      </button>
    {{else}}
      <button class="submit" type="submit" name="btnFuseAction">Créer</button>
    {{/if}}
    </td>
  </tr>

</table>

</form>