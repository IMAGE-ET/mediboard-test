{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
function pageMain() {
  filterFields = ["category_id", "societe_id", "keywords"];
  referencesFilter = new Filter("filter-references", "{{$m}}", "httpreq_vw_references_list", "list-references", filterFields);
  referencesFilter.submit();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-references" action="?" method="post" onsubmit="return referencesFilter.submit();">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="referencesFilter.submit();">
          <option value="0" >&mdash; Toutes les catégories &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="referencesFilter.submit();">
          <option value="0" >&mdash; Toutes les societés &mdash;</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $societe_id==$curr_societe->_id}}selected="selected"{{/if}}>{{$curr_societe->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        
        <button type="button" class="search" onclick="referencesFilter.submit();">Filtrer</button>
        <button type="button" class="cancel notext" onclick="referencesFilter.empty(['keywords', 'societe_id', 'category_id']);"></button>
      </form>

      <div id="list-references"></div>
    </td>


    <td class="halfPane">
      {{if $can->edit}}
      
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0">
        Nouvelle réference
      </a>
      
      <form name="edit_reference" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_reference_aed" />
	    <input type="hidden" name="reference_id" value="{{$reference->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $reference->_id}}
          <th class="title modify" colspan="2">Modification de la référence {{$reference->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une référence</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$reference->_props.societe_id}}">
            <option value="">&mdash; Choisir un Fournisseur</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->societe_id}}" {{if $reference->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="product_id"}}</th>
          <td class="readonly">
            <input type="hidden" name="product_id" value="{{$reference->product_id}}" class="{{$reference->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$reference->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">Chercher</button>
            <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_reference";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$reference->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="quantity"}}</th>
          <td>{{mb_field object=$reference field="quantity" increment=1 form=edit_reference}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="price"}}</th>
          <td>{{mb_field object=$reference field="price" increment=1 form=edit_reference}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $reference->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la référence',objName:'{{$reference->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>