<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("productToggle", { bStartVisible: false });
}
</script>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_stock" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="this.form.submit()">
          <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $curr_category->category_id == $category->category_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
      </form>
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0">
        Nouveau stock
      </a>

    {{if $category->category_id}}
    <h3>{{$category->_view}}</h3>
      <table class="tbl">
        <tr>
          <th>Groupe</th>
          <th>En stock</th>
          <th>Seuils</th>
        </tr>
        
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
        <tr id="product-{{$curr_product->_id}}-trigger">
          <td colspan="3">
            <a style="display: inline; float: right; font-weight: normal;" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id={{$curr_product->_id}}">
              Nouveau stock
            </a>
            {{$curr_product->_view}}
          </td>
        </tr>
        <tbody class="productToggle" id="product-{{$curr_product->_id}}">
        
        <!-- Stocks list of this Product -->
        {{foreach from=$curr_product->_ref_stocks item=curr_stock}}
          <tr {{if $curr_stock->_id == $stock->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Voir ou modifier le stock">{{$curr_stock->_ref_group->_view}}</a></td>
            <td>{{$curr_stock->quantity}}</td>
            <td>{{include file="inc_vw_bargraph.tpl" stock=$curr_stock}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="3">Aucun stock pour ce produit</td>
          </tr>
        {{/foreach}}
        </tbody>
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
      {{if !$stock->_id}}<input type="hidden" name="product_id" value="{{$stock->product_id}}" />{{/if}}
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
          <th>{{mb_label object=$stock field="group_id"}}</th>
          <td><select name="group_id" class="{{$stock->_props.group_id}}">
            <option value="">&mdash; Choisir un groupe</option>
            {{foreach from=$list_groups item=curr_group}}
              <option value="{{$curr_group->_id}}" {{if $stock->group_id == $curr_group->_id || ($curr_group->_id == $g && $stock->group_id != $curr_group->_id)}} selected="selected" {{/if}} >
              {{$curr_group->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="quantity"}}</th>
          <td>{{mb_field object=$stock field="quantity"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="product_id"}}</th>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$stock->_ref_product->_id}}" title="Voir ou modifier le produit">
              <b>{{$stock->_ref_product->_view}}</b>
            </a><br />
            {{$stock->_ref_product->description|nl2br}}
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