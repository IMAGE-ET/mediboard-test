{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
getTypeValue = function(button){
  var checkbox = $(button).up().down('input[name=type]');
  return checkbox.checked ? checkbox.value : "";
}
</script>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CProduct field=name}}</th>
    <th>{{mb_title class=CProduct field=description}}</th>
    <th>{{tr}}CProductOrderItemReception-received_quantity{{/tr}}</th>
  </tr>
  <tr>
   <td>{{mb_value object=$product field=name}}</td>
   <td>{{mb_value object=$product field=description}}</td>
   <td>{{mb_value object=$product_order_item_reception field=quantity}}</td>
  </tr>
  <tr>
    <td colspan="10" style="text-align: center;">
      <button style="float: right;" type="button" class="trash" onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 1, getTypeValue(this))">
        Déstérilisé
      </button>
	    <button style="float: left;" type="button" class="submit" onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 0, getTypeValue(this))">
	      Poser
      </button>
      <label>
        <input type="checkbox" name="type" value="loan" {{if $product->_dmi_type == "loan"}}checked="checked"{{/if}} /> 
        {{tr}}CDMI.type.loan{{/tr}}
      </label>
	  </td>
  </tr>
</table>