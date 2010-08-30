{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $product->_in_order}}
  <table class="tbl" style="display: none;">
    <tr>
      <th colspan="10">{{$product->_in_order|@count}} commandes en attente</th>
    </tr>
    <tr>
      <th>Commande</th>
      <th>Date</th>
      <th>Qté.</th>
    </tr>
    {{foreach from=$product->_in_order item=_item}}
      <tr>
        <td>{{$_item->_ref_order->order_number}}</td>
        <td>{{mb_value object=$_item->_ref_order field=date_ordered}}</td>
        <td>
          {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
            {{$_item->_unit_quantity}}
          {{else}}
            {{$_item->quantity}}
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
  </table>
  <img src="images/icons/order.png" onmouseover="ObjectTooltip.createDOM(this, $(this).previous(), {duration:0})" />
{{/if}}