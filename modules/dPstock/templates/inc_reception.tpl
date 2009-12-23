{{* $Id: inc_order.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CProductOrderItem field=reference_id}}</th>
    <th>{{mb_title class=CProductOrderItem field=quantity}}</th>
    <th>{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th>{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  {{foreach from=$reception->_back.reception_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
      {{include file="inc_order_item.tpl"}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
