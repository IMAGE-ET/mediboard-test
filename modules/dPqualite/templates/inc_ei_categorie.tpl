{{if $categorie->ei_categorie_id}}
<a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id=0">
  {{tr}}CEiCategorie.create{{/tr}}
</a>
{{/if}}
<form name="editCategorie" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_eiCategorie_aed" />
<input type="hidden" name="ei_categorie_id" value="{{$categorie->ei_categorie_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $categorie->ei_categorie_id}}
    <th colspan="2" class="category modify">
      {{tr}}msg-CEiCategorie-title-modify{{/tr}} : {{$categorie->_view}}
    {{else}}
    <th colspan="2" class="category">
      {{tr}}msg-CEiCategorie-title-create{{/tr}}
    {{/if}}
    </th>
  </tr>
  <tr>
    <th>
      <label for="nom" title="{{tr}}CEiCategorie-nom-desc{{/tr}}">{{tr}}CEiCategorie-nom{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="nom" value="{{$categorie->nom}}" class="{{$categorie->_props.nom}}" />
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">            
      {{if $categorie->ei_categorie_id}}
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'{{tr escape="javascript"}}CEiCategorie.one{{/tr}}',objName:'{{$categorie->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>  
</table>
</form>
<br />
<table class="tbl">
  <tr>
    <th>{{tr}}CEiCategorie-nom-court{{/tr}}</th>
  </tr>
  {{foreach from=$listCategories item=curr_cat}}
  <tr>
    <td class="text">
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}" title="{{tr}}CEiCategorie.modify{{/tr}}">
        {{$curr_cat->nom}}
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="2">
      {{tr}}CEiCategorie.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>