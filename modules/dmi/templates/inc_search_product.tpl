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
      <button style="float: right;" type="button" class="trash" onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 1)">
        Déstérilisé
      </button>
	    <button type="button" class="submit" onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 0)">
	      Poser
      </button>
	  </td>
  </tr>
</table>