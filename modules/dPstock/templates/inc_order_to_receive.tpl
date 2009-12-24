{{* $Id: inc_order.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=CMbObject_view object=$order}}

<table class="tbl">
  <tr>
    <th style="width: 50%;"></th>
    <th style="width: 0.1%; text-align: center;">{{mb_title class=CProductOrderItem field=quantity}}</th>
    <th style="width: 0.1%; text-align: center;">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th style="width: 0.1%; text-align: center;">{{mb_title class=CProductOrderItem field=_price}}</th>
    <th style="width: 0.1%; text-align: center;">Déjà reçu</th>
    <th style="width: 0.1%; text-align: right;"></th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_to_receive_item.tpl"}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
