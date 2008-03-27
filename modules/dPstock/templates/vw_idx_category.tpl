{{mb_include_script module="dPstock" script="numeric_field"}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id=0">
        Nouvelle catégorie
      </a>
      <table class="tbl">
        <tr>
          <th>Catégorie</th>
        </tr>
        {{foreach from=$list_categories item=curr_category}}
        <tr {{if $curr_category->_id == $category->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id={{$curr_category->_id}}" title="Modifier la catégorie">
              {{$curr_category->name}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="edit_category" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_aed" />
	  <input type="hidden" name="category_id" value="{{$category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->_id}}
          <th class="title modify" colspan="2">Modification de la catégorie {{$category->name}}</th>
          {{else}}
          <th class="title" colspan="2">Nouvelle catégorie</th>
          {{/if}}
        </tr> 
        <tr>
          <th>{{mb_label object=$category field="name"}}</th>
          <td>{{mb_field object=$category field="name"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $category->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$category->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>