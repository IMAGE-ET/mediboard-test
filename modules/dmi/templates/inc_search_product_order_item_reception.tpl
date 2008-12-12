{{* $Id: $ *}}
<script type="text/javascript">
search_product = function(code, code_lot)
{
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_do_search_product");
  url.addParam("code", code);
  url.addParam("code_lot", code_lot);
  url.requestUpdate("product_reception");
  return false;
}
</script>
<table class="tbl">
	<tr>
		<th>{{mb_title class=CProductOrderItemReception field=order_item_reception_id}}</th>
		<th>{{mb_title class=CProductOrderItemReception field=code}}</th>
		<th>{{mb_title class=CProductOrderItemReception field=date}}</th>
		<th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
		<th></th>
	</tr>
	{{foreach from=$list item=_order_item_reception}}	
	<tr>
	 <td>{{mb_value object=$_order_item_reception field=order_item_reception_id}}</td>
	 <td>{{mb_value object=$_order_item_reception field=code}}</td>
	 <td>{{mb_value object=$_order_item_reception field=date}}</td>
	 <td>{{mb_value object=$_order_item_reception field=lapsing_date}}</td>
	 <td>
		 <button type="button" class="tick" onclick="return search_product('{{$_order_item_reception->_ref_order_item->_ref_reference->_ref_product->code}}','{{$_order_item_reception->code}}')">Sélectionner</button>
	 </td>
  </tr>
	{{/foreach}}
	<tr>
	 <td id="product_reception" colspan="10"></td>
	</tr>
</table>