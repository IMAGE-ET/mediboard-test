<table class="tbl">
  <tr>
    <th>id</th>
    <th>Nom</th>
  </tr>
  {{foreach from=$listCategories item=curr_cat}}
  <tr>
    <td>
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}" title="Modifier la catégorie">
        {{$curr_cat->ei_categorie_id}}
      </a>
    </td>
    <td class="text">
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}" title="Modifier la catégorie">
        {{$curr_cat->nom}}
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="2">
      Actuellement, il n'y a aucune catégorie.
    </td>
  </tr>
  {{/foreach}}
</table><br />

{{if $categorie->ei_categorie_id}}
<a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id=0">
  Créer une nouvelle Catégorie
</a>
{{/if}}
<form name="editCategorie" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_eiCategorie_aed" />
<input type="hidden" name="ei_categorie_id" value="{{$categorie->ei_categorie_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $categorie->ei_categorie_id}}
    <th colspan="2" class="category" style="color:#f00;">
      Modification de la catégorie : {{$categorie->nom}}
    {{else}}
    <th colspan="2" class="category">
      Création d'une nouvelle catégorie
    {{/if}}
    </th>
  </tr>
  <tr>
    <th>
      <label for="nom" title="Veuillez saisir un nom pour la catégorie">Nom de la catégorie</label>
    </th>
    <td>
      <input type="text" name="nom" value="{{$categorie->nom}}" title="{{$categorie->_props.nom}}" />
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">            
      {{if $categorie->ei_categorie_id}}
      <button class="modify" type="submit">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$categorie->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
      {{else}}
      <button class="submit" type="submit">Créer</button>
      {{/if}}
    </td>
  </tr>  
</table>
</form>