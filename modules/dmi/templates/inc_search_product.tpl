<table class="tbl">
  <tr>
    <th>{{mb_title class=CProduct field=name}}</th>
    <th>{{mb_title class=CProduct field=description}}</th>
   <th>{{tr}}CProductOrderItemReception-received_quantity{{/tr}}</th>
 </tr>
 <tr>
   <td>{{mb_value object=$product field=name}}</td>
   <td>{{mb_value object=$product field=description}}</td>
   <td>{{$quantite_delivrable}}</td>
  </tr>
  <tr>
    <td colspan="10">
	    <button type="button" class="submit" onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}')">{{tr}}Save{{/tr}}</button>
	  </td>
  </tr>
</table>