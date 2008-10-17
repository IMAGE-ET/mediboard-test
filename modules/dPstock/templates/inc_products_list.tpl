<div id="list-products-total-count" style="display: none;">{{$list_products_count}}</div>

<table class="tbl">
  <tr>
    <th>{{tr}}CProduct-name{{/tr}}</th>
    <th>{{tr}}CProduct-societe_id{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProduct-_quantity{{/tr}}</th>
    <th>{{tr}}CProduct-packaging{{/tr}}</th>
  </tr>
  {{foreach from=$list_products item=curr_product}}
    <tr>
      <td title="{{$curr_product->name}}"><a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_product->_id}}">{{$curr_product->name|truncate:25}}</a></td>
      <td>{{$curr_product->_ref_societe->_view}}</td>
      <td>{{$curr_product->code}}</td>
      <td title="{{$curr_product->_quantity}}">{{$curr_product->_quantity|truncate:35}}</td>
      <td>{{$curr_product->packaging}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>