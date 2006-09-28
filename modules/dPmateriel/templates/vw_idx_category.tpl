<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id=0">
        Créer une nouvelle catégorie
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Catégorie</th>
        </tr>
        {{foreach from=$listCategory item=curr_category}}
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id={{$curr_category->category_id}}" title="Modifier la catégorie">
              {{$curr_category->category_id}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id={{$curr_category->category_id}}" title="Modifier le catégorie">
              {{$curr_category->category_name}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editCat" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_aed" />
	  <input type="hidden" name="category_id" value="{{$category->category_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->category_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification de la catégorie {{$category->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une fiche</th>
          {{/if}}
        </tr> 
        <tr>
          <th><label for="category_name" title="Nom de la catégorie, obligatoire">Catégorie</label></th>
          <td><input name="category_name" title="{{$category->_props.category_name}}" type="text" value="{{$category->category_name}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $category->category_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$category->_view|escape:"javascript"}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>