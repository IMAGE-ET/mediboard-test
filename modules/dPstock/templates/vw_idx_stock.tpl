{{mb_include_script module="dPstock" script="product_selector"}}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      {{include file="inc_category_selector.tpl"}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0">
        Nouveau stock
      </a>

    {{if $category->category_id}}
    <h3>{{$category->_view}}</h3>
      <table class="tbl">
        <tr>
          <th>Produit</th>
          <th>En stock</th>
          <th>Seuils</th>
        </tr>
        
      <!-- Products list -->
      {{foreach from=$category->_ref_products item=curr_product}}
        {{if $curr_product->_ref_stock_group}}
          <tr {{if $curr_product->_ref_stock_group->_id == $stock->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_product->_ref_stock_group->_id}}" title="Voir ou modifier le stock">{{$curr_product->_view}}</a></td>
            <td>{{$curr_product->_ref_stock_group->quantity}}</td>
            <td>{{include file="inc_bargraph.tpl" stock=$curr_product->_ref_stock_group}}</td>
          </tr>
        {{else}}
          <tr>
            <td>{{$curr_product->_view}}</td>
            <td>Aucun stock pour ce produit</td>
            <td>
              <a style="display: inline; float: right; font-weight: normal;" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id={{$curr_product->_id}}">
                Nouveau stock
              </a>
            </td>
          </tr>
        {{/if}}
      {{foreachelse}}
        <tr>
          <td colspan="3">Aucun produit dans cette catégorie</td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
    </td>
    <!-- Edit/New Stock form -->
    <td class="halfPane">
      <form name="edit_stock" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="group_id" value="{{$g}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $stock->_id}}
          <th class="title modify" colspan="2">Modification du stock de {{$stock->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Nouveau stock</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="quantity"}}</th>
          <td>{{mb_field object=$stock field="quantity"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="product_id"}}</th>
          <td>
            <input type="hidden" name="product_id" value="{{$stock->product_id}}" class="{{$stock->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$stock->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">Chercher</button>
            <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_stock";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$stock->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_critical"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_critical"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_min"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_min"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_optimum"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_optimum"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_max"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_max"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $stock->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le stock',objName:'{{$stock->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>