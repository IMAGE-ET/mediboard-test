<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_category&amp;file_category_id=0">
        Créer une nouvelle catégorie de fichiers
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Catégorie</th>
          <th>Class</th>
        </tr>
        {{foreach from=$listCategory item=curr_category}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_category&amp;file_category_id={{$curr_category->file_category_id}}" title="Modifier la catégorie">
              {{$curr_category->file_category_id}}
            </a>
          </td>
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_category&amp;file_category_id={{$curr_category->file_category_id}}" title="Modifier le catégorie">
              {{$curr_category->nom}}
            </a>
          </td>
          <td class="text">
              {{tr}}{{$curr_category->class}}{{/tr}}
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editCat" action="?m={{$m}}&amp;tab=vw_category" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_filescategory_aed" />
	  	<input type="hidden" name="file_category_id" value="{{$category->file_category_id}}" />
      <input type="hidden" name="del" value="0" />

      <table class="form">
        <tr>
          {{if $category->file_category_id}}
          <th class="title modify" colspan="2">Modification de la catégorie {{$category->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une catégorie</th>
          {{/if}}
        </tr> 
        
        <tr>
          <th>{{mb_label object=$category field="nom"}}</th>
          <td>{{mb_field object=$category field="nom"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$category field="validation_auto"}}</th>
          <td>{{mb_field object=$category field="validation_auto"}}</td>
        </tr>

        <tr>
          <th><label for="class" title="Class de la catégorie">Class</label></th>
          <td>
            {{if $category->file_category_id}}
            {{tr}}{{$category->class}}{{/tr}}
            {{else}}
            <select name="class">
            <option value="">&mdash; Toutes</option>
            {{foreach from=$listClass|smarty:nodefaults item=curr_listClass}}
            <option value="{{$curr_listClass}}"{{if $category->class==$curr_listClass}} selected="selected"{{/if}}>{{tr}}{{$curr_listClass}}{{/tr}}</option>
            {{/foreach}}
            </select>
            {{/if}}
          </td>
        </tr>        
        <tr>
          <td class="button" colspan="2">
            {{if $category->file_category_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$category->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>