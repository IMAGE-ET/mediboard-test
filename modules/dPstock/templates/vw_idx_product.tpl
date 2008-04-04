{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
function pageMain() {
  filterFields = ["category_id", "societe_id", "keywords"];
  productsFilter = new Filter("filter-products", "{{$m}}", "httpreq_vw_products_list", "list-products", filterFields);
  productsFilter.submit();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-products" action="?" method="post" onsubmit="return productsFilter.submit();">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="productsFilter.submit();">
          <option value="0" >&mdash; Toutes les catégories &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="productsFilter.submit();">
          <option value="0" >&mdash; Toutes les fabricants &mdash;</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $societe_id==$curr_societe->_id}}selected="selected"{{/if}}>{{$curr_societe->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        
        <button type="button" class="search" onclick="productsFilter.submit();">Filtrer</button>
        <button type="button" class="cancel notext" onclick="productsFilter.empty('keywords');"></button>
      </form>

      <div id="list-products"></div>
    </td>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id=0">
        Nouveau produit
      </a>
      <form name="edit_product" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_product_aed" />
	  <input type="hidden" name="product_id" value="{{$product->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $product->_id}}
          <th class="title modify" colspan="2">Modification du produit {{$product->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une fiche produit</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$product field="name"}}</th>
          <td>{{mb_field object=$product field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="category_id"}}</th>
          <td><select name="category_id" class="{{$product->_props.category_id}}">
            <option value="">&mdash; Choisir une catégorie</option>
            {{foreach from=$list_categories item=curr_category}}
              <option value="{{$curr_category->_id}}" {{if $product->category_id == $curr_category->_id || $list_categories|@count==1}} selected="selected" {{/if}} >
              {{$curr_category->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$product->_props.societe_id}}">
            <option value="">&mdash; Choisir un fabricant</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->_id}}" {{if $product->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="code"}}</th>
          <td>{{mb_field object=$product field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="description"}}</th>
          <td>{{mb_field object=$product field="description"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $product->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le produit',objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
  {{if $product->_id}}
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">Stock(s) correspondant(s)</th>
        </tr>
        <tr>
          <th>Groupe</th>
          <th>En stock</th>
          <th>Seuils de Commande</th>
        </tr>
        {{foreach from=$product->_ref_stocks item=curr_stock}}
        <tr>
          <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Voir ou modifier le stock">{{$curr_stock->_ref_group->_view}}</a></td>
          <td>{{$curr_stock->quantity}}</td>
          <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="3">Aucun stock trouvé</td>
        </tr>
        {{/foreach}}
        {{if $product->_id}}
          <tr>
            <td colspan="3">
              <button class="new" type="button" onclick="window.location='?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id={{$product->_id}}'">
                Nouveau stock pour ce produit
              </button>
            </td>
          </tr>
        {{/if}}
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Référence(s) correspondante(s)</th>
        </tr>
        <tr>
           <th>Fournisseur</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {{foreach from=$product->_ref_references item=curr_reference}}
         <tr>
           <td>{{$curr_reference->_ref_societe->_view}}</td>
           <td>{{$curr_reference->quantity}}</td>
           <td>{{$curr_reference->price|string_format:"%.2f"}}</td>
           <td>{{$curr_reference->_unit_price|string_format:"%.2f"}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td colspan="4">Aucune référence trouvée</td>
         </tr>
         {{/foreach}}
         {{if $product->_id}}
          <tr>
            <td colspan="4">
              <button class="new" type="button" onclick="window.location='?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id={{$product->_id}}'">
                Nouvelle référence pour ce produit
              </button>
            </td>
          </tr>
        {{/if}}
       </table>
    
    </td>
  </tr>
  {{/if}}
</table>