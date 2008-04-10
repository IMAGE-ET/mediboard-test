<table class="form">
  <tr>
    <th style="width: 5%;">{{mb_title object=$product field=name}}</th>
    <td>{{mb_value object=$product field=name}}</td>
  </tr>
  <tr>
    <th>{{mb_title object=$product field=description}}</th>
    <td>{{mb_value object=$product field=description}}</td>
  </tr>
  <tr>
    <th>{{tr}}CProductStock-quantity{{/tr}}</th>
    <td>{{$product->_ref_stock_group->quantity}} {{include file="inc_bargraph.tpl" stock=$product->_ref_stock_group}}</td>
  </tr>
  <tr>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <td>{{$product->code}}</td>
  </tr>
</table>