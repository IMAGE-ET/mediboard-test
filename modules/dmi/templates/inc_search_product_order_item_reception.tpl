{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
	<tr>
		<th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
		<th>{{mb_title class=CProductOrderItemReception field=date}}</th>
		<th></th>
	</tr>
	{{foreach from=$list item=_order_item_reception}}	
	<tr>
	 <td>{{mb_value object=$_order_item_reception field=code}}</td>
   <td>{{mb_value object=$_order_item_reception field=lapsing_date}}</td>
	 <td>{{mb_value object=$_order_item_reception field=date}}</td>
	 <td>
       <button type="button" class="tick" onclick="return search_product_code('{{$_order_item_reception->_ref_order_item->_ref_reference->_ref_product->code}}','{{$_order_item_reception->code}}')">
         {{tr}}Select{{/tr}}
       </button>
	 </td>
  </tr>
	{{/foreach}}
	<tr>
	  <td id="product_reception_by_product" colspan="10"></td>
	</tr>
</table>